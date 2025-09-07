<?php include 'header.php'; ?>
<?php require 'connect.php'; ?>
<?php // Fetch products
$products = $pdo->query('SELECT * FROM products WHERE is_featured=1')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>BeeBloom</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lora:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Lora', serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(to bottom, #fff8e1, #fbe9a3);
      background-attachment: fixed;
      color: #333;
    }

    main {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 120px 80px 80px 80px;
      min-height: calc(100vh - 160px);
    }

    .hero-text {
      flex: 1;
    }

    .hero-text h1 {
      font-size: 2.5rem;
      margin-bottom: 20px;
      margin-top: -60px;
      color: #502925ee;
    }

    .hero-text .welcome-text {
      font-size: 18px;
      color: #54370eff;
      line-height: 1.6;
      margin-top: 60px;
      text-align: justify;
    }

    .hero-text .btn-container {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .hero-text .btn {
      display: inline-block;
      padding: 20px 35px;
      background-color: #502925ee;
      color: white;
      text-decoration: none;
      font-weight: bold;
      border-radius: 5px;
      cursor: pointer;
    }

    .hero-text .btn:hover {
      background-color: #381d1a;
    }

    .hero-image {
      flex: 1;
      display: flex;
      justify-content: center;
    }

    .swiper {
      width: 500px;
      height: 450px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      overflow: hidden;
      margin-top: 25px;
    }

    .swiper-slide img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 12px;
    }

    .products {
      padding: 80px 50px;
      text-align: center;
      display: none;
    }

    .products h2 {
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      color: #502925ee;
      margin-bottom: 40px;
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


    .footer {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      padding: 40px 60px;
      background-color: #5b362f;
      color: #f8f4f1;
    }

    .footer .footer-section {
      width: 30%;
    }

    .footer .footer-section h4 {
      margin-bottom: 15px;
      font-size: 18px;
      font-weight: 600;
    }

    .footer .footer-section p {
      text-align: justify;
      line-height: 1.7;
      margin-bottom: 20px;
    }

    .footer .footer-section ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .footer .footer-section ul li {
      margin-bottom: 10px;
    }

    .footer .footer-section ul li a {
      color: #f8f4f1;
      text-decoration: none;
      transition: color 0.3s;
    }

    .footer .footer-section ul li a:hover {
      color: #ffd700;
    }

    .footer .social-icons {
      display: flex;
      gap: 15px;
      margin-top: 20px;
    }

    @media(max-width: 768px) {
      main {
        flex-direction: column;
        text-align: center;
        padding: 140px 20px 80px 20px;
      }

      .hero-image {
        width: 100%;
        margin-top: 20px;
      }

      nav {
        width: 100%;
        margin-left: 0;
        margin-top: 15px;
      }

      nav ul {
        justify-content: center;
        flex-wrap: wrap;
        gap: 15px;
      }

      .header {
        flex-direction: column;
        align-items: center;
        gap: 15px;
      }

      .swiper {
        width: 100%;
        height: 350px;
      }

      .product-cards {
        flex-direction: column;
        align-items: center;
      }
    }
  </style>
</head>

<body>

  <main>
    <div class="hero-text">
      <h1>Pure Honey,<br>Straight From Our Bees</h1>
      <p class="welcome-text">
        Welcome to BeeBloom, your trusted source for pure, natural honey.
        We believe that the best things in life come straight from nature, which
        is why every drop of our honey is carefully harvested from our own bees
        with love and respect for the environment. At BeeBloom, we go beyond just
        making honey — we nurture our bees, protect their habitats, and ensure that
        each jar is filled with goodness that’s as close to nature as possible.
        Our honey is not only delicious but also packed with natural health
        benefits, making it a perfect choice for your daily wellness. Whether you
        enjoy it in your tea, on your toast, or straight from the spoon, BeeBloom
        brings you a taste of nature’s golden gift.
      </p>
      <div class="btn-container">
        <a href="javascript:void(0)" class="btn" id="shopNowBtn">Shop Now</a>
      </div>
    </div>

    <div class="hero-image">
      <div class="swiper">
        <div class="swiper-wrapper">
          <div class="swiper-slide"><img src="image/img1.jpg" alt="Honey Image 1"></div>
          <div class="swiper-slide"><img src="image/img2.jpg" alt="Honey Image 2"></div>
          <div class="swiper-slide"><img src="image/img5.jpg" alt="Honey Image 3"></div>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
      </div>
    </div>
  </main>

  <section id="products" class="products">
    <h2> Featured Products </h2>

    <div class="product-grid">
      <?php foreach ($products as $p): ?>
        <div class="card">
          <img src="<?= htmlspecialchars($p['image'] ?: 'placeholder.png') ?>" alt="Product">
          <div class="card-content">
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <p>Rs <?= number_format($p['price'], 2) ?></p>
            <p>Stock: <?= intval($p['stock']) ?> | Weight: <?= htmlspecialchars($p['weight']) ?> g</p>

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
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div style="text-align: center; margin-top: 30px;">
      <a href="products.php" class="btn btn-warning">Show More Products</a>
    </div>
  </section>
  <section id="footer">
    <div class="footer">
      <div class="footer-section">
        <h4>About Us</h4>
        <p>🍯 At BeeBloom, we bring you pure and natural honey straight from the hive. Every jar is crafted with care to ensure freshness, quality, and the golden goodness of nature. Our mission is to share the health and sweetness of bee products with your home. 🐝🌼</p>
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-linkedin"></i></a>
        </div>
      </div>

      <div class="footer-section">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="#">Home</a></li>
          <li><a href="#">Products</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h4>Contact Info</h4>
        <p><i class="fas fa-map-marker-alt"></i> 123 Flower road, Colombo, Sri Lanka.</p>
        <p><i class="fas fa-phone"></i> 0111234567</p>
        <p><i class="fas fa-envelope"></i> contact@example.com</p>
      </div>
    </div>
  </section>

  <!-- Swiper JS -->
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

  <!-- Page JS -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Swiper init
      if (typeof Swiper !== 'undefined') {
        new Swiper('.swiper', {
          loop: true,
          slidesPerView: 1,
          autoplay: {
            delay: 3000,
            disableOnInteraction: false
          },
          pagination: {
            el: '.swiper-pagination',
            clickable: true
          },
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
          }
        });
      }

      // Login check (dummy: replace with PHP if needed)
      function isLoggedIn() {
        return localStorage.getItem("loggedIn") === "true";
      }

      // Shop Now button
      const shopBtn = document.getElementById("shopNowBtn");
      if (shopBtn) {
        shopBtn.addEventListener("click", () => {
          if (isLoggedIn()) {
            const productsSection = document.getElementById("products");
            if (productsSection) {
              productsSection.style.display = "block";
              productsSection.scrollIntoView({
                behavior: "smooth"
              });
            }
          } else {
            localStorage.setItem("redirectAfterLogin", window.location.href + "#products");
            window.location.href = "login.php";
          }
        });
      }

      // Buy Now JS fallback (not used since now buttons link directly)
      window.buyNow = function(product) {
        if (!isLoggedIn()) {
          localStorage.setItem("redirectAfterLogin", window.location.href + "#products");
          window.location.href = "login.php";
        }
      };

      // Auto-show products if logged in
      if (isLoggedIn()) {
        const productsSection = document.getElementById("products");
        if (productsSection) productsSection.style.display = "block";
        if (window.location.hash === "#products") {
          const el = document.getElementById("products");
          if (el) el.scrollIntoView({
            behavior: "smooth"
          });
        }
      }
    });
  </script>
</body>

</html>