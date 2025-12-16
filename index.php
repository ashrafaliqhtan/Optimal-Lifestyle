<?php
session_start();
require_once 'config.php';

// Redirect to login if not authenticated (optional for home page)
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login-page.php");
//     exit();
// }

$user_name = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') : 'Guest';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Optimal Lifestyle - Your complete health and fitness companion">
    <title>Home | Optimal Lifestyle</title>
    
    <!-- Favicon -->
    <link rel="icon" href="Styles/pictures/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #48bb78;
            --primary-dark: #38a169;
            --light-bg: #f8f9fa;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', system-ui, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .navbar {
            background: var(--primary-color) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .carousel-item {
            height: 400px;
            background-size: cover;
            background-position: center;
        }
        
        .carousel-item:nth-child(1) {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('Styles/pictures/carousel/nature.jpg');
        }
        
        .carousel-item:nth-child(2) {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('Styles/pictures/carousel/fruits.jpg');
        }
        
        .carousel-item:nth-child(3) {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('Styles/pictures/carousel/vegetables.jpg');
        }
        
        .feature-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .feature-card img {
            height: 200px;
            object-fit: cover;
        }
        
        .feature-card .btn {
            width: auto;
            padding: 0.5rem 1.5rem;
        }
        
        footer {
            background: var(--primary-color);
            color: white;
            margin-top: auto;
        }
        
        .carousel-caption {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 8px;
            padding: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <i class="bi bi-activity me-2"></i>Optimal Lifestyle
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php"><i class="bi bi-house-door me-1"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="BMI-page.php"><i class="bi bi-calculator me-1"></i>BMI Calculator</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="articles-page.php"><i class="bi bi-newspaper me-1"></i>Articles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Fitness/index.php"><i class="bi bi-clipboard2-pulse me-1"></i>Fitness Management</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="text-light me-3 d-none d-sm-inline">Welcome, <?= $user_name ?></span>
                        <a class="btn btn-outline-light" href="account-page.php">
                            <i class="bi bi-person-circle me-1"></i>Account
                        </a>
                    <?php else: ?>
                        <a class="btn btn-outline-light" href="login-page.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Carousel -->
    <div class="container mt-4">
        <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="2"></button>
            </div>
            <div class="carousel-inner rounded-3">
                <div class="carousel-item active">
                    <div class="carousel-caption">
                        <h5>Healthy Nature</h5>
                        <p>Connect with nature to enhance your wellbeing and mental health</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="carousel-caption">
                        <h5>Fresh Fruits</h5>
                        <p>Incorporate a variety of fruits for essential vitamins and nutrients</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="carousel-caption">
                        <h5>Nutritious Vegetables</h5>
                        <p>Vegetables are the foundation of a healthy, balanced diet</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Your Health & Fitness Tools</h2>
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <div class="col">
                <div class="card h-100 feature-card">
                    <img src="Styles/pictures/cards/BMI2.jpg" class="card-img-top" alt="BMI Calculator">
                    <div class="card-body">
                        <h5 class="card-title">BMI Calculator</h5>
                        <p class="card-text">Calculate your Body Mass Index to understand your weight status and potential health risks.</p>
                        <a href="BMI-page.php" class="btn btn-success">
                            <i class="bi bi-calculator me-1"></i>Calculate Now
                        </a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 feature-card">
                    <img src="Styles/pictures/cards/Calories2.jpg" class="card-img-top" alt="Calorie Tracker">
                    <div class="card-body">
                        <h5 class="card-title">Calorie Tracker</h5>
                        <p class="card-text">Monitor your daily calorie intake and maintain optimal nutrition for your goals.</p>
                        <a href="Calories-page.php" class="btn btn-success">
                            <i class="bi bi-lightning-charge me-1"></i>Track Calories
                        </a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 feature-card">
                    <img src="Styles/pictures/cards/Fitness.jpg" class="card-img-top" alt="Fitness Planner">
                    <div class="card-body">
                        <h5 class="card-title">Fitness Planner</h5>
                        <p class="card-text">Create personalized workout routines tailored to your fitness level and objectives.</p>
                        <a href="fitness.php" class="btn btn-success">
                            <i class="bi bi-clipboard2-pulse me-1"></i>View Plans
                        </a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 feature-card">
                    <img src="Styles/pictures/cards/FoodIcon.jpg" class="card-img-top" alt="Nutrition Tracker">
                    <div class="card-body">
                        <h5 class="card-title">Nutrition Tracker</h5>
                        <p class="card-text">Log and analyze your food consumption to maintain a balanced, healthy diet.</p>
                        <a href="Nutrition-page.php" class="btn btn-success">
                            <i class="bi bi-egg-fried me-1"></i>Track Food
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="bg-primary bg-opacity-10 py-5">
        <div class="container text-center">
            <h2 class="mb-3">Start Your Health Journey Today</h2>
            <p class="lead mb-4">Join thousands of users who have transformed their lives with Optimal Lifestyle</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="account-page.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                </a>
            <?php else: ?>
                <a href="register-page.php" class="btn btn-primary btn-lg me-2">
                    <i class="bi bi-person-plus me-2"></i>Sign Up
                </a>
                <a href="login-page.php" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-activity me-2"></i>Optimal Lifestyle</h5>
                    <p class="mb-0">Your partner in health and wellness.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; <?= date('Y') ?> Optimal Lifestyle. All rights reserved.</p>
                    <small>
                        <a href="mailto:support@optimal-lifestyle.com" class="text-white text-decoration-none">
                            <i class="bi bi-envelope me-1"></i>support@optimal-lifestyle.com
                        </a>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Initialize carousel with auto-cycling
        document.addEventListener('DOMContentLoaded', function() {
            const myCarousel = new bootstrap.Carousel('#mainCarousel', {
                interval: 5000,
                wrap: true,
                pause: 'hover'
            });
        });
    </script>
</body>
</html>