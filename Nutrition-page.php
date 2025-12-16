<?php
session_start();
require_once 'config.php'; // Ensure this file contains your database connection details

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login-page.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle form submission for adding food
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_food'])) {
    $food_name = trim($_POST['food_name']);
    $calorie_amount = (int)$_POST['calorie_amount'];

    if (!empty($food_name) && $calorie_amount > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO Food (user_id, food_name, calorie_amount) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $food_name, $calorie_amount]);
            $success_message = "Food item added successfully!";
        } catch (PDOException $e) {
            $error_message = "Error adding food item: " . $e->getMessage();
        }
    } else {
        $error_message = "Please provide valid food name and calorie amount.";
    }
}

// Fetch user's food entries
try {
    $stmt = $pdo->prepare("SELECT * FROM Food WHERE user_id = ? ORDER BY food_id DESC");
    $stmt->execute([$user_id]);
    $food_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching food entries: " . $e->getMessage();
}

// Calculate total calories
$total_calories = 0;
if (!empty($food_entries)) {
    foreach ($food_entries as $entry) {
        $total_calories += $entry['calorie_amount'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition Tracker - Optimal Lifestyle</title>
    <link rel="stylesheet" href="Styles/index-styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- NavBar -->
<nav class="navbar navbar-expand-lg bg-success br-gradient">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Optimal Lifestyle</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="BMI-page.php">BMI Calculator</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="articles-page.php">Helpful Articles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="Nutrition-page.php">Nutrition Tracker</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link fw-bold" href="account-page.php">My Account</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mt-5">
    <h1 class="text-center mb-4">Nutrition Tracker</h1>

    <!-- Add Food Form -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="card-title">Add Food Entry</h2>
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="food_name" class="form-label">Food Name</label>
                    <input type="text" class="form-control" id="food_name" name="food_name" required>
                </div>
                <div class="mb-3">
                    <label for="calorie_amount" class="form-label">Calorie Amount</label>
                    <input type="number" class="form-control" id="calorie_amount" name="calorie_amount" required min="1">
                </div>
                <button type="submit" name="add_food" class="btn btn-success">Add Food</button>
            </form>
        </div>
    </div>

    <!-- Food Entries Table -->
    <div class="card">
        <div class="card-body">
            <h2 class="card-title">Your Food Entries</h2>
            <?php if (empty($food_entries)): ?>
                <p class="text-muted">No food entries found. Start logging your meals!</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Food Name</th>
                                <th>Calories</th>
                                <th>Date Added</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($food_entries as $entry): ?>
                                <tr>
                                    <td><?= htmlspecialchars($entry['food_name']) ?></td>
                                    <td><?= htmlspecialchars($entry['calorie_amount']) ?> kcal</td>
                                    <td><?= date('Y-m-d H:i', strtotime($entry['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <h4 class="mt-4">Total Calories: <?= $total_calories ?> kcal</h4>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-success mt-5 py-3 text-white">
    <div class="container-fluid text-center">
        <p>&copy; <?= date('Y') ?> Optimal Lifestyle. All rights reserved.</p>
        <p>Contact us: <a href="mailto:support@Optimal Lifestyle.com" class="text-white">support@Optimal Lifestyle.com</a></p>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>