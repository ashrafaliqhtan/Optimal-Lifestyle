<?php
session_start();
require_once 'config.php'; // Ensure this file contains your database connection details

$error_message = "";
$success_message = "";

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        $error_message = "Please fill in all fields.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT email FROM Users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error_message = "Email already exists. Please use a different email.";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user into the database
                $stmt = $pdo->prepare("INSERT INTO Users (name, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $hashed_password]);

                // Set success message and redirect to login page
                $success_message = "Registration successful! You can now log in.";
                header("Refresh: 3; url=login-page.php"); // Redirect after 3 seconds
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration - Optimal Lifestyle</title>
    <link rel="stylesheet" href="Styles/registration-styles.css">
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
                <li class="nav-item"><a class="nav-link" href="articles-page.php">Helpful Articles</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active fw-bold" href="login-page.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Registration Form -->
<div class="container">
    <div class="text-center my-4">
        <h1>Create a New Account</h1>
        <p>Enter your preferred username, email, and password to create a new account.</p>
    </div>
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>
    <form class="bg-light p-4 rounded shadow-sm" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input maxlength="10" required class="form-control" name="name" id="username">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input maxlength="50" required type="email" class="form-control" name="email" id="email">
            <small class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input maxlength="20" required type="password" class="form-control" name="password" id="password">
            <small class="form-text text-muted">Password must be 8-20 characters long and contain letters and numbers.</small>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Create Account</button>
    </form>
    <div class="text-center mt-3">
        <p>Already have an account? <a href="login-page.php">Sign in</a></p>
    </div>
</div>

<!-- Footer -->
<footer class="bg-success mt-auto text-center py-3">
    <span class="text-white">Optimal Lifestyle &copy; <?= date('Y') ?></span>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>