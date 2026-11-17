<?php
declare(strict_types=1);
namespace App\Core;
final class Auth {
    public static function id(): ?int { return isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null; }
    public static function user(): ?array { return $_SESSION['user'] ?? null; }
    public static function login(array $user): void {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>(int)$user['id'],'nome'=>$user['nome'],'email'=>$user['email'],'perfil'=>$user['perfil']];
    }
    public static function logout(): void { unset($_SESSION['user']); session_regenerate_id(true); }
    public static function requireLogin(): int {
        $id = self::id();
        if ($id === null) { \flash('info','Entre na sua conta para continuar.'); \redirect('/entrar'); }
        return $id;
    }
    public static function requireAdmin(): int {
        $id = self::requireLogin();
        if ((self::user()['perfil'] ?? '') !== 'ADMIN') {
            http_response_code(403);
            \render('error',['title'=>'Acesso negado','message'=>'Esta área é reservada à administração.']);
            exit;
        }
        return $id;
    }
}
