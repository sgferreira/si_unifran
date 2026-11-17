<?php
declare(strict_types=1);
namespace App\Core;
final class OrderStatus {
    public const LABELS = [
        'AGUARDANDO_PAGAMENTO'=>'Aguardando pagamento', 'CONFIRMADO'=>'Confirmado',
        'EM_PREPARO'=>'Em preparo', 'SAIU_PARA_ENTREGA'=>'Saiu para entrega',
        'ENTREGUE'=>'Entregue','CANCELADO'=>'Cancelado'
    ];
    private const NEXT = [
        'CONFIRMADO'=>'EM_PREPARO', 'EM_PREPARO'=>'SAIU_PARA_ENTREGA',
        'SAIU_PARA_ENTREGA'=>'ENTREGUE'
    ];
    public static function next(string $current): ?string { return self::NEXT[$current] ?? null; }
    public static function label(string $value): string { return self::LABELS[$value] ?? $value; }
}
