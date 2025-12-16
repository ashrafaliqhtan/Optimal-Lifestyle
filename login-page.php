<?php
session_start();
require_once 'config.php'; // Database connection file

$error_message = "";

// Check for cookies and restore session if available
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['user_name'] = $_COOKIE['user_name'];
    $_SESSION['user_email'] = $_COOKIE['user_email'];
    header("Location: index.php");
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        try {
            // Fetch user from database
            $stmt = $pdo->prepare("SELECT user_id, name, email, password FROM Users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(); // Secure session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                // Store in cookies if "Remember Me" is checked
                if (isset($_POST['rememberMe'])) {
                    setcookie("user_id", $user['user_id'], time() + (86400 * 30), "/"); // 30 days
                    setcookie("user_name", $user['name'], time() + (86400 * 30), "/");
                    setcookie("user_email", $user['email'], time() + (86400 * 30), "/");
                }

                // Redirect after successful login
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Optimal Lifestyle</title>
    <link rel="stylesheet" href="Styles/login-styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-success">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Optimal Lifestyle</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="BMI-page.php">BMI Calculator</a></li>
                <li class="nav-item"><a class="nav-link" href="articles-page.php">Articles</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link fw-bold active" href="login-page.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Login Form -->
<div class="container mt-auto">
    <div class="text-center">
        <h1>Welcome Back!</h1>
        <h4>Please enter your login details</h4>
    </div>
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>
    <form class="container bg-light p-4 rounded shadow" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input required type="email" name="email" id="email" class="form-control">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input required type="password" name="password" id="password" class="form-control">
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
            <label class="form-check-label" for="rememberMe">Remember Me</label>
        </div>
        <button type="submit" name="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <div class="text-center mt-3">
        <p>New here? <a href="registration-page.php">Create an Account</a></p>
    </div>
</div>

<!-- Footer -->
<footer class="bg-success mt-auto py-3 text-white text-center">
    <p>&copy; <?= date('Y') ?> Optimal Lifestyle. All rights reserved.</p>
    <p>Contact us: <a href="mailto:support@OptimalLifestyle.com" class="text-white">support@OptimalLifestyle.com</a></p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>