<?php
session_start();
include 'header.php';
require 'connect.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: products.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header("Location: products.php");
    exit;
}

// Fetch product
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id=?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    header("Location: products.php");
    exit;
}

if (isset($_POST['update_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $weight = $_POST['weight'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // IMAGE HANDLING
    $image = $product['image'];
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'image/';
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $destPath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmp, $destPath)) {
            $image = $destPath;
        } else {
            echo "<p style='color:red;'>Image upload failed. Check folder permissions.</p>";
        }
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, weight=?, image=?, is_featured=? WHERE product_id=?");
    $stmt->execute([$name, $description, $price, $stock, $weight, $image, $is_featured, $id]);

    header("Location: products.php");
    exit;
}
?>

<h1>Edit Product 🍯</h1>
<div class="edit-card">
    <form method="post" enctype="multipart/form-data" class="edit-form">
        <label>Product Image</label>
        <?php if (!empty($product['image'])): ?>
            <img src="<?= htmlspecialchars($product['image']) ?>" width="120" style="margin-bottom:10px; border-radius:8px;">
        <?php endif; ?>
        <input type="file" name="image">

        <label>Product Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

        <label>Product Description</label>
        <textarea name="description" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>

        <label>Price (Rs)</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>

        <label>Stock</label>
        <input type="number" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" required>

        <label>Weight (g)</label>
        <input type="number" step="0.01" name="weight" value="<?= htmlspecialchars($product['weight'] ?? 0) ?>" required>

        <label class="checkbox-label">
            <input type="checkbox" name="is_featured" <?= $product['is_featured'] ? 'checked' : '' ?>> Mark as Featured Product
        </label>

        <button type="submit" name="update_product">Update Product</button>
    </form>
</div>

<style>
body { font-family: 'Lora', serif; margin:0; background:#fff8e1; color:#4e342e; }
h1 { text-align:center; margin:30px 0; color:#5d4037; font-size:2.2rem; }

.edit-card { max-width:500px; margin:0 auto 50px; background:#fff3e0; border-radius:15px; 
    border:2px solid #fbc02d; box-shadow:0 4px 10px rgba(0,0,0,0.1); padding:20px 30px; }

.edit-form label { display:block; margin:12px 0 6px; font-weight:bold; color:#3e2723; }
.edit-form input[type=text], .edit-form input[type=number], .edit-form input[type=file], .edit-form textarea { 
    width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; font-size:14px; margin-bottom:10px; }

.edit-form button { background:linear-gradient(135deg,#ef6c00,#d84315); color:#fff; border:none; 
    padding:10px 20px; border-radius:8px; font-size:16px; font-weight:bold; cursor:pointer; transition:0.3s; 
    margin-top:10px; width:100%; }

.edit-form button:hover { background:linear-gradient(135deg,#d84315,#bf360c); }

.checkbox-label { display:flex; align-items:center; gap:8px; margin-top:10px; font-weight:bold; color:#3e2723; }

@media(max-width:600px){ 
    .edit-card {padding:15px 20px;} 
    h1 {font-size:1.8rem;} 
    .edit-form button {font-size:14px;} 
}
</style>
