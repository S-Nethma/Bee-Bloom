<?php
session_start();
require 'connect.php';

// Check if admin
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    header("Location: products.php");
    exit;
}

// Get product id from URL
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
}

// Redirect back to products page
header("Location: products.php");
exit;
?>
