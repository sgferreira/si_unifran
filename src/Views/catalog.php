<?php declare(strict_types=1); ?>
<section class="hero">
<div class="container hero-grid">
<div class="hero-copy">
  <div class="eyebrow">✳ DOCES MOMENTOS, MEMÓRIAS FELIZES</div>
  <h1>Um instante <span>doce</span> muda o dia inteiro.</h1>
  <p>Cupcakes gourmet feitos com afeto, ingredientes selecionados e aquele toque especial que transforma qualquer pausa em celebração.</p>
  <div class="hero-actions"><a href="#cardapio" class="button button-primary">Explorar o cardápio <span aria-hidden="true">→</span></a><span class="hero-note">✦ Feitos especialmente para você</span></div>
  <div class="hero-trust">♡ <div><strong>Receitas artesanais</strong><small>Uma experiência em cada mordida</small></div></div>
</div>
<div class="hero-art" aria-hidden="true">
  <span class="art-ring ring-one"></span><span class="art-ring ring-two"></span><span class="floating-sparkle sp-one">✳</span><span class="floating-sparkle sp-two">✦</span>
  <div class="cupcake-canvas"><div class="frosting frosting-top"></div><div class="frosting frosting-middle"></div><div class="frosting frosting-bottom"></div><div class="cherry"></div><div class="cake-wrapper"><i></i><i></i><i></i><i></i><i></i></div></div>
  <div class="hero-sticker"><span>feito com</span><strong>♥ amor</strong></div>
</div>
</div>
</section>
<section class="benefits" aria-label="Nossos diferenciais"><div class="container benefits-grid"><span>✳ <strong>Artesanal</strong> de verdade</span><span>♡ Ingredientes <strong>selecionados</strong></span><span>✦ Um carinho em <strong>cada detalhe</strong></span></div></section>
<section class="catalog-section container" id="cardapio">
<div class="section-intro"><div><span class="eyebrow">NOSSA VITRINE DE DELÍCIAS</span><h2>Escolha seu próximo <em>favorito</em></h2><p>Sabores especiais para adoçar qualquer ocasião.</p></div><span class="section-doodle" aria-hidden="true">♡</span></div>
<form class="catalog-filters" action="/" method="get" role="search">
  <label class="search-field"><span aria-hidden="true">⌕</span><span class="sr-only">Buscar produto</span><input name="q" type="search" value="<?= e($search) ?>" placeholder="Busque seu sabor favorito…" maxlength="100"></label>
  <label class="select-field"><span class="sr-only">Filtrar categoria</span><select name="categoria"><option value="0">Todas as categorias</option><?php foreach($categories as $category): ?><option value="<?= e($category['id']) ?>" <?= (int)$category['id']===$categoryId?'selected':'' ?>><?= e($category['nome']) ?></option><?php endforeach; ?></select></label>
  <button class="button button-dark" type="submit">Buscar</button>
</form>
<?php if(!$products): ?>
<div class="empty-state"><span aria-hidden="true">🍰</span><h3>Nenhuma delícia encontrada</h3><p>Experimente outro nome ou categoria.</p><a class="button button-primary" href="/">Ver todos os produtos</a></div>
<?php else: ?>
<div class="product-grid">
<?php foreach($products as $index=>$product): ?>
<article class="product-card">
  <div class="product-visual visual-<?= ($index%6)+1 ?>">
    <span class="product-illustration" aria-hidden="true"><?= $product['categoria']==='Bebidas'?'☕':($product['categoria']==='Combos'?'🎁':'🧁') ?></span>
    <span class="visual-glow"></span><span class="product-category"><?= e($product['categoria']) ?></span>
  </div>
  <div class="product-info"><h3><?= e($product['nome']) ?></h3><p><?= e($product['descricao']) ?></p>
    <div class="product-bottom"><div><strong class="price"><?= brl($product['preco']) ?></strong><small><?= (int)$product['estoque']>0?'Disponível':'Esgotado' ?></small></div>
    <?php if((int)$product['estoque']>0): ?>
      <form method="post" action="/carrinho/adicionar"><?= csrf_input() ?><input type="hidden" name="produto_id" value="<?= e($product['id']) ?>"><input type="hidden" name="quantidade" value="1"><button class="add-button" type="submit" aria-label="Adicionar <?= e($product['nome']) ?> ao carrinho">+</button></form>
    <?php else: ?><button class="add-button disabled" type="button" disabled aria-label="Produto esgotado">×</button><?php endif; ?>
    </div>
  </div>
</article>
<?php endforeach; ?>
</div>
<?php endif; ?>
</section>
<section class="love-note"><div class="container love-note-inner"><span aria-hidden="true">♡</span><h2>O melhor ingrediente é o <em>carinho.</em></h2><p>Um doce para celebrar, presentear ou simplesmente tornar hoje mais especial.</p><a class="button button-light" href="#cardapio">Escolher meu cupcake →</a></div></section>
