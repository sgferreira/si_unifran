<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Auth;
use App\Core\Database;
use App\Core\Validation;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use RuntimeException;
final class AdminController {
    public function index(): void {
        Auth::requireAdmin(); \render('admin/orders',['title'=>'Painel administrativo','orders'=>Order::latestForAdmin()]);
    }
    public function products(): void {
        Auth::requireAdmin();
        \render('admin/products',['title'=>'Gerenciar produtos','products'=>Product::allForAdmin(),'categories'=>Product::categories()]);
    }
    public function saveProduct(): void {
        Auth::requireAdmin();
        $id=(int)($_POST['id'] ?? 0);
        $name=trim((string)($_POST['nome'] ?? ''));
        $description=trim((string)($_POST['descricao'] ?? ''));
        $category=(int)($_POST['categoria_id'] ?? 0);
        $price=Validation::money((string)($_POST['preco'] ?? ''));
        $stock=filter_var($_POST['estoque'] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>0,'max_range'=>100000]]);
        $active=isset($_POST['ativo'])?1:0;
        if(mb_strlen($name)<3 || mb_strlen($name)>150 || mb_strlen($description)>2000 || $category<1 || $price===null || $stock===false){
            \flash('error','Dados do produto inválidos. Confira nome, categoria, preço e estoque.');
            \redirect('/admin/produtos');
        }
        $db=Database::connection();
        if($id>0){
            $stmt=$db->prepare('UPDATE produtos SET categoria_id=?,nome=?,descricao=?,preco=?,estoque=?,ativo=? WHERE id=?');
            $stmt->execute([$category,$name,$description,$price,$stock,$active,$id]);
            \flash('success','Produto atualizado.');
        }else{
            $stmt=$db->prepare('INSERT INTO produtos (categoria_id,nome,descricao,preco,estoque,ativo) VALUES (?,?,?,?,?,?)');
            $stmt->execute([$category,$name,$description,$price,$stock,$active]);
            \flash('success','Produto cadastrado.');
        }
        \redirect('/admin/produtos');
    }
    public function advanceOrder(): void {
        $adminId=Auth::requireAdmin(); $orderId=(int)($_POST['pedido_id'] ?? 0);
        try{
            if($orderId<1) throw new RuntimeException('Pedido inválido.');
            OrderService::advanceStatus($orderId,$adminId);
            \flash('success','Status do pedido atualizado.');
        }catch(RuntimeException $ex){ \flash('error',$ex->getMessage()); }
        \redirect('/admin');
    }
}
