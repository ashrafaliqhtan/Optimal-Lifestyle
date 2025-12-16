<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

// Get statistics with null checks
$stats = [
    'total_workouts' => $pdo->query("SELECT COALESCE(COUNT(*), 0) FROM exercises")->fetchColumn(),
    'active_users' => $pdo->query("SELECT COALESCE(COUNT(DISTINCT user_id), 0) FROM exercises")->fetchColumn(),
    'total_calories' => $pdo->query("SELECT COALESCE(SUM(total_kcal), 0) FROM exercises")->fetchColumn(),
    'avg_workouts' => $pdo->query("
        SELECT COALESCE(COUNT(*) / NULLIF(COUNT(DISTINCT user_id), 1), 0)
        FROM exercises
    ")->fetchColumn()
];

// Get recent workouts with empty array fallback
$recentWorkouts = $pdo->query("
    SELECT e.*, u.name as user_name, ed.exercise_type
    FROM exercises e
    JOIN usersmanage u ON e.user_id = u.id
    JOIN exercises_default ed ON e.exercise_id = ed.exercise_id
    ORDER BY e.date_done DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Get popular exercises with empty array fallback
$popularExercises = $pdo->query("
    SELECT ed.exercise_type, COUNT(e.id) as count, ed.kcal_hour
    FROM exercises e
    JOIN exercises_default ed ON e.exercise_id = ed.exercise_id
    GROUP BY e.exercise_id
    ORDER BY count DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC) ?: [];

$page_title = "Fitness Dashboard";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Fitness Dashboard</h1>
        <div>
            <a href="workouts.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-dumbbell fa-sm text-white-50"></i> View Workouts
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Total Workouts Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Workouts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format((int)$stats['total_workouts']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format((int)$stats['active_users']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calories Burned Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Calories Burned</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format((float)$stats['total_calories']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-fire fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avg Workouts Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Avg Workouts per User</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format((float)$stats['avg_workouts'], 1) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Workouts -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Workouts</h6>
                    <a href="workouts.php" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Exercise</th>
                                    <th>Date</th>
                                    <th>Calories</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentWorkouts as $workout): ?>
                                <tr>
                                    <td><?= htmlspecialchars($workout['user_name'] ?? 'Unknown') ?></td>
                                    <td><?= htmlspecialchars($workout['exercise_type'] ?? 'Unknown') ?></td>
                                    <td><?= !empty($workout['date_done']) ? date('M j, Y', strtotime($workout['date_done'])) : 'N/A' ?></td>
                                    <td><?= $workout['total_kcal'] ?? 0 ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($recentWorkouts)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">No recent workouts found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Popular Exercises -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Popular Exercises</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($popularExercises)): ?>
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="exercisesChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <?php foreach ($popularExercises as $exercise): ?>
                        <span class="mr-2">
                            <i class="fas fa-circle" style="color: <?= getRandomColor() ?>"></i>
                            <?= htmlspecialchars($exercise['exercise_type']) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <p>No exercise data available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($popularExercises)): ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Exercises Chart
var ctx = document.getElementById("exercisesChart");
var exercisesChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [<?= implode(',', array_map(function($e) { return "'" . addslashes($e['exercise_type']) . "'"; }, $popularExercises)) ?>],
        datasets: [{
            data: [<?= implode(',', array_column($popularExercises, 'count')) ?>],
            backgroundColor: [
                <?php foreach ($popularExercises as $exercise): ?>
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
<?php endif; ?>

<?php include 'includes/footer.php'; ?>