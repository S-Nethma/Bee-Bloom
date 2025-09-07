

<?php

if (session_status() === PHP_SESSION_NONE) session_start();

require 'connect.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT user_id, name, email, password, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            $_SESSION['user'] = $user;
            header('Location: products.php');
            exit;
        } else {
            $errors[] = 'Invalid credentials';
        }
    }
}
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>BeeBloom | Login</title>
    <style>
      body {
        font-family: 'Lora', serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background: rgba(251, 233, 163, 0.85);
        background-size: cover;
        margin: 0;
      }

      .container {
        background: rgba(255, 248, 225, 0.95);
        padding: 60px 80px;
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        text-align: center;
        border: 3px solid #f9a825;
        position: relative;
      }

      .container::before {
        content: "🍯";
        font-size: 40px;
        position: absolute;
        top: -30px;
        left: 50%;
        transform: translateX(-50%);
        background: #fff8e1;
        border-radius: 50%;
        padding: 10px;
        border: 2px solid #fbc02d;
      }

      h2 {
        color: #5d4037;
        margin-bottom: 20px;
      }

      input {
        margin: 10px 0;
        padding: 12px;
        width: 250px;
        border-radius: 8px;
        border: 2px solid #fbc02d;
        outline: none;
        transition: 0.3s;
        font-size: 15px;
      }

      input:focus {
        border-color: #ef6c00;
        box-shadow: 0 0 6px #ffca28;
      }

      button {
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        background: linear-gradient(135deg, #ef6c00, #d84315);
        color: white;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
      }

      button:hover {
        background: linear-gradient(135deg, #d84315, #bf360c);
      }

      a {
        display: block;
        margin-top: 15px;
        color: #ef6c00;
        text-decoration: none;
        font-weight: bold;
      }

      a:hover {
        text-decoration: underline;
      }

      ul {
        padding: 0;
        list-style: none;
      }

      ul li {
        color: red;
        font-size: 14px;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <h2>Login to BeeBloom</h2>
      <?php if ($errors): ?>
        <ul>
          <?php foreach ($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?>
        </ul>
      <?php endif; ?>
      <form method="post">
        <label>Email:<br><input name="email" type="email" required></label><br>
        <label>Password:<br><input name="password" type="password" required></label><br>
        <button type="submit">Login</button>
      </form>
      <p>No account? <a href="signup.php">Sign up</a></p>
    </div>
  </body>
</html>

