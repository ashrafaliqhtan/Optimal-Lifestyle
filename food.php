<?php
require_once 'db_connection.php';
session_start();

// Security checks
if (!isset($_SESSION['user_id'])) {
    header("Location: login-page.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle form submission with prepared statements
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validate and sanitize input
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING) ?? '';
    $calories = filter_input(INPUT_POST, 'calories', FILTER_VALIDATE_INT, 
        ['options' => ['min_range' => 1]]);

    if (!empty($name) && $calories) {
        $stmt = $connection->prepare("INSERT INTO Food (user_id, Food_name, calorie_amount) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $user_id, $name, $calories);
        
        if (!$stmt->execute()) {
            $error_message = "Error adding food item: " . $connection->error;
        } else {
            $_SESSION['success_message'] = "Food item added successfully!";
            header("Location: food.php");
            exit();
        }
    } else {
        $error_message = "Please provide valid food name and calorie amount (minimum 1).";
    }
}

// Fetch current food catalogue using prepared statement
$food_result = [];
$stmt = $connection->prepare("SELECT Food_id, Food_name, calorie_amount, created_at FROM Food WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$food_result = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage your personal food catalogue for diet tracking">
    <title>Food Catalogue | Optimal Lifestyle</title>
    
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
            --danger-color: #e53e3e;
            --danger-dark: #c53030;
            --light-bg: #f8fafc;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        body {
            background: linear-gradient(135deg, var(--light-bg) 0%, #e2e8f0 100%);
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
        }
        
        .navbar {
            background: var(--primary-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .food-form {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease;
        }
        
        .food-form:hover {
            transform: translateY(-5px);
        }
        
        .food-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
            box-shadow: var(--card-shadow);
        }
        
        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background: var(--primary-color);
            color: white;
            font-weight: 600;
        }
        
        .delete-btn {
            background: var(--danger-color);
            color: white;
            border: none;
            transition: all 0.2s ease;
        }
        
        .delete-btn:hover {
            background: var(--danger-dark);
        }
        
        .empty-state {
            opacity: 0.7;
        }
        
        .calorie-badge {
            font-size: 0.9rem;
            padding: 0.35em 0.65em;
        }
        
        .food-icon {
            font-size: 1.75rem;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
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
                        <a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="BMI-page.php"><i class="bi bi-calculator me-1"></i>BMI Calculator</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="articles-page.php"><i class="bi bi-newspaper me-1"></i>Articles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="food.php"><i class="bi bi-nutrition me-1"></i>Food Catalogue</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">

                    <a class="btn btn-outline-light" href="account-page.php">
                        <i class="bi bi-person-circle me-1"></i>My Account
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-4 flex-grow-1">
        <!-- Page Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-primary"><i class="bi bi-bookmark-heart me-2"></i>Food Catalogue</h1>
            <p class="lead text-muted">
                Track your frequently eaten foods to simplify calorie counting and diet management.
            </p>
        </div>

        <!-- Alerts -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Add Food Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="food-form p-4 mb-5">
                    <h3 class="text-center mb-4"><i class="bi bi-plus-circle me-2"></i>Add New Food Item</h3>
                    <form method="POST" id="foodForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Food Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-egg-fried"></i></span>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="e.g., Grilled Chicken" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="calories" class="form-label">Calories (per serving)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lightning"></i></span>
                                <input type="number" class="form-control" id="calories" name="calories" 
                                       min="1" placeholder="e.g., 250" required>
                                <span class="input-group-text">kcal</span>
                            </div>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-plus-lg me-2"></i>Add to Catalogue
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Food Catalogue -->
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-4"><i class="bi bi-bookmarks me-2"></i>Your Food Items</h2>
                
                <?php if (empty($food_result)): ?>
                    <div class="text-center py-5 empty-state">
                        <i class="bi bi-journal-x display-4 text-muted mb-3"></i>
                        <h3 class="text-muted">Your food catalogue is empty</h3>
                        <p class="text-muted">Add your frequently eaten foods to get started</p>
                    </div>
                <?php else: ?>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
                        <?php foreach ($food_result as $food): ?>
                            <div class="col">
                                <div class="card food-card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="bi bi-bookmark-check me-2"></i>
                                            <?= htmlspecialchars($food['Food_name'] ?? 'Unnamed Food') ?>
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="mb-1">Calories</h6>
                                                <span class="badge bg-primary rounded-pill calorie-badge">
                                                    <?= number_format($food['calorie_amount'] ?? 0) ?> kcal
                                                </span>
                                            </div>
                                            <i class="bi bi-<?= ($food['calorie_amount'] ?? 0) > 300 ? 'fire' : 'apple' ?> food-icon text-<?= ($food['calorie_amount'] ?? 0) > 300 ? 'danger' : 'success' ?>"></i>
                                        </div>
                                        <small class="text-muted">
                                            Added on <?= date('M j, Y', strtotime($food['created_at'] ?? 'now')) ?>
                                        </small>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <a href="delete_food.php?id=<?= $food['Food_id'] ?? '' ?>" 
                                           class="btn delete-btn w-100 py-2"
                                           onclick="return confirm('Are you sure you want to delete this food item?')">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-activity me-2"></i>Optimal Lifestyle</h5>
                    <p class="text-muted mb-0">Your partner in health and wellness.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; <?= date('Y') ?> Optimal Lifestyle. All rights reserved.</p>
                    <small class="text-muted">v1.0.0</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Focus on food name input
            document.getElementById('name').focus();
            
            // Confirm before deleting
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this food item?')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Form validation
            document.getElementById('foodForm').addEventListener('submit', function(e) {
                const name = document.getElementById('name').value.trim();
                const calories = document.getElementById('calories').value;
                
                if (name === '') {
                    alert('Please enter a food name');
                    e.preventDefault();
                    return false;
                }
                
                if (calories < 1) {
                    alert('Please enter a valid calorie amount (minimum 1)');
                    e.preventDefault();
                    return false;
                }
                
                return true;
            });
        });
    </script>
</body>
</html>