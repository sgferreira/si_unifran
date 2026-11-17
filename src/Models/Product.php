<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
final class Product {
    public static function categories(): array { return Database::connection()->query('SELECT id,nome FROM categorias WHERE ativa=1 ORDER BY nome')->fetchAll(); }
    public static function catalog(string $search='',int $categoryId=0): array {
        $sql = 'SELECT p.*,c.nome AS categoria FROM produtos p JOIN categorias c ON c.id=p.categoria_id WHERE p.ativo=1 AND c.ativa=1';
        $params=[];
        if ($search !== '') { $sql.=' AND (p.nome LIKE ? OR p.descricao LIKE ?)'; $params[]='%'.$search.'%'; $params[]='%'.$search.'%'; }
        if ($categoryId>0) { $sql.=' AND p.categoria_id=?'; $params[]=$categoryId; }
        $stmt=Database::connection()->prepare($sql.' ORDER BY p.nome');
        $stmt->execute($params); return $stmt->fetchAll();
    }
    public static function active(int $id): ?array {
        $stmt=Database::connection()->prepare('SELECT * FROM produtos WHERE id=? AND ativo=1');
        $stmt->execute([$id]); return $stmt->fetch() ?: null;
    }
    public static function allForAdmin(): array {
        return Database::connection()->query('SELECT p.*,c.nome AS categoria FROM produtos p JOIN categorias c ON c.id=p.categoria_id ORDER BY p.id DESC')->fetchAll();
    }
}
