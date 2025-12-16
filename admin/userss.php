<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

// Get nutrition statistics with null checks
$stats = [
    'total_foods' => $pdo->query("SELECT COUNT(*) FROM Food")->fetchColumn() ?? 0,
    'total_calories' => $pdo->query("SELECT COALESCE(SUM(calorie_amount), 0) FROM Food")->fetchColumn() ?? 0,
    'avg_calories' => $pdo->query("SELECT COALESCE(AVG(calorie_amount), 0) FROM Food")->fetchColumn() ?? 0,
    'users_tracking' => $pdo->query("SELECT COALESCE(COUNT(DISTINCT user_id), 0) FROM Calories")->fetchColumn() ?? 0
];

// Get recent food entries
$recentFoods = $pdo->query("
    SELECT c.*, u.name as user_name 
    FROM CalorieCalculator c
    JOIN usersmanage u ON c.user_id = u.id
    ORDER BY c.calorie_id DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC) ?? [];

// Get top foods by calories
$topFoods = $pdo->query("
    SELECT food_name, COALESCE(SUM(calorie_amount), 0) as total_calories
    FROM CalorieCalculator
    GROUP BY food_name
    ORDER BY total_calories DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC) ?? [];

$page_title = "Nutrition Dashboard";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <!-- ... [previous HTML code remains the same until the statistics cards] ... -->

    <!-- Statistics Cards with proper number formatting -->
    <div class="row">
        <!-- Total Foods Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Foods</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format((int)$stats['total_foods']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-utensils fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Calories Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Calories</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format((float)$stats['total_calories']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-fire fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avg Calories Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Avg Calories per Food</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format((float)$stats['avg_calories'], 0) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Tracking Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Users Tracking</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format((int)$stats['users_tracking']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ... [rest of your HTML code remains the same] ... -->