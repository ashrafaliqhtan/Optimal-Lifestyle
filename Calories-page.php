<?php
session_start();
require_once 'config.php'; // Ensure this file contains your database connection details

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login-page.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle form submission for adding calorie entry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_calorie'])) {
    $calorie_count = (int)$_POST['calorie_count'];
    $date = $_POST['date'];

    if ($calorie_count > 0 && !empty($date)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO Calories (user_id, calorie_count, date) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $calorie_count, $date]);
            $success_message = "Calorie entry added successfully!";
        } catch (PDOException $e) {
            $error_message = "Error adding calorie entry: " . $e->getMessage();
        }
    } else {
        $error_message = "Please provide valid calorie count and date.";
    }
}

// Fetch user's calorie entries
try {
    $stmt = $pdo->prepare("SELECT * FROM Calories WHERE user_id = ? ORDER BY date DESC");
    $stmt->execute([$user_id]);
    $calorie_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching calorie entries: " . $e->getMessage();
}

// Calculate total calories
$total_calories = 0;
if (!empty($calorie_entries)) {
    foreach ($calorie_entries as $entry) {
        $total_calories += $entry['calorie_count'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calorie Tracker - Optimal Lifestyle</title>
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
                    <a class="nav-link active" href="Calories-page.php">Calorie Tracker</a>
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
    <h1 class="text-center mb-4">Calorie Tracker</h1>

    <!-- Add Calorie Entry Form -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="card-title">Add Calorie Entry</h2>
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="calorie_count" class="form-label">Calorie Count</label>
                    <input type="number" class="form-control" id="calorie_count" name="calorie_count" required min="1">
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required value="<?= date('Y-m-d') ?>">
                </div>
                <button type="submit" name="add_calorie" class="btn btn-success">Add Entry</button>
            </form>
        </div>
    </div>

    <!-- Calorie Entries Table -->
    <div class="card">
        <div class="card-body">
            <h2 class="card-title">Your Calorie Entries</h2>
            <?php if (empty($calorie_entries)): ?>
                <p class="text-muted">No calorie entries found. Start logging your calories!</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Calories</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($calorie_entries as $entry): ?>
                                <tr>
                                    <td><?= htmlspecialchars($entry['date']) ?></td>
                                    <td><?= htmlspecialchars($entry['calorie_count']) ?> kcal</td>
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