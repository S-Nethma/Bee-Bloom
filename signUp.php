<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require 'connect.php';

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    header('Location: products.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!$name) $errors[] = 'Name is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email is invalid';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters';
    if ($password !== $confirm) $errors[] = 'Passwords do not match';

    if (empty($errors)) {
        // Check if email already exists
        $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already exists';
        } else {
            // Insert user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $hash]);

            // Log user in immediately
            $user_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare('SELECT user_id, name, email, role FROM users WHERE user_id = ?');
            $stmt->execute([$user_id]);
            $_SESSION['user'] = $stmt->fetch();

            header('Location: products.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>BeeBloom | Signup</title>
    <style>
      /* KEEP ALL ORIGINAL STYLES */
      body {font-family:'Lora',serif; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; background:rgba(251,233,163,0.85); background-size:cover;}
      .container {background:rgba(255,248,225,0.95); padding:60px 80px; border-radius:20px; box-shadow:0 8px 20px rgba(0,0,0,0.15); text-align:center; border:3px solid #f9a825; position:relative;}
      .container::before {content:"🐝"; font-size:40px; position:absolute; top:-30px; left:50%; transform:translateX(-50%); background:#fff8e1; border-radius:50%; padding:10px; border:2px solid #fbc02d;}
      h2 {color:#5d4037; margin-bottom:20px;}
      input {margin:10px 0; padding:12px; width:250px; border-radius:8px; border:2px solid #fbc02d; outline:none; transition:0.3s; font-size:15px;}
      input:focus {border-color:#ef6c00; box-shadow:0 0 6px #ffca28;}
      button {padding:12px 25px; border:none; border-radius:8px; background:linear-gradient(135deg,#ef6c00,#d84315); color:white; font-size:16px; font-weight:bold; cursor:pointer; transition:0.3s;}
      button:hover {background:linear-gradient(135deg,#d84315,#bf360c);}
      a {display:block; margin-top:15px; color:#ef6c00; text-decoration:none; font-weight:bold;}
      a:hover {text-decoration:underline;}
      ul {padding:0; list-style:none;}
      ul li {color:red; font-size:14px;}
    </style>
  </head>
  <body>
    <div class="container">
      <h2>Create Your BeeBloom Account</h2>
      <?php if ($errors): ?>
        <ul>
          <?php foreach ($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?>
        </ul>
      <?php endif; ?>
      <form method="post">
        <label>Name:<br><input name="name" required></label><br>
        <label>Email:<br><input name="email" type="email" required></label><br>
        <label>Password:<br><input name="password" type="password" required></label><br>
        <label>Confirm:<br><input name="confirm" type="password" required></label><br>
        <button type="submit">Sign up</button>
      </form>
      <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
  </body>
</html>
