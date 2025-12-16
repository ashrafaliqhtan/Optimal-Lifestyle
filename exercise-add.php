<?php
session_start();
require_once 'config.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login-page.php");
    exit();
}

// Check if fitness_id is provided and valid
if (!isset($_GET['fitness_id']) || !is_numeric($_GET['fitness_id'])) {
    header("Location: fitness.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$fitness_id = intval($_GET['fitness_id']);

// Initialize variables
$error_message = '';
$success_message = '';
$exercise_types = ['Running', 'Cycling', 'Swimming', 'Weight Lifting', 'Yoga', 'Push-ups', 'Sit-ups', 'Squats', 'Jump Rope', 'Walking'];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $exercise_type = trim($_POST['exercise_type']);
        $amount = intval($_POST['amount']);
        $time = $_POST['time'];

        // Validate inputs
        if (empty($exercise_type)) {
            throw new Exception("Exercise type is required");
        }
        if ($amount <= 0) {
            throw new Exception("Amount must be a positive number");
        }
        if (empty($time)) {
            throw new Exception("Time is required");
        }

        // Insert new exercise
        $stmt = $pdo->prepare("
            INSERT INTO Exercise (exercise_type, amount, time, fitness_id)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$exercise_type, $amount, $time, $fitness_id]);

        $success_message = "Exercise added successfully!";
        
        // Clear form inputs
        $_POST = [];
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Verify the fitness record belongs to the user
try {
    $stmt = $pdo->prepare("
        SELECT day FROM FitnessRecords 
        WHERE fitness_id = ? AND user_id = ?
    ");
    $stmt->execute([$fitness_id, $user_id]);
    $day_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$day_info) {
        throw new Exception("Workout plan not found or you don't have permission to add exercises");
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Exercise - FitnessTracker</title>
    
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
        
        .container {
            margin-top: 3rem;
            padding-bottom: 3rem;
        }
        
        .card {
            border: none;
            background-color: var(--bg-transparent);
            color: var(--text-dark);
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
        
        .form-label {
            font-weight: 600;
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
                    <h2 class="text-center mb-4">Add Exercise to Day <?= isset($day_info['day']) ? htmlspecialchars($day_info['day']) : '' ?></h2>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($error_message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($success_message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <div class="mt-2">
                                <a href="fitness-day.php?id=<?= $fitness_id ?>" class="btn btn-sm btn-outline-success me-2">
                                    View Exercises
                                </a>
                                <a href="exercise-add.php?fitness_id=<?= $fitness_id ?>" class="btn btn-sm btn-success">
                                    Add Another
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="exercise-add.php?fitness_id=<?= $fitness_id ?>">
                            <div class="mb-3">
                                <label for="exercise_type" class="form-label">Exercise Type</label>
                                <select class="form-select" id="exercise_type" name="exercise_type" required>
                                    <option value="">Select an exercise type</option>
                                    <?php foreach ($exercise_types as $type): ?>
                                        <option value="<?= htmlspecialchars($type) ?>" <?= isset($_POST['exercise_type']) && $_POST['exercise_type'] === $type ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($type) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       min="1" required 
                                       value="<?= isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : '' ?>">
                                <div class="form-text">Number of reps, minutes, or other appropriate measure</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="time" class="form-label">Time</label>
                                <input type="time" class="form-control" id="time" name="time" 
                                       required step="1"
                                       value="<?= isset($_POST['time']) ? htmlspecialchars($_POST['time']) : '' ?>">
                                <div class="form-text">Duration of the exercise (HH:MM:SS)</div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="fitness-day.php?id=<?= $fitness_id ?>" class="btn btn-outline-secondary me-md-2">
                                    <i class="bi bi-arrow-left"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-custom">
                                    <i class="bi bi-plus-circle"></i> Add Exercise
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