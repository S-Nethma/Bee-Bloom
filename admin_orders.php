<?php
include 'header.php';
if (session_status() === PHP_SESSION_NONE) session_start();
require 'connect.php';
require_admin();

// Fetch orders with user info
$stmt = $pdo->query('SELECT o.*, u.name, u.email 
                     FROM orders o 
                     JOIN users u USING(user_id) 
                     ORDER BY o.order_date ');
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Orders (Admin)</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <style>
    body { 
      font-family: 'Lora', serif; 
      margin:0; 
      background: linear-gradient(to bottom, #fff8e1, #fbe9a3); 
      color:#4e342e; 
    }
    h1 { 
      text-align:center; 
      margin:30px 0; 
      color:#5d4037; 
      font-size:2.2rem; 
    }

    .orders-container { 
      max-width: 900px; 
      margin: 0 auto; 
      padding: 20px; 
    }

    .order-card { 
      background:#fff3e0; 
      border-radius:15px; 
      border:2px solid #fbc02d; 
      box-shadow:0 4px 10px rgba(0,0,0,0.1); 
      padding:20px; 
      margin-bottom:20px; 
    }

    .order-card h3 { 
      margin:0 0 10px; 
      font-size:1.2rem; 
      color:#3e2723; 
    }

    .order-meta { 
      font-size:0.9rem; 
      color:#5d4037; 
      margin-bottom:10px; 
    }

    .order-items { 
      margin:0; 
      padding-left:20px; 
      color:#4e342e; 
    }

    .order-items li { 
      margin:4px 0; 
      font-size:0.95rem; 
    }

    @media(max-width:600px){
      .orders-container { padding: 10px; }
      h1 { font-size:1.8rem; }
    }
  </style>
</head>
<body>

<h1>All Orders (Admin)</h1>
<div class="orders-container">
<?php if (!$orders): ?>
  <p>No orders yet.</p>
<?php endif; ?>

<?php foreach ($orders as $o): ?>
  <div class="order-card">
    <h3>Order #<?=intval($o['order_id'])?> — <?=htmlspecialchars($o['order_date'])?></h3>
    <p class="order-meta">
      <strong>User:</strong> <?=htmlspecialchars($o['name'])?> 
      (<?=htmlspecialchars($o['email'])?>)
    </p>
    <p class="order-meta">
      <strong>Total:</strong> Rs <?=number_format($o['total_price'],2)?>
    </p>
    <ul class="order-items">
      <?php
        $stmt = $pdo->prepare('SELECT oi.*, p.name 
                               FROM order_items oi 
                               JOIN products p USING(product_id) 
                               WHERE oi.order_id = ?');
        $stmt->execute([$o['order_id']]);
        foreach ($stmt->fetchAll() as $it) {
          echo '<li>' . htmlspecialchars($it['name']) . 
               ' — ' . intval($it['quantity']) . 
               ' × Rs ' . number_format($it['price'],2) . '</li>';
        }
      ?>
    </ul>
  </div>
<?php endforeach; ?>
</div>

</body>
</html>
