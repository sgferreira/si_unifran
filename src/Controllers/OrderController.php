<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Auth;
use App\Models\Order;
final class OrderController {
    public function index(): void {
        $userId=Auth::requireLogin();
        \render('orders',['title'=>'Meus pedidos','orders'=>Order::forCustomer($userId)]);
    }
    public function detail(): void {
        $userId=Auth::requireLogin(); $orderId=(int)($_GET['id'] ?? 0);
        $order=Order::findForCustomer($orderId,$userId);
        if($order===null){
            http_response_code(404);
            \render('error',['title'=>'Pedido não encontrado','message'=>'Não encontramos esse pedido na sua conta.']);
            return;
        }
        \render('order_detail',[
            'title'=>'Pedido #'.$orderId,'order'=>$order,
            'items'=>Order::items($orderId),'history'=>Order::history($orderId)
        ]);
    }
}
