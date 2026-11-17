<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Auth;
use App\Core\Validation;
use App\Services\CartService;
use RuntimeException;
final class CartController {
    public function index(): void {
        $userId=Auth::requireLogin(); $lines=CartService::lines($userId);
        \render('cart',['title'=>'Meu carrinho','lines'=>$lines,'subtotal'=>CartService::total($lines)]);
    }
    public function add(): void {
        $userId=Auth::requireLogin(); $productId=(int)($_POST['produto_id'] ?? 0);
        $quantity=Validation::positiveQuantity($_POST['quantidade'] ?? null);
        try{
            if($quantity===null) throw new RuntimeException('Informe uma quantidade válida.');
            CartService::add($userId,$productId,$quantity);
            \flash('success','Doçura adicionada ao seu carrinho!');
        }catch(RuntimeException $ex){ \flash('error',$ex->getMessage()); }
        \redirect('/carrinho');
    }
    public function update(): void {
        $userId=Auth::requireLogin(); $productId=(int)($_POST['produto_id'] ?? 0);
        $qty=filter_var($_POST['quantidade'] ?? null,FILTER_VALIDATE_INT);
        try{
            if($qty===false || $qty===null || $qty<0) throw new RuntimeException('Quantidade inválida.');
            CartService::update($userId,$productId,$qty);
            \flash('success','Carrinho atualizado.');
        }catch(RuntimeException $ex){ \flash('error',$ex->getMessage()); }
        \redirect('/carrinho');
    }
}
