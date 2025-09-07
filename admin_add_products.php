<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require 'connect.php';
require_admin(); // Make sure this function checks if user is admin
include 'header.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $weight = $_POST['weight'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    $image = '';
    if(!empty($_FILES['image']['name'])){
        $image = 'image/'.basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, weight, image, is_featured) VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([$name, $description, $price, $stock, $weight, $image, $is_featured]);

    header('Location: products.php'); 
    exit;
}
?>

<h1>Add New Product 🍯</h1>
<div class="edit-card">
    <form method="post" enctype="multipart/form-data" class="edit-form">
        <label>Product Image</label>
        <input type="file" name="image">

        <label>Product Name</label>
        <input type="text" name="name" required>

        <label>Product Description</label>
        <textarea name="description" rows="4" required></textarea>

        <label>Price (Rs)</label>
        <input type="number" step="0.01" name="price" required>

        <label>Stock</label>
        <input type="number" name="stock" required>

        <label>Weight (g)</label>
        <input type="number" step="0.01" name="weight" required>

        <label class="checkbox-label">
            <input type="checkbox" name="is_featured"> Mark as Featured Product
        </label>

        <button type="submit" name="add_product">Add Product</button> 
    </form>
</div>

<style>
body { font-family: 'Lora', serif; margin:0; background:#fff8e1; color:#4e342e; }
h1 { text-align:center; margin:30px 0; color:#5d4037; font-size:2.2rem; }

.edit-card { 
    max-width:500px; margin:0 auto 50px; 
    background:#fff3e0; border-radius:15px; 
    border:2px solid #fbc02d; box-shadow:0 4px 10px rgba(0,0,0,0.1); 
    padding:20px 30px; 
}

.edit-form label { display:block; margin:12px 0 6px; font-weight:bold; color:#3e2723; }
.edit-form input[type=text], .edit-form input[type=number], .edit-form input[type=file], .edit-form textarea { 
    width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; font-size:14px; margin-bottom:10px; 
}

.edit-form button { 
    background:linear-gradient(135deg,#ef6c00,#d84315); color:#fff; border:none; 
    padding:10px 20px; border-radius:8px; font-size:16px; font-weight:bold; cursor:pointer; transition:0.3s; 
    margin-top:10px; width:100%; 
}
.edit-form button:hover { background:linear-gradient(135deg,#d84315,#bf360c); }

.checkbox-label { display:flex; align-items:center; gap:8px; margin-top:10px; font-weight:bold; color:#3e2723; }

@media(max-width:600px){ 
    .edit-card {padding:15px 20px;} 
    h1 {font-size:1.8rem;} 
    .edit-form button {font-size:14px;} 
}
</style>
