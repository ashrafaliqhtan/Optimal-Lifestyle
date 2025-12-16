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
$day_info = [];
$exercises = [];
$error_message = '';

try {
    $pdo->beginTransaction();
    
    // Verify the fitness record belongs to the user and get day info
    $stmt = $pdo->prepare("
        SELECT fr.day, fr.created_at 
        FROM FitnessRecords fr
        WHERE fr.fitness_id = ? AND fr.user_id = ?
    ");
    $stmt->execute([$fitness_id, $user_id]);
    $day_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$day_info) {
        throw new Exception("Workout plan not found or you don't have permission to view it.");
    }
    
    // Get all exercises for this fitness day
    $stmt = $pdo->prepare("
        SELECT e.exercise_id, e.exercise_type, e.amount, e.time
        FROM Exercise e
        JOIN FitnessRecords fr ON e.fitness_id = fr.fitness_id
        WHERE e.fitness_id = ? AND fr.user_id = ?
        ORDER BY e.exercise_id ASC
    ");
    $stmt->execute([$fitness_id, $user_id]);
    $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    $error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day <?= htmlspecialchars($day_info['day'] ?? '') ?> - FitnessTracker</title>
    
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
            --primary-hover: #218838;
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
        
        .navbar-brand, .nav-link {
            color: var(--text-light) !important;
            transition: color 0.3s ease;
        }
        
        .navbar-brand:hover, .nav-link:hover {
            color: #d4d4d4 !important;
        }
        
        .container {
            margin-top: 3rem;
            padding-bottom: 3rem;
        }
        
        .card {
            border: none;
            transition: all 0.3s ease;
            background-color: var(--bg-transparent);
            color: var(--text-dark);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            font-weight: bold;
        }
        
        .exercise-card {
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .btn-custom {
            background-color: var(--primary-color);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-custom:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }
        
        .btn-outline-custom {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline-custom:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .badge-custom {
            background-color: var(--primary-color);
        }
        
        .footer {
            background-color: var(--primary-color);
            padding: 1rem 0;
            text-align: center;
            margin-top: auto;
        }
        
        @media (max-width: 768px) {
            .container {
                margin-top: 2rem;
            }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

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
        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <a href="fitness.php" class="btn btn-sm btn-outline-danger ms-2">Back to Fitness Planner</a>
            </div>
        <?php else: ?>
            <!-- Day Header -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Day <?= htmlspecialchars($day_info['day']) ?></h2>
                    <div>
                        <a href="fitness-edit.php?id=<?= $fitness_id ?>" class="btn btn-sm btn-outline-light me-2">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="fitness-delete.php?id=<?= $fitness_id ?>" class="btn btn-sm btn-outline-light" onclick="return confirm('Are you sure you want to delete this workout plan?');">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        <small>Created: <?= date('M j, Y', strtotime($day_info['created_at'])) ?></small>
                    </p>
                </div>
            </div>
            
            <!-- Exercises Section -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Exercises</h3>
                <a href="exercise-add.php?fitness_id=<?= $fitness_id ?>" class="btn btn-custom">
                    <i class="bi bi-plus-circle"></i> Add Exercise
                </a>
            </div>
            
            <?php if (empty($exercises)): ?>
                <div class="alert alert-info">
                    No exercises added yet. Click "Add Exercise" to get started!
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($exercises as $exercise): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card exercise-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h4 class="card-title"><?= htmlspecialchars($exercise['exercise_type']) ?></h4>
                                        <div>
                                            <a href="exercise-edit.php?id=<?= $exercise['exercise_id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="exercise-delete.php?id=<?= $exercise['exercise_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this exercise?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6">
                                            <p class="mb-1"><strong>Amount:</strong> <?= htmlspecialchars($exercise['amount']) ?></p>
                                        </div>
                                        <div class="col-6">
                                            <p class="mb-1"><strong>Time:</strong> <?= htmlspecialchars($exercise['time']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <a href="fitness.php" class="btn btn-outline-light me-md-2">
                    <i class="bi bi-arrow-left"></i> Back to Planner
                </a>
            </div>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> FitnessTracker. All rights reserved.</p>
            <p class="mb-0">Contact: <a href="mailto:support@fitnesstracker.com" class="text-light">support@fitnesstracker.com</a></p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Simple animation for exercise cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.exercise-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        });
    </script>
</body>
</html>