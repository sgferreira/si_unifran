<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\Database;
use App\Core\Validation;
use RuntimeException;
use Throwable;
final class CartService {
    public static function openCartId(int $userId): ?int {
        $stmt=Database::connection()->prepare("SELECT id FROM carrinhos WHERE usuario_id=? AND situacao='ABERTO' ORDER BY id DESC LIMIT 1");
        $stmt->execute([$userId]); $id=$stmt->fetchColumn();
        return $id===false?null:(int)$id;
    }
    private static function ensureCartId(int $userId): int {
        $id=self::openCartId($userId);
        if($id!==null) return $id;
        $db=Database::connection();
        $stmt=$db->prepare('INSERT INTO carrinhos (usuario_id) VALUES (?)');
        $stmt->execute([$userId]); return (int)$db->lastInsertId();
    }
    public static function lines(int $userId): array {
        $cartId=self::openCartId($userId);
        if($cartId===null) return [];
        $stmt=Database::connection()->prepare('SELECT ci.produto_id,ci.quantidade,p.nome,p.preco,p.estoque,p.ativo,(ci.quantidade*p.preco) AS total_linha FROM carrinho_itens ci JOIN produtos p ON p.id=ci.produto_id WHERE ci.carrinho_id=? ORDER BY p.nome');
        $stmt->execute([$cartId]); return $stmt->fetchAll();
    }
    public static function total(array $lines): float {
        return array_reduce($lines,static fn(float $sum,array $row):float=>$sum+(float)$row['preco']*(int)$row['quantidade'],0.0);
    }
    public static function add(int $userId,int $productId,int $quantity): void {
        if(Validation::positiveQuantity($quantity)===null || $productId<1) throw new RuntimeException('Informe uma quantidade válida.');
        $db=Database::connection(); $db->beginTransaction();
        try{
            $stmt=$db->prepare('SELECT id FROM usuarios WHERE id=? FOR UPDATE'); $stmt->execute([$userId]);
            if($stmt->fetchColumn()===false) throw new RuntimeException('Usuário não encontrado.');
            $stmt=$db->prepare('SELECT estoque FROM produtos WHERE id=? AND ativo=1 FOR UPDATE');
            $stmt->execute([$productId]); $product=$stmt->fetch();
            if(!$product) throw new RuntimeException('Produto indisponível.');
            $cartId=self::ensureCartId($userId);
            $stmt=$db->prepare('SELECT quantidade FROM carrinho_itens WHERE carrinho_id=? AND produto_id=? FOR UPDATE');
            $stmt->execute([$cartId,$productId]); $current=(int)($stmt->fetchColumn() ?: 0);
            if($current+$quantity>(int)$product['estoque'] || $current+$quantity>99)
                throw new RuntimeException('Quantidade superior ao estoque disponível.');
            $stmt=$db->prepare('INSERT INTO carrinho_itens (carrinho_id,produto_id,quantidade) VALUES (?,?,?) ON DUPLICATE KEY UPDATE quantidade=quantidade+VALUES(quantidade)');
            $stmt->execute([$cartId,$productId,$quantity]); $db->commit();
        }catch(Throwable $ex){if($db->inTransaction())$db->rollBack();throw $ex;}
    }
    public static function update(int $userId,int $productId,int $quantity): void {
        $cartId=self::openCartId($userId);
        if($cartId===null || $productId<1) throw new RuntimeException('Item não encontrado.');
        $db=Database::connection();
        if($quantity<=0){
            $stmt=$db->prepare('DELETE FROM carrinho_itens WHERE carrinho_id=? AND produto_id=?');
            $stmt->execute([$cartId,$productId]);return;
        }
        if(Validation::positiveQuantity($quantity)===null) throw new RuntimeException('Informe uma quantidade entre 1 e 99.');
        $stmt=$db->prepare('SELECT estoque FROM produtos WHERE id=? AND ativo=1');
        $stmt->execute([$productId]); $stock=$stmt->fetchColumn();
        if($stock===false || $quantity>(int)$stock) throw new RuntimeException('Quantidade superior ao estoque disponível.');
        $stmt=$db->prepare('UPDATE carrinho_itens SET quantidade=? WHERE carrinho_id=? AND produto_id=?');
        $stmt->execute([$quantity,$cartId,$productId]);
    }
}
