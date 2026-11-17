<?php
declare(strict_types=1);
namespace App\Core;
final class Security {
    public static function csrfToken(): string {
        if (!isset($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }
    public static function verifyCsrf(): void {
        $submitted = $_POST['_csrf'] ?? '';
        if (!is_string($submitted) || !hash_equals(self::csrfToken(), $submitted)) {
            http_response_code(419);
            \render('error',['title'=>'Sessão expirada','message'=>'Atualize a página e envie novamente o formulário.']);
            exit;
        }
    }
}
