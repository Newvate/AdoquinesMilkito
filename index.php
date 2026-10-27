<?php session_start(); require __DIR__ . '/config.php';
$flavors = [];
try {
    $flavors = db()->query('SELECT * FROM flavors WHERE active=1 ORDER BY id')->fetchAll();
    if (!$flavors) {
        $seed = db()->prepare('INSERT INTO flavors (name, description, emoji) VALUES (?, ?, ?)');
        foreach ([
            ['Lúcuma', 'Cremoso, peruano y con ese toque de caramelo.', '🍨'],
            ['Chocolate', 'Intenso, suave y perfecto para cualquier momento.', '🍫'],
            ['Fresa', 'Frutal, fresco y hecho para sonreír.', '🍓'],
            ['Maracuyá', 'Ácido, tropical y muy refrescante.', '🥭'],
            ['Vainilla', 'Clásico, aromático y deliciosamente cremoso.', '🍦'],
            ['Menta choco', 'Refrescante con trocitos de chocolate.', '🌿']
        ] as $flavor) $seed->execute($flavor);
        $flavors = db()->query('SELECT * FROM flavors WHERE active=1 ORDER BY id')->fetchAll();
    }
} catch (Throwable $ex) {
    $setupError = true;
    // Catálogo visual de respaldo: la interfaz puede mostrarse aunque MySQL aún no esté configurado.
    $flavors = [
        ['id'=>1, 'name'=>'Lúcuma', 'description'=>'Cremoso, peruano y con ese toque de caramelo.', 'emoji'=>'🍨'],
        ['id'=>2, 'name'=>'Chocolate', 'description'=>'Intenso, suave y perfecto para cualquier momento.', 'emoji'=>'🍫'],
        ['id'=>3, 'name'=>'Fresa', 'description'=>'Frutal, fresco y hecho para sonreír.', 'emoji'=>'🍓'],
        ['id'=>4, 'name'=>'Maracuyá', 'description'=>'Ácido, tropical y muy refrescante.', 'emoji'=>'🥭'],
        ['id'=>5, 'name'=>'Vainilla', 'description'=>'Clásico, aromático y deliciosamente cremoso.', 'emoji'=>'🍦'],
        ['id'=>6, 'name'=>'Menta choco', 'description'=>'Refrescante con trocitos de chocolate.', 'emoji'=>'🌿']
    ];
}
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Adoquines Milkito | Delivery Huánuco</title><link rel="stylesheet" href="assets/style.css"></head><body>
<header class="topbar"><a class="brand" href="index.php"><span class="brand-mark">M</span><span>adoquines <b>milkito</b></span></a><nav><a href="#sabores">Sabores</a><a href="#como">Cómo funciona</a><a class="admin-link" href="admin.php">Panel de pedidos ↗</a></nav></header>
<main><section class="hero"><div class="hero-copy"><p class="eyebrow">HELADOS ARTESANALES · HUÁNUCO</p><h1>Un adoquín de felicidad, <em>hasta tu puerta.</em></h1><p class="lead">Personaliza tu helado peruano favorito y recíbelo fresco en cualquier rincón de Huánuco.</p><a class="btn btn-dark" href="#pedido">Arma tu pedido <span>↓</span></a><div class="trust"><span>✦</span><span><b>Delivery rápido</b><small>En toda la ciudad</small></span><span>✦</span><span><b>Hecho con cariño</b><small>Ingredientes locales</small></span></div></div><div class="hero-art"><div class="sun"></div><div class="scoops"><div class="scoop scoop-pink">🍓</div><div class="scoop scoop-yellow">🍨</div><div class="scoop scoop-brown">🍫</div></div><div class="waffle">adoquín<br><small>hecho en Huánuco</small></div></div></section>
<section id="sabores" class="section"><div class="section-title"><p class="eyebrow">PARA TODOS LOS ANTOJOS</p><h2>Elige tu sabor.</h2><p>Recetas cremosas con identidad peruana.</p></div><div class="flavor-grid"><?php foreach ($flavors as $f): ?><article class="flavor-card"><div class="flavor-icon"><?=e($f['emoji'])?></div><h3><?=e($f['name'])?></h3><p><?=e($f['description'])?></p></article><?php endforeach; ?></div></section>
<section id="pedido" class="order-section"><div class="order-intro"><p class="eyebrow">TU MOMENTO MILKITO</p><h2>Diseña tu adoquín.</h2><p>Hazlo a tu manera: elige el tamaño, tu sabor favorito y cuántos quieres recibir.</p><div class="mini-note">📍 Cambia el sabor y mira cómo cambia tu adoquín.</div><div class="live-preview"><div class="adoquin-preview flavor-lucuma" id="adoquinPreview"><span class="preview-sprinkle s1"></span><span class="preview-sprinkle s2"></span><span class="preview-sprinkle s3"></span></div><div><b id="previewText">1 × Grande de Lúcuma</b><small id="previewFlavor">Vaso de lúcuma</small></div></div></div><form class="order-form builder" action="order.php" method="post" id="orderForm"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="size" id="size" value="Grande"><input type="hidden" name="flavor_id" id="flavor_id" value="<?=e((string)($flavors[0]['id']??''))?>"><input type="hidden" name="quantity" id="quantity" value="1"><fieldset><legend>1. Escoge el tamaño</legend><div class="choice-grid size-choices"><button type="button" class="choice active" data-size="Grande" data-price="3.5">🥤 <b>Vaso grande</b><small>S/ 3.50</small></button><button type="button" class="choice" data-size="Chico" data-price="1.8">🥤 <b>Vaso chico</b><small>S/ 1.80</small></button></div></fieldset><fieldset><legend>2. Elige tu sabor</legend><div class="choice-grid"><?php foreach ($flavors as $i=>$f): ?><button type="button" class="choice flavor-choice <?=$i===0?'active':''?>" data-flavor="<?=e((string)$f['id'])?>" data-name="<?=e($f['name'])?>" data-emoji="<?=e($f['emoji'])?>"><?=e($f['emoji'])?> <b><?=e($f['name'])?></b></button><?php endforeach; ?></div></fieldset><fieldset><legend>3. ¿Cuántos quieres?</legend><div class="quantity-control"><button type="button" id="minus">−</button><strong id="quantityLabel">1</strong><button type="button" id="plus">+</button><span>vaso(s)</span></div></fieldset><div class="price-line"><span>Tu total estimado</span><strong id="total">S/ 3.50</strong></div><button class="btn btn-coral" type="submit">Continuar con mi pedido <span>→</span></button></form></section>
<section id="como" class="steps section"><div class="section-title"><p class="eyebrow">SIMPLE Y RICO</p><h2>Así llega tu antojo.</h2></div><div class="step-grid"><div><b>01</b><h3>Personaliza</h3><p>Elige tu tamaño, sabor y cantidad.</p></div><div><b>02</b><h3>Cuéntanos dónde</h3><p>Déjanos tus datos y dirección en Huánuco.</p></div><div><b>03</b><h3>Disfruta</h3><p>Preparamos y enviamos tu pedido con cuidado.</p></div></div></section></main><footer><span>© <?=date('Y')?> Adoquines Milkito</span><span>Hecho con sabor huanuqueño ♥</span></footer><script src="assets/app.js?v=3"></script></script></body></html>
