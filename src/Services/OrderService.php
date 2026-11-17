<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Database;
use App\Core\OrderStatus;
use App\Core\Validation;
use RuntimeException;
use Throwable;

final class OrderService {
    public const DELIVERY_FEE_CENTS=800;
    public const FREE_DELIVERY_FROM_CENTS=8000;
    public static function deliveryFeeCents(int $subtotalCents):int {
        return $subtotalCents>=self::FREE_DELIVERY_FROM_CENTS?0:self::DELIVERY_FEE_CENTS;
    }
    public static function checkout(int $userId,array $address,string $paymentMethod,string $notes=''):int {
        if(!in_array($paymentMethod,['PIX','CARTAO_CREDITO','CARTAO_DEBITO'],true))
            throw new RuntimeException('Selecione uma forma de pagamento.');
        if(!is_string($address['cep']??null) || !is_string($address['uf']??null))
            throw new RuntimeException('CEP ou UF inválidos.');
        $cep=preg_replace('/\D/','',(string)$address['cep']);
        $uf=strtoupper(trim((string)$address['uf']));
        foreach(['logradouro'=>180,'numero'=>20,'bairro'=>100,'cidade'=>100] as $field=>$max){
            if(!is_string($address[$field]??null)) throw new RuntimeException('Preencha corretamente o endereço de entrega.');
            $value=trim((string)$address[$field]);
            if($value==='' || mb_strlen($value)>$max) throw new RuntimeException('Preencha corretamente o endereço de entrega.');
            $address[$field]=$value;
        }
        if(!Validation::cep($cep) || !Validation::uf($uf) || mb_strlen($notes)>500)
            throw new RuntimeException('CEP, UF ou observações inválidos.');
        if(!is_string($address['complemento']??'')) throw new RuntimeException('Complemento inválido.');
        $db=Database::connection();$db->beginTransaction();
        try{
            $stmt=$db->prepare('SELECT id FROM usuarios WHERE id=? FOR UPDATE');
            $stmt->execute([$userId]);
            if($stmt->fetchColumn()===false) throw new RuntimeException('Usuário não encontrado.');
            $stmt=$db->prepare("SELECT id FROM carrinhos WHERE usuario_id=? AND situacao='ABERTO' ORDER BY id DESC LIMIT 1 FOR UPDATE");
            $stmt->execute([$userId]);$cartId=$stmt->fetchColumn();
            if($cartId===false) throw new RuntimeException('Seu carrinho está vazio.');
            $stmt=$db->prepare('SELECT ci.produto_id,ci.quantidade,p.nome,p.preco,p.estoque,p.ativo FROM carrinho_itens ci JOIN produtos p ON p.id=ci.produto_id WHERE ci.carrinho_id=? ORDER BY ci.produto_id FOR UPDATE');
            $stmt->execute([$cartId]);$lines=$stmt->fetchAll();
            if(!$lines) throw new RuntimeException('Seu carrinho está vazio.');
            $subtotalCents=0;
            foreach($lines as $line){
                $qty=(int)$line['quantidade'];
                if(!$line['ativo'] || $qty<1 || $qty>(int)$line['estoque'])
                    throw new RuntimeException('Um dos produtos está sem estoque. Atualize seu carrinho.');
                $subtotalCents+=(int)round((float)$line['preco']*100)*$qty;
            }
            $feeCents=self::deliveryFeeCents($subtotalCents);
            $stmt=$db->prepare('INSERT INTO enderecos (usuario_id,cep,logradouro,numero,complemento,bairro,cidade,uf) VALUES (?,?,?,?,?,?,?,?)');
            $stmt->execute([$userId,$cep,$address['logradouro'],$address['numero'],mb_substr(trim((string)($address['complemento']??'')),0,100),$address['bairro'],$address['cidade'],$uf]);
            $addressId=(int)$db->lastInsertId();
            $stmt=$db->prepare('INSERT INTO pedidos (cliente_id,endereco_id,carrinho_id,subtotal,taxa_entrega,observacoes) VALUES (?,?,?,?,?,?)');
            $stmt->execute([$userId,$addressId,$cartId,self::decimal($subtotalCents),self::decimal($feeCents),$notes]);
            $orderId=(int)$db->lastInsertId();
            $insertItem=$db->prepare('INSERT INTO pedido_itens (pedido_id,produto_id,nome_produto,quantidade,preco_unitario) VALUES (?,?,?,?,?)');
            $reduceStock=$db->prepare('UPDATE produtos SET estoque=estoque-? WHERE id=? AND estoque>=?');
            foreach($lines as $line){
                $qty=(int)$line['quantidade'];
                $insertItem->execute([$orderId,$line['produto_id'],$line['nome'],$qty,$line['preco']]);
                $reduceStock->execute([$qty,$line['produto_id'],$qty]);
                if($reduceStock->rowCount()!==1) throw new RuntimeException('Estoque insuficiente. Tente novamente.');
            }
            // Somente simulação acadêmica: não executa cobrança.
            $stmt=$db->prepare("INSERT INTO pagamentos (pedido_id,metodo,status,valor,confirmado_em) VALUES (?,?,'APROVADO',?,NOW())");
            $stmt->execute([$orderId,$paymentMethod,self::decimal($subtotalCents+$feeCents)]);
            $db->prepare("UPDATE pedidos SET status='CONFIRMADO' WHERE id=?")->execute([$orderId]);
            $db->prepare('INSERT INTO historico_status_pedido (pedido_id,status_anterior,status_novo,alterado_por) VALUES (?,?,?,?)')
               ->execute([$orderId,'AGUARDANDO_PAGAMENTO','CONFIRMADO',$userId]);
            $db->prepare('INSERT INTO entregas (pedido_id) VALUES (?)')->execute([$orderId]);
            $db->prepare("UPDATE carrinhos SET situacao='CONVERTIDO' WHERE id=?")->execute([$cartId]);
            $db->commit();return $orderId;
        }catch(Throwable $ex){if($db->inTransaction())$db->rollBack();throw $ex;}
    }
    public static function advanceStatus(int $orderId,int $adminId):void {
        $db=Database::connection();$db->beginTransaction();
        try{
            $stmt=$db->prepare('SELECT status FROM pedidos WHERE id=? FOR UPDATE');
            $stmt->execute([$orderId]);$current=$stmt->fetchColumn();
            if($current===false) throw new RuntimeException('Pedido não encontrado.');
            $next=OrderStatus::next((string)$current);
            if($next===null) throw new RuntimeException('Este pedido não permite avançar o status.');
            $db->prepare('UPDATE pedidos SET status=? WHERE id=?')->execute([$next,$orderId]);
            $db->prepare('INSERT INTO historico_status_pedido (pedido_id,status_anterior,status_novo,alterado_por) VALUES (?,?,?,?)')
                ->execute([$orderId,$current,$next,$adminId]);
            if($next==='SAIU_PARA_ENTREGA')
                $db->prepare("UPDATE entregas SET status='EM_ROTA',saiu_em=NOW() WHERE pedido_id=?")->execute([$orderId]);
            elseif($next==='ENTREGUE')
                $db->prepare("UPDATE entregas SET status='ENTREGUE',entregue_em=NOW() WHERE pedido_id=?")->execute([$orderId]);
            $db->commit();
        }catch(Throwable $ex){if($db->inTransaction())$db->rollBack();throw $ex;}
    }
    private static function decimal(int $cents):string {return sprintf('%d.%02d',intdiv($cents,100),$cents%100);}
}
