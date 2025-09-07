<?php
include 'header.php';
require_once 'connect.php';

// Get product ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Invalid product.</p>";
    exit;
}

$product_id = intval($_GET['id']);

// Fetch product from DB
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<p>Product not found.</p>";
    exit;
}
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container py-5">
    <div class="row">
        <!-- Product Images -->
        <div class="col-md-6 mb-4">
            <div class="card" style="align-items: center;">
                <img style="width: 500; height: 550;" src="<?= htmlspecialchars($product['image'] ?: 'placeholder.png') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-md-6">
            <h1 class="h2 mb-3"><?= htmlspecialchars($product['name']) ?></h1>

            <div class="mb-3">
                <span class="h4 me-2">Rs <?= number_format($product['price'], 2) ?></span>
                <?php if (!empty($product['old_price'])): ?>
                    <span class="text-muted text-decoration-line-through">Rs <?= number_format($product['old_price'], 2) ?></span>
                    <span class="badge bg-danger ms-2">
                        <?= round((($product['old_price'] - $product['price']) / $product['old_price']) * 100) ?>% OFF
                    </span>
                <?php endif; ?>
            </div>

            <p class="mb-2"><strong>Stock:</strong> <?= intval($product['stock']) ?></p>
            <p class="mb-4"><strong>Weight:</strong> <?= htmlspecialchars($product['weight']) ?> g</p>
            <p class="mb-4"><?= htmlspecialchars($product['description'] ?: 'No description available.') ?></p>

            <!-- Quantity -->
            <div class="mb-4">
                <div class="d-flex align-items-center">
                    <label class="me-2">Quantity:</label>
                    <select class="form-select w-auto" id="quantity">
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-grid gap-2">
                <form method="post" action="cart.php">
                    <input type="hidden" name="product_id" value="<?= intval($product['product_id']) ?>">
                    <input type="hidden" name="quantity" id="form-quantity" value="1">
                    <button class="btn btn-primary" style="width: 650;" type="submit" name="action" value="add">Add to Cart</button>
                </form>
                <!-- Buy Now Form -->
                <form method="post" action="cart.php?action=add">
                    <input type="hidden" name="product_id" value="<?= intval($product['product_id']) ?>">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="redirect" value="checkout">
                    <button type="submit" class="btn btn-outline-secondary" style="width: 650; margin-top: -10;">Buy Now</button>
                </form>
            </div>

            <!-- Additional Info -->
            <div class="mt-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-truck text-primary me-2"></i>
                    <span>Free shipping on orders over Rs 500</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-undo text-primary me-2"></i>
                    <span>30-day return policy</span>
                </div>
                <div class="d-flex align-items-center">
                    <i class="fas fa-shield-alt text-primary me-2"></i>
                    <span>2-year warranty</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update hidden quantity input
    document.getElementById('quantity').addEventListener('change', function() {
        document.getElementById('form-quantity').value = this.value;
    });
</script>