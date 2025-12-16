<?php
session_start();
require_once 'config.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login-page.php");
    exit();
}

// Secure session handling
$user_id = $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES, 'UTF-8');

// Fetch user's food entries with prepared statements
$food_entries = [];
$calorie_entries = [];
$total_calories = 0;

try {
    // Get food catalogue
    $stmt = $pdo->prepare("SELECT * FROM Food WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $food_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get calorie entries and calculate total
    $stmt = $pdo->prepare("SELECT * FROM CalorieCalculator WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $calorie_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total_calories = array_sum(array_column($calorie_entries, 'calorie_amount'));
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error_message = "A database error occurred. Please try again later.";
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Invalid form submission.";
    } else {
        // Add calorie entry
        if (isset($_POST['submit'])) {
            $food_name = trim(filter_input(INPUT_POST, 'food_name', FILTER_SANITIZE_STRING));
            $calorie_amount = filter_input(INPUT_POST, 'calorie_amount', FILTER_VALIDATE_INT, 
                ['options' => ['min_range' => 1]]);

            if ($food_name && $calorie_amount) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO CalorieCalculator (user_id, food_name, calorie_amount) VALUES (?, ?, ?)");
                    $stmt->execute([$user_id, $food_name, $calorie_amount]);
                    $_SESSION['success_message'] = "Calorie entry added successfully!";
                    header("Location: calorie-calculator.php");
                    exit();
                } catch (PDOException $e) {
                    error_log("Insert error: " . $e->getMessage());
                    $error_message = "Failed to add calorie entry. Please try again.";
                }
            } else {
                $error_message = "Please provide valid food name and calorie amount (minimum 1).";
            }
        }
    }
}

