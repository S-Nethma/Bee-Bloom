<?php
include 'header.php';
require_once 'connect.php';

// Handle delete request for admin
if (isset($_GET['delete_id']) && isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
  $delete_id = intval($_GET['delete_id']);

  // Delete image file if exists
  $stmt = $pdo->prepare("SELECT image FROM products WHERE product_id = ?");
  $stmt->execute([$delete_id]);
  $product_to_delete = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($product_to_delete && !empty($product_to_delete['image']) && file_exists($product_to_delete['image'])) {
    unlink($product_to_delete['image']);
  }

  // Delete product
  $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
  $stmt->execute([$delete_id]);

  header("Location: products.php");
  exit;
}

// Fetch products
$products = $pdo->query('SELECT * FROM products ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>BeeBloom Products</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Lora', serif;
      margin: 0;
      background: #fff8e1;
      color: #4e342e;
    }

    h1 {
      text-align: center;
      margin: 30px 0;
      color: #5d4037;
      font-size: 2.2rem;
    }

    /* Product Grid */
    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 24px;
      padding: 20px 40px;
    }

    /* Product Card */
    .card {
      background: #fff3e0;
      border-radius: 15px;
      border: 2px solid #fbc02d;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: 0.3s;
    }

    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    .card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }

    .card-content {
      padding: 14px;
      flex: 1;
      text-align: center;
    }

    .card-content h3 {
      margin: 0 0 8px;
      font-size: 18px;
      color: #3e2723;
    }

    .card-content p {
      margin: 5px 0;
      font-size: 14px;
      color: #5d4037;
    }

    /* Buttons like index.php */
    .card .btn-group {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-top: 10px;
      flex-wrap: wrap;
    }

    .card .btn-group .btn,
    .card .btn-buy {
      font-size: 0.9rem;
      padding: 8px 14px;
      border-radius: 6px;
      font-weight: 600;
      transition: 0.3s;
      text-align: center;
      cursor: pointer;
      border: none;
      display: inline-block;
      text-decoration: none;
    }

    .btn-outline-primary {
      border: 1px solid #007bff;
      color: #007bff;
      background: transparent;
    }

    .btn-outline-primary:hover {
      background-color: #007bff;
      color: #fff;
    }

    .btn-cart {
      border: 1px solid #28a745;
      color: #28a745;
      background: transparent;
    }

    .btn-cart:hover {
      background-color: #1e7e34;
      color: #fff;
    }

    .btn-buy {
      display: block;
      width: 100%;
      margin-top: 12px;
      border: 1px solid #dc3545;
      background: #dc3545;
      padding: 12px 0;
      font-size: 1rem;
      color: #fff;
      border-radius: 6px;
      text-align: center;
    }

    .btn-buy:hover {
      background-color: #b02a37;
      color: #fff;
    }

    /* Admin Buttons */
    .admin-add-btn {
      text-align: center;
      margin-bottom: 20px;
    }

    .admin-add-btn a {
      background: #fbc02d;
      color: #4e342e;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      transition: 0.3s;
    }

    .admin-add-btn a:hover {
      background: #f9a825;
    }

    .admin-links a {
      background: #d84315;
      color: #fff;
      padding: 5px 10px;
      border-radius: 6px;
      text-decoration: none;
      margin: 0 5px;
      font-size: 12px;
      transition: 0.3s;
    }

    .admin-links a:hover {
      background: #bf360c;
    }

    @media(max-width:600px) {
      .product-grid {
        padding: 10px;
      }

      .card-content h3 {
        font-size: 16px;
      }

      h1 {
        font-size: 1.8rem;
      }
    }
  </style>
</head>

<body>

  <h1>Our Honey Products 🍯</h1>

  <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
    <div class="admin-add-btn">
      <a href="admin_add_products.php">+ Add New Product</a>
      
      <a href="admin_orders.php" style=" background-color: #fa5454ff;  "  > View Orders</a>
    </div>
  <?php endif; ?>
  <hr>

  <div class="product-grid">
    <?php foreach ($products as $p): ?>
      <div class="card">
        <img src="<?= htmlspecialchars($p['image'] ?: 'placeholder.png') ?>" alt="Product">
        <div class="card-content">
          <h3><?= htmlspecialchars($p['name']) ?></h3>
          <p>Rs <?= number_format($p['price'], 2) ?></p>
          <p>Stock: <?= intval($p['stock']) ?> | Weight: <?= htmlspecialchars($p['weight']) ?> g</p>

          <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
            <div class="admin-links">
              <a href="admin_edit_products.php?id=<?= intval($p['product_id']) ?>">Edit</a>
              <a href="products.php?delete_id=<?= intval($p['product_id']) ?>"
                onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
            </div>
          <?php else: ?>
            <div class="btn-group">
              <a href="single-product.php?id=<?= intval($p['product_id']) ?>" class="btn btn-outline-primary">View</a>

              <!-- Add to Cart Form -->
              <form method="post" action="cart.php?action=add" style="display:inline;">
                <input type="hidden" name="product_id" value="<?= intval($p['product_id']) ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-cart">Add to Cart</button>
              </form>
            </div>

            <!-- Buy Now Form -->
            <form method="post" action="cart.php?action=add">
              <input type="hidden" name="product_id" value="<?= intval($p['product_id']) ?>">
              <input type="hidden" name="quantity" value="1">
              <input type="hidden" name="redirect" value="checkout">
              <button type="submit" class="btn-buy">Buy Now</button>
            </form>

          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</body>

</html>