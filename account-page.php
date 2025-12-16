<?php
require_once "config.php";
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login-page.php");
    exit();
}

// Secure session handling
$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'Guest', ENT_QUOTES, 'UTF-8');
$display_name = htmlspecialchars($_COOKIE['user_name'] ?? $user_name, ENT_QUOTES, 'UTF-8');

// CSRF protection for logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);
    
    // Clear session data
    $_SESSION = array();
    
    // Destroy the session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    
    // Clear cookies
    setcookie('user_id', '', time() - 3600, '/', '', true, true);
    setcookie('user_name', '', time() - 3600, '/', '', true, true);
    
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage your fitness account, track progress, and plan workouts">
    <title>My Account | OptimalLifestyle</title>
    
    <!-- Favicon -->
    <link rel="icon" href="Styles/pictures/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->

    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-gradient">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <i class="bi bi-activity me-2"></i>OptimalLifestyle
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="BMI-page.php"><i class="bi bi-calculator me-1"></i>BMI Calculator</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="articles-page.php"><i class="bi bi-newspaper me-1"></i>Articles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="fitness.php"><i class="bi bi-calendar-check me-1"></i>Fitness Planner</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-light me-3 d-none d-sm-inline">
                        <i class="bi bi-person-circle me-1"></i><?= $user_name ?>
                    </span>
                    <a class="btn btn-outline-light btn-sm" href="account-page.php">
                        <i class="bi bi-gear me-1"></i>Account
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-5 flex-grow-1">
        <!-- Welcome Section -->
        <section class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-3"><i class="bi bi-person-circle me-2"></i>Personal Account</h1>
            <p class="lead text-muted">Welcome back, <span class="text-primary fw-bold"><?= $display_name ?></span></p>
        </section>

        <!-- Features Grid -->
        <section class="row g-4 justify-content-center">
            <!-- Calorie Calculator Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 overflow-hidden">
                    <div class="card-img-top bg-dark" style="height: 180px; background-image: url('Styles/pictures/cards/Calories2.jpg'); background-size: cover; background-position: center;">
                        <div class="d-flex align-items-end h-100 p-3">
                            <span class="badge bg-primary"><i class="bi bi-calculator me-1"></i>Calculator</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-lightning-charge text-warning me-2"></i>Calorie Calculator</h5>
                        <hr class="my-2">
                        <p class="card-text text-muted">Calculate your daily calorie needs and track your nutritional intake.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="calorie-calculator.php" class="btn btn-primary stretched-link">
                            <i class="bi bi-arrow-right me-1"></i>Get Started
                        </a>
                    </div>
                </div>
            </div>

            <!-- Fitness Planner Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 overflow-hidden">
                    <div class="card-img-top bg-dark" style="height: 180px; background-image: url('Styles/pictures/cards/Fitness.jpg'); background-size: cover; background-position: center;">
                        <div class="d-flex align-items-end h-100 p-3">
                            <span class="badge bg-success"><i class="bi bi-calendar-check me-1"></i>Planner</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-activity text-success me-2"></i>Fitness Planner</h5>
                        <hr class="my-2">
                        <p class="card-text text-muted">Create and manage your personalized workout routines and schedules.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="fitness.php" class="btn btn-success stretched-link">
                            <i class="bi bi-arrow-right me-1"></i>Plan Workouts
                        </a>
                    </div>
                </div>
            </div>

            <!-- Food Catalogue Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 overflow-hidden">
                    <div class="card-img-top bg-dark" style="height: 180px; background-image: url('Styles/pictures/cards/FoodIcon.jpg'); background-size: cover; background-position: center;">
                        <div class="d-flex align-items-end h-100 p-3">
                            <span class="badge bg-info"><i class="bi bi-journal-bookmark me-1"></i>Catalogue</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-egg-fried text-info me-2"></i>Food Catalogue</h5>
                        <hr class="my-2">
                        <p class="card-text text-muted">Track your meals and create a personalized food database.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="food.php" class="btn btn-info stretched-link">
                            <i class="bi bi-arrow-right me-1"></i>View Catalogue
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Logout Section -->
        <section class="text-center mt-5">
            <form method="POST" class="d-inline-block">
                <button type="submit" name="logout" class="btn btn-outline-danger px-4 py-2">
                    <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                </button>
            </form>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-activity me-2"></i>OptimalLifestyle</h5>
                    <p class="text-muted">Your personal health and fitness companion.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; <?= date('Y') ?> Optimal Lifestyle. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Enable Bootstrap tooltips
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip()
        });
        
        // Add active class to current nav item
        $(document).ready(function() {
            $('.nav-link').each(function() {
                if (this.href === window.location.href) {
                    $(this).addClass('active');
                }
            });
        });
    </script>
</body>
</html>