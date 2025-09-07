<?php
if (session_status() === PHP_SESSION_NONE) session_start();


$cart_count = 0;
if (!empty($_SESSION['user']['user_id'])) {
    require_once 'connect.php';
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(quantity),0) as cnt FROM carts WHERE user_id = ?');
    $stmt->execute([$_SESSION['user']['user_id']]);
    $cart_count = (int) $stmt->fetchColumn();
}
?>

<style>
nav {
  background-color: #502925ee;
  padding: 15px 40px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: sticky;
  top:0;
  z-index:100;
  box-shadow:0 3px 8px rgba(0,0,0,0.15);
}

nav a, nav span {
  color: #fff;
  margin: 0 10px;
  text-decoration: none;
  font-family: 'Lora', serif;
  font-size: 18px;
}

nav a:hover { text-decoration: underline; }

nav span.logo { font-size: 26px; font-weight: bold; }

nav .cart-icon { position: relative; display: inline-block; }
nav .cart-icon img { width:24px; height:24px; }
nav .cart-badge {
  position: absolute;
  top:-6px; right:-6px;
  background:#d84315;
  color:#fff;
  font-size:12px;
  font-weight:bold;
  border-radius:50%;
  width:18px; height:18px;
  display:flex; align-items:center; justify-content:center;
}



</style>

<nav>
  <div class="left-nav">
    <span  class="logo" >🍯 BeeBloom</span>
    <a href="index.php">Home</a>
    <?php if(isset($_SESSION['user'])): ?>
      <a href="products.php">Products</a>
      <a href="#footer">Contact Us</a>
    <?php endif; ?>
  </div>
  <div class="right-nav">
    <?php if(isset($_SESSION['user'])): ?>
      <a href="cart.php" class="cart-icon">
        <img src="image/cart.png" alt="Cart">
        <span class="cart-badge"><?=intval($cart_count)?></span>
      </a>
      <span>Welcome, <?=htmlspecialchars($_SESSION['user']['name'])?></span>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="signup.php">Sign Up</a>
    <?php endif; ?>
  </div>
</nav>
