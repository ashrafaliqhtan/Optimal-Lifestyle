<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

$page_title = "Analytics";
include 'includes/header.php';
include 'includes/sidebar.php';

// Initialize analytics data
$user_analytics = [];
$content_analytics = [];
$fitness_analytics = [];
$nutrition_analytics = [];
$activity_logs = [];

try {
    // User Analytics
    $stmt = $pdo->prepare("
        SELECT 
            DATE(created_at) as date,
            COUNT(*) as signups,
            SUM(CASE WHEN user_type = 'admin' THEN 1 ELSE 0 END) as admin_signups
        FROM usersmanage
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    $stmt->execute();
    $user_analytics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Content Analytics
    $stmt = $pdo->prepare("
        SELECT 
            DATE(a.created_at) as date,
            COUNT(a.article_id) as articles,
            COUNT(DISTINCT a.author_id) as authors,
            COUNT(v.view_id) as views,
            COUNT(DISTINCT v.user_id) as unique_visitors
        FROM Articles a
        LEFT JOIN Article_Views v ON a.article_id = v.article_id
        WHERE a.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY DATE(a.created_at)
        ORDER BY date ASC
    ");
    $stmt->execute();
    $content_analytics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fitness Analytics
    $stmt = $pdo->prepare("
        SELECT 
            DATE(date_done) as date,
            COUNT(*) as workouts,
            COUNT(DISTINCT user_id) as active_users,
            SUM(total_kcal) as calories_burned
        FROM exercises
        WHERE date_done >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY DATE(date_done)
        ORDER BY date ASC
    ");
    $stmt->execute();
    $fitness_analytics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Nutrition Analytics
    // Nutrition Analytics
$stmt = $pdo->prepare("
    SELECT 
        DATE(consumed_at) as date,
        COUNT(*) as food_entries,
        COUNT(DISTINCT user_id) as tracking_users,
        AVG(calorie_amount) as avg_calories
    FROM CalorieCalculator
    WHERE consumed_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DATE(consumed_at)
    ORDER BY date ASC
");
    $stmt->execute();
    $nutrition_analytics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recent Activities
    $stmt = $pdo->prepare("
        SELECT 
            al.*,
            u.name as user_name,
            u.email as user_email
        FROM activity_logs al
        LEFT JOIN usersmanage u ON al.user_id = u.id
        ORDER BY al.created_at DESC
        LIMIT 10
    ");
    $stmt->execute();
    $activity_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Error fetching analytics data: " . $e->getMessage();
}
?>

<div id="content">
    <?php include 'includes/topbar.php'; ?>

    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">System Analytics</h1>
            <div class="d-none d-sm-inline-block">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary active" id="last30Days">Last 30 Days</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="last90Days">Last 90 Days</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="thisYear">This Year</button>
                </div>
                <button class="btn btn-sm btn-primary shadow-sm ml-2" id="refreshAnalytics">
                    <i class="fas fa-sync-alt fa-sm text-white-50"></i> Refresh
                </button>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- User Analytics Card -->
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">User Analytics</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                <div class="dropdown-header">Export Options:</div>
                                <a class="dropdown-item export-user-analytics" href="#" data-type="csv">CSV</a>
                                <a class="dropdown-item export-user-analytics" href="#" data-type="json">JSON</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="userAnalyticsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Analytics Card -->
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Content Analytics</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                <div class="dropdown-header">Export Options:</div>
                                <a class="dropdown-item export-content-analytics" href="#" data-type="csv">CSV</a>
                                <a class="dropdown-item export-content-analytics" href="#" data-type="json">JSON</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="contentAnalyticsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Fitness Analytics Card -->
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Fitness Analytics</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                <div class="dropdown-header">Export Options:</div>
                                <a class="dropdown-item export-fitness-analytics" href="#" data-type="csv">CSV</a>
                                <a class="dropdown-item export-fitness-analytics" href="#" data-type="json">JSON</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="fitnessAnalyticsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nutrition Analytics Card -->
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Nutrition Analytics</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                <div class="dropdown-header">Export Options:</div>
                                <a class="dropdown-item export-nutrition-analytics" href="#" data-type="csv">CSV</a>
                                <a class="dropdown-item export-nutrition-analytics" href="#" data-type="json">JSON</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="nutritionAnalyticsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
                <a href="activities.php" class="btn btn-sm btn-link">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Activity</th>
                                <th>Details</th>
                                <th>IP Address</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activity_logs as $activity): ?>
                            <tr>
                                <td>
                                    <?php if ($activity['user_id']): ?>
                                        <a href="user-view.php?id=<?= $activity['user_id'] ?>">
                                            <?= htmlspecialchars($activity['user_name']) ?>
                                        </a>
                                    <?php else: ?>
                                        System
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($activity['activity_type']) ?></td>
                                <td><?= htmlspecialchars(truncateString($activity['details'], 50)) ?></td>
                                <td><?= htmlspecialchars($activity['ip_address']) ?></td>
                                <td><?= formatDateTime($activity['created_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
$(document).ready(function() {
    // User Analytics Chart
    const userCtx = document.getElementById('userAnalyticsChart');
    const userAnalyticsChart = new Chart(userCtx, {
        type: 'line',
        data: {
            labels: [<?= implode(',', array_map(function($v) { return "'" . date('M j', strtotime($v['date'])) . "'"; }, $user_analytics)) ?>],
            datasets: [
                {
                    label: "User Signups",
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "#fff",
                    pointHoverBackgroundColor: "#fff",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    data: [<?= implode(',', array_column($user_analytics, 'signups')) ?>]
                },
                {
                    label: "Admin Signups",
                    backgroundColor: "rgba(28, 200, 138, 0.05)",
                    borderColor: "rgba(28, 200, 138, 1)",
                    pointBackgroundColor: "rgba(28, 200, 138, 1)",
                    pointBorderColor: "#fff",
                    pointHoverBackgroundColor: "#fff",
                    pointHoverBorderColor: "rgba(28, 200, 138, 1)",
                    data: [<?= implode(',', array_column($user_analytics, 'admin_signups')) ?>]
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    }
                }
            }
        }
    });

    // Content Analytics Chart
    const contentCtx = document.getElementById('contentAnalyticsChart');
    const contentAnalyticsChart = new Chart(contentCtx, {
        type: 'bar',
        data: {
            labels: [<?= implode(',', array_map(function($v) { return "'" . date('M j', strtotime($v['date'])) . "'"; }, $content_analytics)) ?>],
            datasets: [
                {
                    label: "Articles Published",
                    backgroundColor: "rgba(54, 185, 204, 0.5)",
                    borderColor: "rgba(54, 185, 204, 1)",
                    borderWidth: 1,
                    data: [<?= implode(',', array_column($content_analytics, 'articles')) ?>]
                },
                {
                    label: "Content Views",
                    backgroundColor: "rgba(78, 115, 223, 0.5)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    borderWidth: 1,
                    data: [<?= implode(',', array_column($content_analytics, 'views')) ?>]
                },
                {
                    label: "Unique Visitors",
                    backgroundColor: "rgba(28, 200, 138, 0.5)",
                    borderColor: "rgba(28, 200, 138, 1)",
                    borderWidth: 1,
                    data: [<?= implode(',', array_column($content_analytics, 'unique_visitors')) ?>]
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Fitness Analytics Chart
    const fitnessCtx = document.getElementById('fitnessAnalyticsChart');
    const fitnessAnalyticsChart = new Chart(fitnessCtx, {
        type: 'line',
        data: {
            labels: [<?= implode(',', array_map(function($v) { return "'" . date('M j', strtotime($v['date'])) . "'"; }, $fitness_analytics)) ?>],
            datasets: [
                {
                    label: "Workouts",
                    backgroundColor: "rgba(246, 194, 62, 0.1)",
                    borderColor: "rgba(246, 194, 62, 1)",
                    pointBackgroundColor: "rgba(246, 194, 62, 1)",
                    pointBorderColor: "#fff",
                    pointHoverBackgroundColor: "#fff",
                    pointHoverBorderColor: "rgba(246, 194, 62, 1)",
                    data: [<?= implode(',', array_column($fitness_analytics, 'workouts')) ?>]
                },
                {
                    label: "Active Users",
                    backgroundColor: "rgba(231, 74, 59, 0.1)",
                    borderColor: "rgba(231, 74, 59, 1)",
                    pointBackgroundColor: "rgba(231, 74, 59, 1)",
                    pointBorderColor: "#fff",
                    pointHoverBackgroundColor: "#fff",
                    pointHoverBorderColor: "rgba(231, 74, 59, 1)",
                    data: [<?= implode(',', array_column($fitness_analytics, 'active_users')) ?>]
                },
                {
                    label: "Calories Burned (÷100)",
                    backgroundColor: "rgba(28, 200, 138, 0.1)",
                    borderColor: "rgba(28, 200, 138, 1)",
                    pointBackgroundColor: "rgba(28, 200, 138, 1)",
                    pointBorderColor: "#fff",
                    pointHoverBackgroundColor: "#fff",
                    pointHoverBorderColor: "rgba(28, 200, 138, 1)",
                    data: [<?= implode(',', array_map(function($v) { return $v['calories_burned'] / 100; }, $fitness_analytics)) ?>]
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label.includes('Calories')) {
                                return label + ': ' + (context.parsed.y * 100).toLocaleString();
                            }
                            return label + ': ' + context.parsed.y;
                        }
                    }
                },
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    }
                }
            }
        }
    });

    // Nutrition Analytics Chart
    const nutritionCtx = document.getElementById('nutritionAnalyticsChart');
    const nutritionAnalyticsChart = new Chart(nutritionCtx, {
        type: 'bar',
        data: {
            labels: [<?= implode(',', array_map(function($v) { return "'" . date('M j', strtotime($v['date'])) . "'"; }, $nutrition_analytics)) ?>],
            datasets: [
                {
                    label: "Food Entries",
                    backgroundColor: "rgba(108, 117, 125, 0.5)",
                    borderColor: "rgba(108, 117, 125, 1)",
                    borderWidth: 1,
                    data: [<?= implode(',', array_column($nutrition_analytics, 'food_entries')) ?>]
                },
                {
                    label: "Tracking Users",
                    backgroundColor: "rgba(0, 123, 255, 0.5)",
                    borderColor: "rgba(0, 123, 255, 1)",
                    borderWidth: 1,
                    data: [<?= implode(',', array_column($nutrition_analytics, 'tracking_users')) ?>]
                },
                {
                    label: "Avg Calories (÷10)",
                    backgroundColor: "rgba(220, 53, 69, 0.5)",
                    borderColor: "rgba(220, 53, 69, 1)",
                    borderWidth: 1,
                    data: [<?= implode(',', array_map(function($v) { return $v['avg_calories'] / 10; }, $nutrition_analytics)) ?>]
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label.includes('Avg Calories')) {
                                return 'Avg Calories: ' + (context.parsed.y * 10).toFixed(0);
                            }
                            return label + ': ' + context.parsed.y;
                        }
                    }
                },
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Time period buttons
    $('#last30Days').click(function() {
        loadAnalyticsData('30days');
    });

    $('#last90Days').click(function() {
        loadAnalyticsData('90days');
    });

    $('#thisYear').click(function() {
        loadAnalyticsData('year');
    });

    // Refresh button
    $('#refreshAnalytics').click(function() {
        location.reload();
    });

    // Export buttons
    $('.export-user-analytics').click(function(e) {
        e.preventDefault();
        exportAnalyticsData('user', $(this).data('type'));
    });

    $('.export-content-analytics').click(function(e) {
        e.preventDefault();
        exportAnalyticsData('content', $(this).data('type'));
    });

    $('.export-fitness-analytics').click(function(e) {
        e.preventDefault();
        exportAnalyticsData('fitness', $(this).data('type'));
    });

    $('.export-nutrition-analytics').click(function(e) {
        e.preventDefault();
        exportAnalyticsData('nutrition', $(this).data('type'));
    });

    // Helper functions
    function loadAnalyticsData(period) {
        // In a real app, this would make an AJAX call to fetch data for the selected period
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');
        
        // Show loading state
        $('#refreshAnalytics').html('<i class="fas fa-spinner fa-spin fa-sm text-white-50"></i> Loading...');
        
        // Simulate loading
        setTimeout(function() {
            $('#refreshAnalytics').html('<i class="fas fa-sync-alt fa-sm text-white-50"></i> Refresh');
            toastr.success('Analytics data updated for ' + period);
        }, 1000);
    }

    function exportAnalyticsData(type, format) {
        // In a real app, this would make an AJAX call to export the data
        toastr.info('Exporting ' + type + ' analytics as ' + format.toUpperCase());
    }
});
</script>