<?php
declare(strict_types=1);
namespace App\Core;
use PDO;
use PDOException;
use RuntimeException;
final class Database {
    private static ?PDO $connection = null;
    public static function connection(): PDO {
        if (self::$connection !== null) return self::$connection;
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $name = getenv('DB_NAME') ?: '';
        $user = getenv('DB_USER') ?: '';
        $pass = getenv('DB_PASS');
        if ($name === '' || $user === '' || $pass === false) throw new RuntimeException('Banco de dados não configurado. Consulte o README.');
        try {
            self::$connection = new PDO('mysql:host='.$host.';dbname='.$name.';charset=utf8mb4', $user, $pass, [
                PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES=>false
            ]);
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new RuntimeException('Não foi possível conectar ao banco de dados.');
        }
        return self::$connection;
    }
}
