<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit;}
require dirname(__DIR__).'/src/bootstrap.php';
use App\Core\OrderStatus;
use App\Core\Validation;
use App\Services\OrderService;
$total=0;
function check(bool $condition,string $name):void {
    global $total;
    if(!$condition) throw new RuntimeException('FALHOU: '.$name);
    $total++;echo 'OK: '.$name.PHP_EOL;
}
check(Validation::email('sergio@example.com'),'E-mail válido');
check(!Validation::email('email-invalido'),'E-mail inválido');
check(Validation::cep('14400000'),'CEP com oito dígitos');
check(!Validation::cep('14400-000'),'CEP com pontuação rejeitado');
check(Validation::uf('SP') && !Validation::uf('S1'),'UF validada');
check(Validation::positiveQuantity('2')===2,'Quantidade positiva');
check(Validation::positiveQuantity('0')===null,'Quantidade zero rejeitada');
check(Validation::money('19.90')==='19.90','Preço decimal normalizado');
check(Validation::money('-1.00')===null,'Preço negativo rejeitado');
check(OrderService::deliveryFeeCents(7999)===800,'Entrega abaixo do limite');
check(OrderService::deliveryFeeCents(8000)===0,'Entrega grátis no limite');
check(OrderStatus::next('CONFIRMADO')==='EM_PREPARO','Transição confirmação/preparo');
check(OrderStatus::next('EM_PREPARO')==='SAIU_PARA_ENTREGA','Transição preparo/entrega');
check(OrderStatus::next('SAIU_PARA_ENTREGA')==='ENTREGUE','Transição entrega/conclusão');
check(OrderStatus::next('ENTREGUE')===null,'Pedido entregue não avança');
check(OrderStatus::next('CANCELADO')===null,'Pedido cancelado não avança');
echo PHP_EOL.$total.' testes concluídos com sucesso.'.PHP_EOL;
