<?php
include 'header.php';
require 'connect.php';
require_login();
$uid = $_SESSION['user']['user_id'];

$action = $_POST['action'] ?? ($_GET['action'] ?? null);

// ADD TO CART or BUY NOW
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $quantity = max(1, intval($_POST['quantity'] ?? 1));

    $stmt = $pdo->prepare('SELECT stock FROM products WHERE product_id = ?');
    $stmt->execute([$product_id]);
    $prod = $stmt->fetch();
    if (!$prod) { die('Product not found'); }
    $quantity = min($quantity, $prod['stock']);

    $stmt = $pdo->prepare('INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = LEAST(VALUES(quantity) + quantity, ?)');
    $stmt->execute([$uid, $product_id, $quantity, $prod['stock']]);

    // Redirect depending on Buy Now or Add to Cart
    $redirect = $_POST['redirect'] ?? null;
    if ($redirect === 'checkout') {
        header('Location: checkout.php');
    } else {
        header('Location: cart.php');
    }
    exit;
}

// UPDATE CART
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantities = $_POST['quantity'] ?? [];
    foreach ($quantities as $cart_id => $q) {
        $q = max(0, intval($q));
        if ($q === 0) {
            $pdo->prepare('DELETE FROM carts WHERE cart_id = ? AND user_id = ?')->execute([$cart_id, $uid]);
        } else {
            $stmt = $pdo->prepare('SELECT p.stock FROM carts c JOIN products p ON p.product_id = c.product_id WHERE c.cart_id = ? AND c.user_id = ?');
            $stmt->execute([$cart_id, $uid]);
            $r = $stmt->fetch();
            if ($r) {
                $q = min($q, $r['stock']);
                $pdo->prepare('UPDATE carts SET quantity = ? WHERE cart_id = ? AND user_id = ?')->execute([$q, $cart_id, $uid]);
            }
        }
    }
    header('Location: cart.php');
    exit;
}

// REMOVE ITEM
if ($action === 'remove' && isset($_GET['id'])) {
    $cid = intval($_GET['id']);
    $pdo->prepare('DELETE FROM carts WHERE cart_id = ? AND user_id = ?')->execute([$cid, $uid]);
    header('Location: cart.php');
    exit;
}

// FETCH CART ITEMS
$stmt = $pdo->prepare('SELECT c.cart_id, c.quantity, p.name, p.price, p.image, p.stock 
                       FROM carts c 
                       JOIN products p USING(product_id) 
                       WHERE c.user_id = ?');
$stmt->execute([$uid]);
$items = $stmt->fetchAll();

$subtotal = 0;
foreach ($items as $it) {
    $subtotal += $it['price'] * $it['quantity'];
}
$shipping = ($subtotal > 0) ? 450 : 0;
$total = $subtotal + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    .cart-wrapper { background: rgba(251, 233, 163, 0.85); min-height: 100vh; padding: 40px 0; }
    .product-card { background: white; border-radius: 12px; transition: transform 0.2s; }
    .product-card:hover { transform: translateY(-2px); }
    .quantity-input { width: 60px; text-align: center; border: 1px solid #dee2e6; border-radius: 6px; }
    .product-image { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; }
    .summary-card { background: white; border-radius: 12px; position: sticky; top: 20px; }
    .checkout-btn { background: linear-gradient(135deg, #d6a85eff, #d79b41ff); border: none; transition: transform 0.2s; }
    .checkout-btn:hover { transform: translateY(-2px); background: linear-gradient(135deg, #d79b41ff, #e19d1eff); }
    .remove-btn { color: #dc2626; cursor: pointer; transition: all 0.2s; }
    .remove-btn:hover { color: #991b1b; }
    .quantity-btn { width: 28px; height: 28px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; background: #c7a161ff; border: none; transition: all 0.2s; }
    .quantity-btn:hover { background: #5e301eff; }
    .btn, button, input[type="submit"] { background-color: #d79b41ff; }
  </style>
</head>
<body>
<div class="cart-wrapper">
  <div class="container">
    <div class="row g-4">
      <!-- Cart Items -->
      <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="mb-0">Shopping Cart</h4>
          <span class="text-muted"><?=count($items)?> items</span>
        </div>

        <form method="post" action="cart.php?action=update">
          <div class="d-flex flex-column gap-3">
            <?php if ($items): ?>
              <?php foreach ($items as $it): ?>
                <div class="product-card p-3 shadow-sm">
                  <div class="row align-items-center">
                    <div class="col-md-2">
                      <img src="<?=htmlspecialchars($it['image'] ?: 'https://via.placeholder.com/100')?>" alt="Product" class="product-image">
                    </div>
                    <div class="col-md-4">
                      <h6 class="mb-1"><?=htmlspecialchars($it['name'])?></h6>
                      <p class="text-muted mb-0">In stock: <?=$it['stock']?></p>
                    </div>
                    <div class="col-md-3">
                      <div class="d-flex align-items-center gap-2">
                        <button type="button" class="quantity-btn" onclick="changeQuantity(<?=$it['cart_id']?>,-1)">-</button>
                        <input type="number" class="quantity-input" name="quantity[<?=$it['cart_id']?>]" id="qty-<?=$it['cart_id']?>" value="<?=$it['quantity']?>" min="1" max="<?=$it['stock']?>">
                        <button type="button" class="quantity-btn" onclick="changeQuantity(<?=$it['cart_id']?>,1)">+</button>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <span class="fw-bold">Rs <?=number_format($it['price']*$it['quantity'],2)?></span>
                    </div>
                    <div class="col-md-1">
                      <a href="cart.php?action=remove&id=<?=$it['cart_id']?>" class="remove-btn"><i class="bi bi-trash"></i></a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-muted">Your cart is empty.</p>
            <?php endif; ?>
          </div>
          <?php if ($items): ?>
            <div class="mt-3">
              <button type="submit" class="btn btn-secondary">Update Cart</button>
            </div>
          <?php endif; ?>
        </form>
      </div>

      <!-- Summary -->
      <div class="col-lg-4">
        <div class="summary-card p-4 shadow-sm">
          <h5 class="mb-4">Order Summary</h5>
          <div class="d-flex justify-content-between mb-3">
            <span class="text-muted">Subtotal</span>
            <span>Rs.<?=number_format($subtotal,2)?></span>
          </div>
          <div class="d-flex justify-content-between mb-3">
            <span class="text-muted">Shipping</span>
            <span>Rs. <?=number_format($shipping,2)?></span>
          </div>
          <hr>
          <div class="d-flex justify-content-between mb-4">
            <span class="fw-bold">Total</span>
            <span class="fw-bold">Rs. <?=number_format($total,2)?></span>
          </div>
          <?php if ($items): ?>
            <a href="checkout.php" class="btn btn-primary checkout-btn w-100 mb-3">Proceed to Checkout</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function changeQuantity(id, delta) {
  const input = document.getElementById('qty-' + id);
  let val = parseInt(input.value) + delta;
  if (val >= 1 && val <= parseInt(input.max)) {
    input.value = val;
  }
}
</script>
</body>
</html>
