<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include 'header.php';
require 'connect.php';
require_login();
$uid = $_SESSION['user']['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $payment_method = $_POST['payment_method'];
    $card_number = $_POST['card_number'] ?? null;
    $expiry_date = $_POST['expiry_date'] ?? null;
    $cvv = $_POST['cvv'] ?? null;

    $pdo->beginTransaction();
    try {
        // Lock cart items
        $stmt = $pdo->prepare('SELECT c.product_id, c.quantity, p.price, p.stock FROM carts c JOIN products p USING(product_id) WHERE c.user_id = ? FOR UPDATE');
        $stmt->execute([$uid]);
        $items = $stmt->fetchAll();
        if (!$items) throw new Exception('Cart is empty');

        $total = 0;
        foreach ($items as $it) {
            if ($it['quantity'] > $it['stock']) throw new Exception('Insufficient stock for a product');
            $total += $it['price'] * $it['quantity'];
        }

        // Insert order with user details
        $stmt = $pdo->prepare('INSERT INTO orders (user_id, total_price, name, address, phone, payment_method, card_number, expiry_date, cvv) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$uid, $total, $name, $address, $phone, $payment_method, $card_number, $expiry_date, $cvv]);
        $order_id = $pdo->lastInsertId();

        // Insert order items and update stock
        $stmtIns = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
        $stmtStock = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE product_id = ?');
        foreach ($items as $it) {
            $stmtIns->execute([$order_id, $it['product_id'], $it['quantity'], $it['price']]);
            $stmtStock->execute([$it['quantity'], $it['product_id']]);
        }

        // Clear cart
        $pdo->prepare('DELETE FROM carts WHERE user_id = ?')->execute([$uid]);

        $pdo->commit();
        echo "<p style='text-align:center; margin-top:50px; font-size:1.2rem;'>Order placed successfully! Thank you.</p>";
        echo "<p style='text-align:center;'><a href='orders.php' style='color:#ef6c00; text-decoration:underline;'>View Your Orders</a></p>";
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        die('Checkout failed: ' . $e->getMessage());
    }
} else {
    // Show form
?>

    <style>
        body {
            font-family: 'Lora', serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #fff8e1, #fbe9a3);
            background-attachment: fixed;
            color: #333;
        }
    </style>

    <body>
        <div style="max-width:500px; 
         margin:50px auto;
         background:#fff3e0; 
         padding:20px; border-radius:10px; 
         border:2px solid #fbc02d;">

            <h2 style="text-align:center; color:#5d4037;">Checkout</h2>
            <form method="post">
                <label>Name</label>
                <input type="text" name="name" required style="width:100%; padding:8px; margin:5px 0;"><br>

                <label>Address</label>
                <textarea name="address" required style="width:100%; padding:8px; margin:5px 0;"></textarea><br>

                <label>Phone Number</label>
                <input type="tel" name="phone" required style="width:100%; padding:8px; margin:5px 0;"><br>

                <label>Payment Method</label>
                <select id="paymentMethod" name="payment_method" required style="width:100%; padding:8px; margin:5px 0;">
                    <option value="">Select</option>
                    <option value="card">Card</option>
                    <option value="cash">Cash on Delivery</option>
                </select><br>

                <div id="cardDetails" style="display:none;">
                    <label>Card Number</label>
                    <input type="text" name="card_number" maxlength="16" style="width:100%; padding:8px; margin:5px 0;"><br>
                    <label>Expiry Date</label>
                    <input type="month" name="expiry_date" style="width:100%; padding:8px; margin:5px 0;"><br>
                    <label>CVV</label>
                    <input type="text" name="cvv" maxlength="3" style="width:100%; padding:8px; margin:5px 0;"><br>
                </div>

                <button type="submit" style="background:#ef6c00; color:#fff; padding:10px 20px; border:none; border-radius:5px; width:100%; margin-top:10px;">Confirm Order</button>
            </form>
        </div>
    </body>
    <script>
        document.getElementById('paymentMethod').addEventListener('change', function() {
            const card = document.getElementById('cardDetails');
            if (this.value === 'card') {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    </script>
<?php
}
?>