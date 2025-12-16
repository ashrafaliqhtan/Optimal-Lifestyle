<?php  
session_start();  
require_once 'config.php';  

// Redirect to login if user is not logged in  
if (!isset($_SESSION['user_id'])) {  
    header("Location: login-page.php");  
    exit();  
}  

$user_id = intval($_SESSION['user_id']);  // Ensure user_id is an integer

// Fetch fitness schedules for the logged-in user with proper error handling
$days_result = [];
$workout_stats = ['total_workouts' => 0, 'total_exercises' => 0];

try {
    $pdo->beginTransaction();
    
    // Get fitness schedules
    $stmt = $pdo->prepare("
        SELECT fr.*, COUNT(e.exercise_id) AS exercise_count 
        FROM FitnessRecords fr
        LEFT JOIN Exercise e ON fr.fitness_id = e.fitness_id
        WHERE fr.user_id = ? 
        GROUP BY fr.fitness_id
        ORDER BY fr.day ASC
    ");
    $stmt->execute([$user_id]);
    $days_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get workout statistics
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) AS total_workouts,
            SUM(
                (SELECT COUNT(*) FROM Exercise e WHERE e.fitness_id = fr.fitness_id)
            ) AS total_exercises
        FROM FitnessRecords fr
        WHERE fr.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $workout_stats = $stmt->fetch(PDO::FETCH_ASSOC) ?: $workout_stats;
    
    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Database error: " . $e->getMessage());
    // Continue execution with empty results but show error to user
    $error_message = "Could not load fitness data. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Planner - OptimalLifestyle</title>
    
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
            height: 100%;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .card-title {
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .footer {
            background-color: var(--primary-color);
            padding: 1rem 0;
            text-align: center;
            margin-top: auto;
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
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .add-plan-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .add-plan-card:hover {
            background-color: rgba(40, 167, 69, 0.1);
        }
        
        .bi-plus-circle {
            transition: transform 0.3s ease;
        }
        
        .add-plan-card:hover .bi-plus-circle {
            transform: scale(1.2);
        }
        
        @media (max-width: 768px) {
            .container {
                margin-top: 2rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">OptimalLifestyle</a>
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
    <main class="container text-center">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <h1 class="mb-3">Fitness Planner</h1>
        <p class="lead mb-4">"Strength does not come from winning. Your struggles develop your strengths."</p>

        <!-- Workout Stats -->
        <div class="row my-4 g-3">
            <div class="col-md-6">
                <div class="card p-4">
                    <h5 class="card-title">Total Workouts</h5>
                    <p class="stat-number"><?= htmlspecialchars((string) $workout_stats['total_workouts']) ?></p>
                    <p class="text-muted">Keep up the good work!</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-4">
                    <h5 class="card-title">Total Exercises</h5>
                    <p class="stat-number"><?= htmlspecialchars((string) $workout_stats['total_exercises']) ?></p>
                    <p class="text-muted">Every rep counts!</p>
                </div>
            </div>
        </div>

        <!-- Fitness Schedule -->
        <h2 class="my-4">Your Fitness Schedule</h2>
        
        <?php if (empty($days_result)): ?>
            <div class="alert alert-info">
                You don't have any workout plans yet. Create your first plan to get started!
            </div>
        <?php endif; ?>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($days_result as $schedule): ?>
                <div class="col">
                    <div class="card p-3 h-100">
                        <div class="card-body">
                            <h5 class="card-title">Day <?= htmlspecialchars($schedule['day']) ?></h5>
                            <p class="card-text">
                                <span class="badge bg-primary rounded-pill">
                                    <?= htmlspecialchars($schedule['exercise_count']) ?> exercises
                                </span>
                            </p>
                            <p class="card-text text-muted">
                                Created: <?= date('M j, Y', strtotime($schedule['created_at'])) ?>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="fitness-day.php?id=<?= htmlspecialchars($schedule['fitness_id']) ?>" 
                               class="btn btn-custom w-100">
                                <i class="bi bi-arrow-right-circle"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Add New Plan -->
            <div class="col">
                <div class="card p-3 h-100">
                    <a href="fitness-creation.php" class="add-plan-card text-decoration-none text-success p-4">
                        <i class="bi bi-plus-circle" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Add New Plan</h5>
                        <small class="text-muted">Click to create a new workout plan</small>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> OptimalLifestyle. All rights reserved.</p>
            <p class="mb-0">Contact: <a href="mailto:support@OptimalLifestyle.com" class="text-light">support@OptimalLifestyle.com</a></p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Add animation to cards on page load
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
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