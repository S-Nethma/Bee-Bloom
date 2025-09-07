<?php
include 'header.php';
require 'connect.php';
require_login();
$uid = $_SESSION['user']['user_id'];

$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC');
$stmt->execute([$uid]);
$orders = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Your Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: rgba(251, 233, 163, 0.85);
            font-family: 'Lora', serif;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
            color: #5d4037;
        }
        .order-card {
            background: #fff3e0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 2px solid #fbc02d;
        }
        .order-card h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #4e342e;
        }
        .order-card p {
            font-weight: 500;
            color: #4e342e;
            margin-bottom: 6px;
        }
        .order-items {
            list-style: none;
            padding-left: 0;
            margin-top: 10px;
        }
        .order-items li {
            padding: 6px 0;
            border-bottom: 1px solid #eee;
            color: #3e2723;
        }
        .order-items li:last-child {
            border-bottom: none;
        }
        .empty-msg {
            text-align: center;
            margin-top: 40px;
            font-size: 1.2rem;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Orders</h1>

        <?php if (!$orders): ?>
            <p class="empty-msg">No orders yet.</p>
        <?php endif; ?>

        <?php foreach ($orders as $o): ?>
            <div class="order-card">
                <h3>Order #<?=intval($o['order_id'])?> — <?=htmlspecialchars($o['order_date'])?></h3>
                <p><strong>Name:</strong> <?=htmlspecialchars($o['name'])?></p>
                <p><strong>Address:</strong> <?=htmlspecialchars($o['address'])?></p>
                <p><strong>Phone:</strong> <?=htmlspecialchars($o['phone'])?></p>
                <p><strong>Payment Method:</strong> <?=htmlspecialchars(ucfirst($o['payment_method']))?></p>
                <?php if($o['payment_method'] === 'card'): ?>
                    <p><strong>Card:</strong> <?=htmlspecialchars($o['card_number'])?> | Exp: <?=htmlspecialchars($o['expiry_date'])?></p>
                <?php endif; ?>
                <p><strong>Total:</strong> Rs <?=number_format($o['total_price'],2)?></p>

                <ul class="order-items">
                <?php
                    $stmt = $pdo->prepare('SELECT oi.*, p.name FROM order_items oi JOIN products p USING(product_id) WHERE oi.order_id = ?');
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
