<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
final class Order {
    public static function forCustomer(int $customerId): array {
        $stmt=Database::connection()->prepare('SELECT p.*,pg.metodo FROM pedidos p LEFT JOIN pagamentos pg ON pg.pedido_id=p.id WHERE p.cliente_id=? ORDER BY p.criado_em DESC,p.id DESC');
        $stmt->execute([$customerId]); return $stmt->fetchAll();
    }
    public static function findForCustomer(int $orderId,int $customerId): ?array {
        $stmt=Database::connection()->prepare('SELECT p.*,e.logradouro,e.numero,e.bairro,e.cidade,e.uf,pg.metodo FROM pedidos p JOIN enderecos e ON e.id=p.endereco_id LEFT JOIN pagamentos pg ON pg.pedido_id=p.id WHERE p.id=? AND p.cliente_id=?');
        $stmt->execute([$orderId,$customerId]); return $stmt->fetch() ?: null;
    }
    public static function items(int $orderId): array {
        $stmt=Database::connection()->prepare('SELECT * FROM pedido_itens WHERE pedido_id=? ORDER BY id');
        $stmt->execute([$orderId]); return $stmt->fetchAll();
    }
    public static function history(int $orderId): array {
        $stmt=Database::connection()->prepare('SELECT * FROM historico_status_pedido WHERE pedido_id=? ORDER BY id');
        $stmt->execute([$orderId]); return $stmt->fetchAll();
    }
    public static function latestForAdmin(): array {
        return Database::connection()->query('SELECT p.*,u.nome AS cliente FROM pedidos p JOIN usuarios u ON u.id=p.cliente_id ORDER BY p.criado_em DESC,p.id DESC LIMIT 100')->fetchAll();
    }
}
