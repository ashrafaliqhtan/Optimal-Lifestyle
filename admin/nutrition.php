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
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nutrition Dashboard</h1>
        <div>
            <a href="foods.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-utensils fa-sm text-white-50"></i> View Foods
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
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

    <div class="row">
        <!-- Recent Food Entries -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Food Entries</h6>
                    <a href="foods.php" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Food</th>
                                    <th>Calories</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentFoods as $food): ?>
                                <tr>
                                    <td><?= htmlspecialchars($food['user_name']) ?></td>
                                    <td><?= htmlspecialchars($food['food_name']) ?></td>
                                    <td><?= $food['calorie_amount'] ?></td>
                                    <td><?= date('M j, Y', strtotime($food['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Foods by Calories -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Foods by Calories</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="foodsChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <?php foreach ($topFoods as $food): ?>
                        <span class="mr-2">
                            <i class="fas fa-circle" style="color: <?= getRandomColor() ?>"></i>
                            <?= htmlspecialchars($food['food_name']) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Foods Chart
var ctx = document.getElementById("foodsChart");
var foodsChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [<?= implode(',', array_map(function($f) { return "'" . addslashes($f['food_name']) . "'"; }, $topFoods)) ?>],
        datasets: [{
            data: [<?= implode(',', array_column($topFoods, 'total_calories')) ?>],
            backgroundColor: [
                <?php foreach ($topFoods as $food): ?>
                '<?= getRandomColor() ?>',
                <?php endforeach; ?>
            ],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            tooltip: {
                backgroundColor: "rgb(255,255,255)",
                bodyColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                displayColors: false,
            },
            legend: {
                display: false
            }
        },
        cutout: '80%',
    },
});
</script>

<?php include 'includes/footer.php'; ?>