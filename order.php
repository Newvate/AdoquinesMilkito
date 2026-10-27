<?php session_start(); require __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && empty($_SESSION['milkito_cart'])) { header('Location: index.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') verify_csrf();
$flavorId=(int)($_POST['flavor_id']??0); $size=trim($_POST['size']??''); $quantity=max(1,min(20,(int)($_POST['quantity']??1)));
$prices=['Grande'=>3.50,'Chico'=>1.80];
$cart=[]; if (!empty($_POST['cart_json'])) { $decoded=json_decode($_POST['cart_json'], true); if (is_array($decoded)) foreach($decoded as $item) if(isset($prices[$item['size']],$item['flavor_id'])) $cart[]=['size'=>$item['size'],'flavor_id'=>(int)$item['flavor_id'],'quantity'=>max(1,min(20,(int)($item['quantity']??1)))]; }
if (!$cart && $_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['customer_name'])) $cart=[['size'=>$size,'flavor_id'=>$flavorId,'quantity'=>$quantity]];
if (!$cart && !empty($_SESSION['milkito_cart'])) $cart=$_SESSION['milkito_cart'];
foreach ($cart as &$legacyItem) {
    if (($legacyItem['size'] ?? '') === 'Personal' || ($legacyItem['size'] ?? '') === 'Doble') $legacyItem['size'] = 'Chico';
    if (($legacyItem['size'] ?? '') === 'Familiar') $legacyItem['size'] = 'Grande';
    if (!isset($prices[$legacyItem['size'] ?? ''])) $legacyItem['size'] = stripos((string)($legacyItem['size'] ?? ''), 'grande') !== false ? 'Grande' : 'Chico';
}
unset($legacyItem);
if ($cart) $_SESSION['milkito_cart']=$cart;
if (empty($_SESSION['user'])) { header('Location: auth.php'); exit; }
$size=$cart[0]['size']; $flavorId=$cart[0]['flavor_id']; $quantity=$cart[0]['quantity'];
if (!isset($prices[$size])) exit('Tamaño no válido.');
$flavor=db()->prepare('SELECT * FROM flavors WHERE id=? AND active=1'); $flavor->execute([$flavorId]); $flavor=$flavor->fetch();
if (!$flavor) {
    $flavor = db()->query('SELECT * FROM flavors WHERE active=1 ORDER BY id LIMIT 1')->fetch();
    if ($flavor) { $flavorId=(int)$flavor['id']; foreach($cart as &$item) $item['flavor_id']=$flavorId; unset($item); $_SESSION['milkito_cart']=$cart; }
}
if (!$flavor) exit('No hay sabores registrados en MySQL. Importa nuevamente database.sql.');
$cartTotal=array_sum(array_map(fn($i)=>$prices[$i['size']]*$i['quantity'],$cart));
$quantityForDisplay=$quantity;
if ($cartTotal > 0 && isset($prices[$size])) $quantityForDisplay=$cartTotal/$prices[$size];
$quantity=$quantityForDisplay;
if (isset($_POST['customer_name'])) {
  $name=trim($_POST['customer_name']);$phone=trim($_POST['phone']);$address=trim($_POST['address']);$district=trim($_POST['district']);$notes=trim($_POST['notes']??'');
  if(!$name||!$phone||!$address||!$district){$error='Completa todos los datos obligatorios.';}else{$pdo=db();$pdo->beginTransaction();$st=$pdo->prepare('INSERT INTO orders(user_id,customer_name,phone,address,district,notes,total) VALUES(?,?,?,?,?,?,?)');$total=array_sum(array_map(fn($i)=>$prices[$i['size']]*$i['quantity'],$cart));$st->execute([$_SESSION['user']['id'],$name,$phone,$address,$district,$notes,$total]);$id=(int)$pdo->lastInsertId();$itemSt=$pdo->prepare('INSERT INTO order_items(order_id,flavor_id,size_name,quantity,unit_price) VALUES(?,?,?,?,?)');foreach($cart as $item)$itemSt->execute([$id,$item['flavor_id'],$item['size'],$item['quantity'],$prices[$item['size']]]);$pdo->commit();unset($_SESSION['milkito_cart']);$success=$id;}
}
if (isset($success)) { echo '<script>window.addEventListener("DOMContentLoaded",()=>{const b=document.querySelector(".btn-dark");if(b){const a=document.createElement("a");a.href="account.php";a.className="btn btn-coral tracking-button";a.style.cssText="display:flex;width:100%;margin:12px 0 0 0;box-sizing:border-box;justify-content:center";a.textContent="Ver mis pedidos →";b.parentNode.appendChild(a)}});</script>'; }
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Finalizar pedido | Milkito</title><link rel="stylesheet" href="assets/style.css"></head><body><header class="topbar"><a class="brand" href="index.php"><span class="brand-mark">M</span><span>adoquines <b>milkito</b></span></a></header><main class="order-section" style="min-height:calc(100vh - 80px)"><?php if(isset($success)): ?><div class="order-intro"><p class="eyebrow">PEDIDO RECIBIDO</p><h2>¡Gracias por elegir Milkito!</h2><p>Tu pedido <b>#<?=e((string)$success)?></b> fue registrado. Te contactaremos al <?=e($phone)?> para confirmar el envío.</p><a class="btn btn-dark" href="index.php">Volver a la tienda →</a></div><?php else: ?><div class="order-intro"><p class="eyebrow">ÚLTIMO PASO</p><h2>¿Dónde lo llevamos?</h2><p>Pedido: <?=$quantity?> × <?=e($size)?> de <?=e($flavor['name'])?> · <b><?=money($prices[$size]*$quantity)?></b></p></div><form class="order-form" method="post"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="flavor_id" value="<?=$flavorId?>"><input type="hidden" name="size" value="<?=e($size)?>"><input type="hidden" name="quantity" value="<?=$quantity?>"><?php if(isset($error)): ?><p style="color:#c94b38"><?=e($error)?></p><?php endif; ?><label>Nombre completo*<input name="customer_name" required></label><label>Celular / WhatsApp*<input name="phone" required></label><label>Dirección exacta*<input name="address" placeholder="Calle, número y referencia" required></label><label>Distrito*<input name="district" placeholder="Huánuco, Amarilis, Pillco Marca..." required></label><label>Nota para el delivery<textarea name="notes" rows="3" style="width:100%;margin-top:8px;border:1px solid var(--line);padding:12px"></textarea></label><button class="btn btn-coral" type="submit">Confirmar pedido · <?=money($prices[$size]*$quantity)?></button></form><?php endif; ?></main></body></html>
