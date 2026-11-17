<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Auth;
use App\Core\Validation;
use App\Models\User;
use PDOException;
final class AuthController {
    public function loginForm(): void { \render('auth/login',['title'=>'Entrar na conta']); }
    public function login(): void {
        $email=strtolower(trim((string)($_POST['email'] ?? '')));
        $password=(string)($_POST['senha'] ?? '');
        $now=time();
        $_SESSION['failed_logins']=array_values(array_filter($_SESSION['failed_logins'] ?? [],static fn(int $t):bool=>$t>$now-900));
        if(count($_SESSION['failed_logins'])>=5){
            \flash('error','Muitas tentativas. Aguarde 15 minutos antes de tentar novamente.');
            \redirect('/entrar');
        }
        $user=User::byEmail($email);
        if(!$user || !password_verify($password,$user['senha_hash'])){
            $_SESSION['failed_logins'][]=$now;
            \flash('error','E-mail ou senha incorretos.');
            \redirect('/entrar');
        }
        unset($_SESSION['failed_logins']);
        Auth::login($user);
        \flash('success','Que bom ter você por aqui, '.$user['nome'].'!');
        \redirect($user['perfil']==='ADMIN'?'/admin':'/');
    }
    public function registerForm(): void { \render('auth/register',['title'=>'Criar conta']); }
    public function register(): void {
        $name=trim((string)($_POST['nome'] ?? ''));
        $email=strtolower(trim((string)($_POST['email'] ?? '')));
        $password=(string)($_POST['senha'] ?? '');
        $confirmation=(string)($_POST['confirmar'] ?? '');
        if(mb_strlen($name)<3 || mb_strlen($name)>150 || !Validation::email($email)){
            \flash('error','Informe um nome e e-mail válidos.'); \redirect('/cadastro');
        }
        if(strlen($password)<8 || strlen($password)>72 || $password!==$confirmation){
            \flash('error','Use uma senha de 8 a 72 caracteres e confirme-a corretamente.'); \redirect('/cadastro');
        }
        try{ User::create($name,$email,$password); }
        catch(PDOException $ex){
            if($ex->getCode()!=='23000') throw $ex;
            \flash('error','Não foi possível cadastrar este e-mail. Verifique se ele já está em uso.');
            \redirect('/cadastro');
        }
        $user=User::byEmail($email);
        if(!$user) throw new \RuntimeException('Conta não encontrada após cadastro.');
        Auth::login($user);
        \flash('success','Sua conta foi criada! Escolha seu cupcake favorito.');
        \redirect('/');
    }
    public function logout(): void {
        Auth::logout(); \flash('success','Você saiu da sua conta com segurança.'); \redirect('/');
    }
}
