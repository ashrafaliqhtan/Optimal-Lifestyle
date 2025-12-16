<?php
session_start();
require_once 'config.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login-page.php");
    exit();
}

// Check if fitness_id is provided and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: fitness.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$fitness_id = intval($_GET['id']);

// Initialize variables
$error_message = '';
$success = false;
$day_info = [];

try {
    // Verify the fitness record belongs to the user
    $stmt = $pdo->prepare("
        SELECT day FROM FitnessRecords 
        WHERE fitness_id = ? AND user_id = ?
    ");
    $stmt->execute([$fitness_id, $user_id]);
    $day_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$day_info) {
        throw new Exception("Workout plan not found or you don't have permission to delete it.");
    }

    // Check if this is a confirmation request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pdo->beginTransaction();
        
        // First delete all exercises associated with this fitness plan
        $stmt = $pdo->prepare("DELETE FROM Exercise WHERE fitness_id = ?");
        $stmt->execute([$fitness_id]);
        
        // Then delete the fitness record itself
        $stmt = $pdo->prepare("DELETE FROM FitnessRecords WHERE fitness_id = ? AND user_id = ?");
        $stmt->execute([$fitness_id, $user_id]);
        
        $pdo->commit();
        $success = true;
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $error_message = $e->getMessage();
}

// If deletion was successful, redirect to fitness page
if ($success) {
    $_SESSION['success_message'] = "Workout plan for Day {$day_info['day']} has been deleted successfully.";
    header("Location: fitness.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Workout Plan - FitnessTracker</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #28a745;
            --danger-color: #dc3545;
            --text-light: #f8f9fa;
            --text-dark: #212529;
            --bg-transparent: rgba(255, 255, 255, 0.9);
        }
        
        body {
            font-family: 'Lato', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url("pictures/wallpaper/wallpaper1.jpg") center/cover fixed;
            color: var(--text-light);
            min-height: 100vh;
        }
        
        .navbar {
            background-color: rgba(40, 167, 69, 0.9);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .container {
            margin-top: 3rem;
            padding-bottom: 3rem;
        }
        
        .card {
            border: none;
            background-color: var(--bg-transparent);
            color: var(--text-dark);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">FitnessTracker</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="BMI-page.php">BMI Calculator</a></li>
                    <li class="nav-item"><a class="nav-link" href="articles-page.php">Articles</a></li>
                    <li class="nav-item"><a class="nav-link active" href="fitness.php">Fitness Planner</a></li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-light me-3 d-none d-sm-inline">Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                    <a class="btn btn-light btn-sm" href="account-page.php">
                        <i class="bi bi-person-circle"></i> My Account
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card p-4">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($error_message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <div class="mt-2">
                                <a href="fitness.php" class="btn btn-sm btn-outline-danger">
                                    Back to Fitness Planner
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <h2 class="text-center mb-4">Delete Workout Plan</h2>
                        <div class="alert alert-warning">
                            <h5 class="alert-heading">Warning!</h5>
                            <p>You are about to delete the workout plan for <strong>Day <?= htmlspecialchars($day_info['day']) ?></strong> and all its associated exercises. This action cannot be undone.</p>
                            <hr>
                            <p class="mb-0">Are you sure you want to proceed?</p>
                        </div>
                        
                        <form method="POST" action="fitness-delete.php?id=<?= $fitness_id ?>">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="fitness-day.php?id=<?= $fitness_id ?>" class="btn btn-outline-secondary me-md-2">
                                    <i class="bi bi-arrow-left"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash"></i> Confirm Delete
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container">
            <p>&copy; <?= date('Y') ?> FitnessTracker. All rights reserved.</p>
            <p class="mb-0">Contact: <a href="mailto:support@fitnesstracker.com" class="text-light">support@fitnesstracker.com</a></p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>