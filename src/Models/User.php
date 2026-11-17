<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
final class User {
    public static function byEmail(string $email): ?array {
        $stmt = Database::connection()->prepare('SELECT * FROM usuarios WHERE email=? AND ativo=1 LIMIT 1');
        $stmt->execute([$email]); return $stmt->fetch() ?: null;
    }
    public static function create(string $name,string $email,string $password): int {
        $stmt = Database::connection()->prepare("INSERT INTO usuarios (nome,email,senha_hash,perfil) VALUES (?,?,?,'CLIENTE')");
        $stmt->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
        return (int) Database::connection()->lastInsertId();
    }
}
