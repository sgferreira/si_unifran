<?php
declare(strict_types=1);
use App\Core\Security;

function e(mixed $value): string { return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function brl(float|string|int $value): string { return 'R$ ' . number_format((float) $value, 2, ',', '.'); }
function render(string $template, array $data = []): void {
    $file = __DIR__ . '/Views/' . $template . '.php';
    if (!is_file($file)) throw new RuntimeException('Visualização não encontrada.');
    extract($data, EXTR_SKIP);
    ob_start(); require $file; $content = ob_get_clean();
    require __DIR__ . '/Views/layout.php';
}
function redirect(string $path): never { header('Location: ' . $path, true, 303); exit; }
function flash(string $kind, string $message): void { $_SESSION['flash'][] = ['kind' => $kind, 'message' => $message]; }
function take_flashes(): array { $flashes = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $flashes; }
function csrf_input(): string { return '<input type="hidden" name="_csrf" value="' . e(Security::csrfToken()) . '">'; }
