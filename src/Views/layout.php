<?php
declare(strict_types=1);
use App\Core\Auth;
$user=Auth::user();
$currentPath=parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH) ?: '/';
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#7b153b">
<meta name="description" content="Cupcakes gourmet, carinho em cada detalhe. Conheça o cardápio Doce Instante.">
<title><?= e($title??'Doce Instante') ?> · Doce Instante</title>
<link rel="stylesheet" href="/assets/style.css">
<script src="/assets/app.js" defer></script>
</head>
<body>
<div class="announcement">✦ Feito com carinho, entregue com doçura ✦</div>
<header class="site-header"><div class="container header-inner">
  <a class="brand" href="/" aria-label="Doce Instante, página inicial">
    <span class="brand-mark" aria-hidden="true">🧁</span>
    <span class="brand-name">Doce <em>Instante</em><small>cupcakes gourmet</small></span>
  </a>
  <button class="nav-toggle" type="button" aria-controls="main-nav" aria-expanded="false" aria-label="Abrir menu"><span></span><span></span><span></span></button>
  <nav class="main-nav" id="main-nav" aria-label="Navegação principal">
    <a class="<?= $currentPath==='/'?'active':'' ?>" href="/">Cardápio</a>
    <?php if($user): ?>
      <a class="<?= str_starts_with($currentPath,'/pedido')?'active':'' ?>" href="/pedidos">Meus pedidos</a>
      <?php if($user['perfil']==='ADMIN'): ?><a href="/admin">Administração</a><?php endif; ?>
      <a class="cart-link" href="/carrinho">Meu carrinho <span aria-hidden="true">↗</span></a>
      <form action="/sair" method="post" class="inline-form"><?= csrf_input() ?><button class="nav-text-button" type="submit">Sair</button></form>
    <?php else: ?>
      <a href="/entrar">Entrar</a>
      <a class="nav-cta" href="/cadastro">Criar conta <span aria-hidden="true">↗</span></a>
    <?php endif; ?>
  </nav>
</div></header>
<main id="conteudo">
<?php $messages=take_flashes();if($messages): ?>
<div class="container flash-stack" role="status" aria-live="polite">
<?php foreach($messages as $message): ?><div class="flash flash-<?= e($message['kind']) ?>"><?= e($message['message']) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>
<?= $content ?>
</main>
<footer class="site-footer">
<div class="container footer-grid">
<div><div class="footer-brand">Doce <em>Instante</em><span> 🧁</span></div><p>Pequenos momentos, grandes sorrisos.<br>Uma pitada de carinho em cada pedido.</p></div>
<div><strong>Explore</strong><a href="/">Nosso cardápio</a><a href="/carrinho">Meu carrinho</a><a href="/pedidos">Acompanhar pedidos</a></div>
<div><strong>Projeto acadêmico</strong><p>Protótipo funcional para o PIT-II de Sistemas de Informação · UNIFRAN. Pedidos e pagamentos são simulados.</p></div>
</div>
<div class="container footer-bottom"><span>© <?= date('Y') ?> Doce Instante</span><span>Feito para aprender, testar e evoluir.</span></div>
</footer>
</body>
</html>
