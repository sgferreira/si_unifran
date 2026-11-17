<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Auth;
use App\Services\CartService;
use App\Services\OrderService;
use RuntimeException;
final class CheckoutController {
    public function form(): void {
        $userId=Auth::requireLogin(); $lines=CartService::lines($userId);
        if(!$lines){ \flash('info','Adicione produtos antes de finalizar o pedido.'); \redirect('/carrinho'); }
        $subtotal=CartService::total($lines);
        $fee=OrderService::deliveryFeeCents((int)round($subtotal*100))/100;
        \render('checkout',['title'=>'Finalizar pedido','lines'=>$lines,'subtotal'=>$subtotal,'fee'=>$fee]);
    }
    public function submit(): void {
        $userId=Auth::requireLogin();
        try{
            $address=[];
            foreach(['cep','logradouro','numero','complemento','bairro','cidade','uf'] as $field)
                $address[$field]=$_POST[$field] ?? '';
            $orderId=OrderService::checkout($userId,$address,(string)($_POST['metodo'] ?? ''),trim((string)($_POST['observacoes'] ?? '')));
            \flash('success','Pedido #'.$orderId.' confirmado! Pagamento simulado com sucesso.');
            \redirect('/pedido?id='.$orderId);
        }catch(RuntimeException $ex){ \flash('error',$ex->getMessage()); \redirect('/finalizar'); }
    }
}
