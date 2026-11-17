<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/bootstrap.php';

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CatalogController;
use App\Controllers\CheckoutController;
use App\Controllers\OrderController;
use App\Core\Security;

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');

$method=$_SERVER['REQUEST_METHOD'] ?? 'GET';
$path=parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH) ?: '/';
$routes=[
 'GET /'=>[CatalogController::class,'index'],
 'GET /entrar'=>[AuthController::class,'loginForm'],
 'POST /entrar'=>[AuthController::class,'login'],
 'GET /cadastro'=>[AuthController::class,'registerForm'],
 'POST /cadastro'=>[AuthController::class,'register'],
 'POST /sair'=>[AuthController::class,'logout'],
 'GET /carrinho'=>[CartController::class,'index'],
 'POST /carrinho/adicionar'=>[CartController::class,'add'],
 'POST /carrinho/alterar'=>[CartController::class,'update'],
 'GET /finalizar'=>[CheckoutController::class,'form'],
 'POST /finalizar'=>[CheckoutController::class,'submit'],
 'GET /pedidos'=>[OrderController::class,'index'],
 'GET /pedido'=>[OrderController::class,'detail'],
 'GET /admin'=>[AdminController::class,'index'],
 'GET /admin/produtos'=>[AdminController::class,'products'],
 'POST /admin/produtos/salvar'=>[AdminController::class,'saveProduct'],
 'POST /admin/pedidos/avancar'=>[AdminController::class,'advanceOrder']
];
if($method==='POST') Security::verifyCsrf();
$handler=$routes[$method.' '.$path] ?? null;
if($handler===null){
 http_response_code(404);
 render('error',['title'=>'Página não encontrada','message'=>'O endereço solicitado não existe.']);
 exit;
}
try{
 [$class,$action]=$handler;
 (new $class())->$action();
}catch(Throwable $exception){
 error_log($exception->__toString());
 http_response_code(500);
 render('error',['title'=>'Não foi possível concluir','message'=>'Ocorreu um erro inesperado. Tente novamente em instantes.']);
}
