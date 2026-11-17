<?php
declare(strict_types=1);
namespace App\Core;
final class Validation {
    public static function email(string $value): bool { return strlen($value) <= 190 && filter_var($value, FILTER_VALIDATE_EMAIL) !== false; }
    public static function cep(string $value): bool { return preg_match('/^[0-9]{8}$/',$value) === 1; }
    public static function uf(string $value): bool { return preg_match('/^[A-Z]{2}$/',$value) === 1; }
    public static function positiveQuantity(mixed $value): ?int {
        $n = filter_var($value,FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>99]]);
        return $n === false ? null : $n;
    }
    public static function money(string $value): ?string {
        if (!preg_match('/^[0-9]{1,7}(?:\.[0-9]{1,2})?$/',$value)) return null;
        return number_format((float)$value,2,'.','');
    }
}