// Handle deletion of calorie entry
if (isset($_GET['delete'])) {
    $calorie_id = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    
    if ($calorie_id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM CalorieCalculator WHERE calorie_id = ? AND user_id = ?");
            $stmt->execute([$calorie_id, $user_id]);
            $_SESSION['success_message'] = "Calorie entry deleted successfully!";
            header("Location: calorie-calculator.php");
            exit();
        } catch (PDOException $e) {
            error_log("Delete error: " . $e->getMessage());
            $error_message = "Failed to delete calorie entry. Please try again.";
        }
    } else {
        $error_message = "Invalid entry ID.";
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Track and manage your daily calorie intake with Optimal Lifestyle">
    <title>Calorie Tracker | Optimal Lifestyle</title>
    
    <!-- Favicon -->
    <link rel="icon" href="Styles/pictures/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-dark">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
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
                        <a class="nav-link active" href="calorie-calculator.php"><i class="bi bi-nutrition me-1"></i>Calorie Tracker</a>
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
    <main class="container my-4 flex-grow-1">
        <!-- Page Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-success"><i class="bi bi-nutrition me-2"></i>Calorie Tracker</h1>
            <p class="lead text-muted">
                Monitor your daily calorie intake to maintain a healthy diet and lifestyle.
            </p>
        </div>

        <!-- Alerts -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Add Calorie Entry -->
            <div class="col-lg-5">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h2 class="h5 mb-0"><i class="bi bi-plus-circle me-2"></i>Add New Entry</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            
                            <div class="mb-3">
                                <label for="food_name" class="form-label">Food Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-egg-fried"></i></span>
                                    <input type="text" class="form-control" id="food_name" name="food_name" required
                                           placeholder="e.g., Grilled Chicken">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="calorie_amount" class="form-label">Calories</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lightning-charge"></i></span>
                                    <input type="number" class="form-control" id="calorie_amount" name="calorie_amount" 
                                           required min="1" placeholder="e.g., 250">
                                    <span class="input-group-text">kcal</span>
                                </div>
                            </div>
                            
                            <button type="submit" name="submit" class="btn btn-success w-100">
                                <i class="bi bi-plus-lg me-2"></i>Add Entry
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Daily Summary -->
            <div class="col-lg-7">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h5 mb-0"><i class="bi bi-graph-up me-2"></i>Daily Summary</h2>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="display-4 fw-bold text-primary"><?= $total_calories ?></div>
                                <div class="text-muted">Total Calories</div>
                            </div>
                            <div class="col-md-6">
                                <div class="display-4 fw-bold text-success"><?= count($calorie_entries) ?></div>
                                <div class="text-muted">Food Entries</div>
                            </div>
                        </div>
                        
                        <!-- Progress bar for daily goal (example with 2000 kcal goal) -->
                        <?php $daily_goal = 2000; ?>
                        <div class="mt-4">
                            <div class="d-flex justify-content-between mb-2">
                                <small>0 kcal</small>
                                <small><?= $daily_goal ?> kcal daily goal</small>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: <?= min(100, ($total_calories / $daily_goal) * 100) ?>%" 
                                     aria-valuenow="<?= $total_calories ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="<?= $daily_goal ?>">
                                    <?= round(($total_calories / $daily_goal) * 100) ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Food Entries -->
        <div class="row mt-4 g-4">
            <!-- Today's Entries -->
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h2 class="h5 mb-0"><i class="bi bi-list-check me-2"></i>Today's Food Log</h2>
                    </div>
                    <div class="card-body">
                        <?php if (empty($calorie_entries)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-journal-x display-4 text-muted"></i>
                                <p class="mt-3 text-muted">No entries yet. Add your first food item!</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Food</th>
                                            <th class="text-end">Calories</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($calorie_entries as $entry): ?>
                                            <tr>
                                                <td>
                                                    <i class="bi bi-egg-fried text-info me-2"></i>
                                                    <?= htmlspecialchars($entry['food_name']) ?>
                                                </td>
                                                <td class="text-end fw-bold">
                                                    <?= number_format($entry['calorie_amount']) ?> kcal
                                                </td>
                                                <td class="text-end">
                                                    <a href="?delete=<?= $entry['calorie_id'] ?>" 
                                                       class="btn btn-sm btn-outline-danger"
                                                       onclick="return confirm('Are you sure you want to delete this entry?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-group-divider">
                                        <tr class="fw-bold">
                                            <td>Total</td>
                                            <td class="text-end"><?= number_format($total_calories) ?> kcal</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Food Catalogue -->
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h2 class="h5 mb-0"><i class="bi bi-bookmark-heart me-2"></i>Your Food Catalogue</h2>
                    </div>
                    <div class="card-body">
                        <?php if (empty($food_entries)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-journal-plus display-4 text-muted"></i>
                                <p class="mt-3 text-muted">Your food catalogue is empty. Add frequently eaten items!</p>
                                <a href="food.php" class="btn btn-outline-warning">
                                    <i class="bi bi-plus-lg me-1"></i>Add Food Items
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($food_entries as $entry): ?>
                                    <form method="POST" class="list-group-item list-group-item-action">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="food_name" value="<?= htmlspecialchars($entry['food_name']) ?>">
                                        <input type="hidden" name="calorie_amount" value="<?= htmlspecialchars($entry['calorie_amount']) ?>">
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-bookmark-check text-warning me-2"></i>
                                                <strong><?= htmlspecialchars($entry['food_name']) ?></strong>
                                                <span class="text-muted ms-2"><?= number_format($entry['calorie_amount']) ?> kcal</span>
                                            </div>
                                            <button type="submit" name="submit" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-plus-lg"></i> Add
                                            </button>
                                        </div>
                                    </form>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-3 text-center">
                                <a href="food.php" class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-pencil me-1"></i>Edit Catalogue
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-activity me-2"></i>Optimal Lifestyle</h5>
                    <p class="text-muted">Your partner in health and wellness.</p>
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
        // Enable tooltips
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // Auto-focus on food name input
        $(document).ready(function() {
            $('#food_name').focus();
        });

        // Confirm before deleting
        $(document).on('click', '.delete-btn', function() {
            return confirm('Are you sure you want to delete this entry?');
        });
    </script>
</body>
</html>