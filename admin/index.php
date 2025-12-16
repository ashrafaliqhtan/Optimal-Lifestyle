<?php
// Start session and check authentication
require_once 'includes/auth-check.php';
require_once 'config/database.php';

// Define helper functions
require_once 'includes/helpers.php';

// Initialize all statistics with default values
$user_stats = ['total_users' => 0, 'admin_users' => 0, 'active_users' => 0, 'new_users' => 0];
$content_stats = ['total_content' => 0, 'published_content' => 0, 'draft_content' => 0, 'total_views' => 0];
$fitness_stats = ['total_workouts' => 0, 'active_trainees' => 0, 'total_exercises' => 0];
$nutrition_stats = ['total_foods' => 0, 'users_tracking' => 0, 'avg_calories' => 0];
$prev_user_stats = ['total_users' => 0, 'active_users' => 0, 'new_users' => 0];
$prev_content_stats = ['total_views' => 0];
$recent_activities = [];
$recent_users = [];
$recent_content = [];
$visitor_stats = [];
$popular_content = [];

// Get statistics for dashboard
try {
    // User statistics
    $users_stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_users,
            SUM(CASE WHEN user_type = 'admin' THEN 1 ELSE 0 END) as admin_users,
            COUNT(DISTINCT id) as active_users,
            COUNT(DISTINCT CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 100 DAY) THEN id END) as new_users
        FROM usersmanage
    ");
    $users_stmt->execute();
    $user_stats = array_merge($user_stats, $users_stmt->fetch(PDO::FETCH_ASSOC) ?: []);

    // Content statistics (using Articles table)
    $content_stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_content,
            (SELECT COUNT(*) FROM Article_Views) as total_views
        FROM Articles
    ");
    $content_stmt->execute();
    $content_stats = array_merge($content_stats, $content_stmt->fetch(PDO::FETCH_ASSOC) ?: []);

    // Fitness statistics (using exercises table)
    $fitness_stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_workouts,
            COUNT(DISTINCT user_id) as active_trainees,
            SUM(total_kcal) as total_exercises
        FROM exercises
    ");
    $fitness_stmt->execute();
    $fitness_stats = array_merge($fitness_stats, $fitness_stmt->fetch(PDO::FETCH_ASSOC) ?: []);

    // Nutrition statistics (using CalorieCalculator table)
    $nutrition_stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_foods,
            COUNT(DISTINCT user_id) as users_tracking,
            AVG(calorie_amount) as avg_calories
        FROM CalorieCalculator
    ");
    $nutrition_stmt->execute();
    $nutrition_stats = array_merge($nutrition_stats, $nutrition_stmt->fetch(PDO::FETCH_ASSOC) ?: []);

    // Recent activities (using user registrations as example)
    $activities_stmt = $pdo->prepare("
        SELECT 
            id as admin_id, 
            name as admin_name, 
            created_at as activity_date,
            'User Registration' as activity_type,
            CONCAT('New user registered: ', name) as activity_details
        FROM usersmanage
        ORDER BY created_at DESC 
        LIMIT 8
    ");
    $activities_stmt->execute();
    $recent_activities = $activities_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recent user registrations
    $recent_users_stmt = $pdo->prepare("
        SELECT id, name, email, created_at 
        FROM usersmanage 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $recent_users_stmt->execute();
    $recent_users = $recent_users_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recent content (using Articles table)
    $recent_content_stmt = $pdo->prepare("
        SELECT a.article_id as id, a.title, 'article' as content_type, 
               a.created_at, u.name as author_name
        FROM Articles a
        LEFT JOIN usersmanage u ON a.author_id = u.id
        ORDER BY a.created_at DESC
        LIMIT 5
    ");
    $recent_content_stmt->execute();
    $recent_content = $recent_content_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Visitor statistics (using Article_Views as proxy)
    $visitor_stmt = $pdo->prepare("
        SELECT 
            DATE(view_date) as date,
            COUNT(*) as visits,
            COUNT(DISTINCT user_id) as unique_visitors
        FROM Article_Views
        WHERE view_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY DATE(view_date)
        ORDER BY date ASC
    ");
    $visitor_stmt->execute();
    $visitor_stats = $visitor_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Popular content (using Articles and Article_Views)
    $popular_content_stmt = $pdo->prepare("
        SELECT a.article_id as id, a.title, 'article' as content_type, 
               COUNT(v.view_id) as views, u.name as author_name
        FROM Articles a
        LEFT JOIN Article_Views v ON a.article_id = v.article_id
        LEFT JOIN usersmanage u ON a.author_id = u.id
        GROUP BY a.article_id
        ORDER BY views DESC
        LIMIT 5
    ");
    $popular_content_stmt->execute();
    $popular_content = $popular_content_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Dashboard data fetch error: " . $e->getMessage());
    $error = "Error fetching dashboard data. Please try again later.";
}

// Get previous period data for comparison
try {
    $prev_users_stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_users,
            COUNT(DISTINCT id) as active_users,
            COUNT(DISTINCT CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY) 
                                AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY) 
                           THEN id END) as new_users
        FROM usersmanage
    ");
    $prev_users_stmt->execute();
    $prev_user_stats = array_merge($prev_user_stats, $prev_users_stmt->fetch(PDO::FETCH_ASSOC) ?: []);

    $prev_content_stmt = $pdo->prepare("
        SELECT (SELECT COUNT(*) FROM Article_Views) as total_views
    ");
    $prev_content_stmt->execute();
    $prev_content_stats = array_merge($prev_content_stats, $prev_content_stmt->fetch(PDO::FETCH_ASSOC) ?: []);

} catch (PDOException $e) {
    error_log("Previous period data fetch error: " . $e->getMessage());
}

// Include header
$page_title = "Dashboard";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<div id="content">

    <!-- Topbar -->
    <?php include 'includes/topbar.php'; ?>
    <!-- End of Topbar -->

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard Overview</h1>
            <div class="d-none d-sm-inline-block">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="today-btn">Today</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary active" id="week-btn">Week</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="month-btn">Month</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="year-btn">Year</button>
                </div>
                <button class="btn btn-sm btn-primary shadow-sm ml-2" id="generate-report">
                    <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                </button>
            </div>
        </div>

        <!-- Status Cards -->
        <div class="row">
            <!-- Total Users Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Users</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($user_stats['total_users']) ?></div>
                                <div class="mt-2 text-xs">
                                    <?php 
                                    $current = (int)$user_stats['total_users'];
                                    $previous = (int)$prev_user_stats['total_users'];
                                    $change = getPercentageChange($current, $previous);
                                    ?>
                                    <span class="<?= $change >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <i class="fas fa-arrow-<?= $change >= 0 ? 'up' : 'down' ?>"></i>
                                        <?= abs(round($change)) ?>%
                                    </span>
                                    <span class="text-muted ml-1">vs previous period</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($user_stats['active_users']) ?></div>
                                <div class="mt-2 text-xs">
                                    <?php 
                                    $current = (int)$user_stats['active_users'];
                                    $previous = (int)$prev_user_stats['active_users'];
                                    $change = getPercentageChange($current, $previous);
                                    ?>
                                    <span class="<?= $change >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <i class="fas fa-arrow-<?= $change >= 0 ? 'up' : 'down' ?>"></i>
                                        <?= abs(round($change)) ?>%
                                    </span>
                                    <span class="text-muted ml-1">vs previous period</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Users Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    New Users (7d)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($user_stats['new_users']) ?></div>
                                <div class="mt-2 text-xs">
                                    <?php 
                                    $current = (int)$user_stats['new_users'];
                                    $previous = (int)$prev_user_stats['new_users'];
                                    $change = getPercentageChange($current, $previous);
                                    ?>
                                    <span class="<?= $change >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <i class="fas fa-arrow-<?= $change >= 0 ? 'up' : 'down' ?>"></i>
                                        <?= abs(round($change)) ?>%
                                    </span>
                                    <span class="text-muted ml-1">vs previous period</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Views Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Content Views</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($content_stats['total_views']) ?></div>
                                <div class="mt-2 text-xs">
                                    <?php 
                                    $current = (int)$content_stats['total_views'];
                                    $previous = (int)$prev_content_stats['total_views'];
                                    $change = getPercentageChange($current, $previous);
                                    ?>
                                    <span class="<?= $change >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <i class="fas fa-arrow-<?= $change >= 0 ? 'up' : 'down' ?>"></i>
                                        <?= abs(round($change)) ?>%
                                    </span>
                                    <span class="text-muted ml-1">vs previous period</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-eye fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Visitors Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Visitors Overview (Last 30 Days)</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" id="visitorsDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                <div class="dropdown-header">Export Options:</div>
                                <a class="dropdown-item export-btn" href="#" data-type="csv"><i class="fas fa-file-csv mr-2"></i>CSV</a>
                                <a class="dropdown-item export-btn" href="#" data-type="excel"><i class="fas fa-file-excel mr-2"></i>Excel</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item print-btn" href="#"><i class="fas fa-print mr-2"></i>Print Chart</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="visitorsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Status Chart -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Content Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="contentStatusChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            <span class="mr-2">
                                <i class="fas fa-circle text-primary"></i> Articles (<?= $content_stats['total_content'] ?>)
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-info"></i> Views (<?= $content_stats['total_views'] ?>)
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activities and Users Row -->
        <div class="row">
            <!-- Recent Activities -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
                        <a href="activities.php" class="btn btn-sm btn-link">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_activities as $activity): ?>
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-<?= getActivityColor($activity['activity_type']) ?> mr-3">
                                            <i class="fas fa-<?= getActivityIcon($activity['activity_type']) ?> text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($activity['admin_name']) ?></h6>
                                            <p class="mb-0 small text-muted"><?= htmlspecialchars($activity['activity_type']) ?></p>
                                        </div>
                                    </div>
                                    <small class="text-muted"><?= formatDateTime($activity['activity_date']) ?></small>
                                </div>
                                <p class="mb-0 mt-2"><?= htmlspecialchars(truncateString($activity['activity_details'], 80)) ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Registrations</h6>
                        <a href="users.php" class="btn btn-sm btn-link">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Registered</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_users as $user): ?>
                                    <tr class="cursor-pointer" onclick="window.location='user-view.php?id=<?= $user['id'] ?>'">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?= getGravatar($user['email']) ?>" class="rounded-circle me-2" width="32" height="32">
                                                <div>
                                                    <div class="fw-bold"><?= htmlspecialchars($user['name']) ?></div>
                                                    <div class="text-muted small"><?= htmlspecialchars($user['email']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= formatDate($user['created_at']) ?></td>
                                        <td>
                                            <span class="badge bg-success">Active</span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->
<div class="row">
    <!-- Recent Content -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Articles</h6>
                <a href="content.php" class="btn btn-sm btn-link">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($recent_content as $content): ?>
                    <a href="content-edit.php?id=<?= $content['id'] ?? '' ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><?= htmlspecialchars($content['title'] ?? 'Untitled Article') ?></h6>
                            <small><?= !empty($content['created_at']) ? formatDate($content['created_at']) : 'Date not available' ?></small>
                        </div>
                        <p class="mb-1">
                            <span class="badge bg-secondary me-1"><?= ucfirst($content['content_type'] ?? 'article') ?></span>
                            <small class="text-muted">By <?= htmlspecialchars($content['author_name'] ?? 'Unknown Author') ?></small>
                        </p>
                    </a>
                    <?php endforeach; ?>
                    <?php if (empty($recent_content)): ?>
                        <div class="list-group-item text-muted">No recent articles found</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Content -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Popular Articles</h6>
                <a href="content.php?sort=views" class="btn btn-sm btn-link">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($popular_content as $content): ?>
                    <a href="content.php?id=<?= $content['id'] ?? '' ?>" target="_blank" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><?= htmlspecialchars($content['title'] ?? 'Untitled Article') ?></h6>
                            <span class="badge bg-primary"><?= number_format($content['views'] ?? 0) ?> views</span>
                        </div>
                        <p class="mb-1">
                            <span class="badge bg-secondary me-1"><?= ucfirst($content['content_type'] ?? 'article') ?></span>
                            <small class="text-muted">By <?= htmlspecialchars($content['author_name'] ?? 'Unknown Author') ?></small>
                        </p>
                    </a>
                    <?php endforeach; ?>
                    <?php if (empty($popular_content)): ?>
                        <div class="list-group-item text-muted">No popular articles found</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Fitness and Nutrition Row -->
        <div class="row">
            <!-- Fitness Stats -->
            <div class="col-md-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Fitness Statistics</h6>
                        <a href="fitness.php" class="btn btn-sm btn-link">View Dashboard</a>
                    </div>
<div class="card-body">
    <div class="row text-center">
        <div class="col-md-4 mb-3">
            <div class="stat-circle bg-primary mx-auto">
                <i class="fas fa-dumbbell"></i>
            </div>
            <h4 class="mt-3"><?= isset($fitness_stats['total_workouts']) ? number_format((int)$fitness_stats['total_workouts']) : '0' ?></h4>
            <p class="text-muted mb-0">Workouts</p>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-circle bg-success mx-auto">
                <i class="fas fa-users"></i>
            </div>
            <h4 class="mt-3"><?= isset($fitness_stats['active_trainees']) ? number_format((int)$fitness_stats['active_trainees']) : '0' ?></h4>
            <p class="text-muted mb-0">Active Trainees</p>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-circle bg-info mx-auto">
                <i class="fas fa-fire"></i>
            </div>
            <h4 class="mt-3"><?= isset($fitness_stats['total_exercises']) ? number_format((int)$fitness_stats['total_exercises']) : '0' ?></h4>
            <p class="text-muted mb-0">Calories Burned</p>
        </div>
    </div>
</div>
                </div>
            </div>

            <!-- Nutrition Stats -->
<div class="col-md-6 mb-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Nutrition Statistics</h6>
            <a href="nutrition.php" class="btn btn-sm btn-link">View Dashboard</a>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4 mb-3">
                    <div class="stat-circle bg-warning mx-auto">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h4 class="mt-3"><?= isset($nutrition_stats['total_foods']) ? number_format((int)$nutrition_stats['total_foods']) : '0' ?></h4>
                    <p class="text-muted mb-0">Foods Tracked</p>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stat-circle bg-danger mx-auto">
                        <i class="fas fa-user"></i>
                    </div>
                    <h4 class="mt-3"><?= isset($nutrition_stats['users_tracking']) ? number_format((int)$nutrition_stats['users_tracking']) : '0' ?></h4>
                    <p class="text-muted mb-0">Users Tracking</p>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stat-circle bg-secondary mx-auto">
                        <i class="fas fa-burn"></i>
                    </div>
                    <h4 class="mt-3"><?= isset($nutrition_stats['avg_calories']) ? number_format((float)$nutrition_stats['avg_calories'], 0) : '0' ?></h4>
                    <p class="text-muted mb-0">Avg Calories</p>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>

    </div>
    <!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<?php include 'includes/footer.php'; ?>

<!-- Page level plugins -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>

<!-- Page level custom scripts -->
<script>
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.font.family = 'Nunito, -apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.color = '#858796';

// Visitors Chart
var ctx = document.getElementById("visitorsChart");
var visitorsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [<?= implode(',', array_map(function($v) { return "'" . date('M j', strtotime($v['date'])) . "'"; }, $visitor_stats)) ?>],
        datasets: [
            {
                label: "Visits",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: [<?= implode(',', array_column($visitor_stats, 'visits')) ?>],
            },
            {
                label: "Unique Visitors",
                lineTension: 0.3,
                backgroundColor: "rgba(28, 200, 138, 0.05)",
                borderColor: "rgba(28, 200, 138, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(28, 200, 138, 1)",
                pointBorderColor: "rgba(28, 200, 138, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(28, 200, 138, 1)",
                pointHoverBorderColor: "rgba(28, 200, 138, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: [<?= implode(',', array_column($visitor_stats, 'unique_visitors')) ?>],
            }
        ],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            },
            y: {
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return number_format(value);
                    }
                },
                grid: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            },
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: "rgb(255,255,255)",
                bodyColor: "#858796",
                titleMarginBottom: 10,
                titleColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(context) {
                        var label = context.dataset.label || '';
                        return label + ': ' + number_format(context.parsed.y);
                    }
                }
            }
        }
    }
});

// Content Status Chart
var ctx = document.getElementById("contentStatusChart");
var contentStatusChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ["Articles", "Views"],
        datasets: [{
            data: [<?= $content_stats['total_content'] ?>, <?= $content_stats['total_views'] ?>],
            backgroundColor: ['#4e73df', '#36b9cc'],
            hoverBackgroundColor: ['#2e59d9', '#2c9faf'],
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
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            }
        },
        cutout: '80%',
    },
});

// Calories Chart (example)
var ctx = document.getElementById("caloriesChart");
var caloriesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
            label: "Calories",
            lineTension: 0.3,
            backgroundColor: "rgba(108, 117, 125, 0.05)",
            borderColor: "rgba(108, 117, 125, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(108, 117, 125, 1)",
            pointBorderColor: "rgba(108, 117, 125, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(108, 117, 125, 1)",
            pointHoverBorderColor: "rgba(108, 117, 125, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: [1850, 1920, 1870, 1950, 2000, 1980, 2050, 2100, 2080, 2150, 2200, 2180],
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            },
            y: {
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return number_format(value);
                    }
                },
                grid: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            },
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: "rgb(255,255,255)",
                bodyColor: "#858796",
                titleMarginBottom: 10,
                titleColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(context) {
                        var label = context.dataset.label || '';
                        return label + ': ' + number_format(context.parsed.y);
                    }
                }
            }
        }
    }
});

// Time period buttons functionality
document.getElementById('today-btn').addEventListener('click', function() {
    fetchDashboardData('today');
});

document.getElementById('week-btn').addEventListener('click', function() {
    fetchDashboardData('week');
});

document.getElementById('month-btn').addEventListener('click', function() {
    fetchDashboardData('month');
});

document.getElementById('year-btn').addEventListener('click', function() {
    fetchDashboardData('year');
});

// Generate report button
document.getElementById('generate-report').addEventListener('click', function() {
    generateReport();
});

// Export buttons functionality
document.querySelectorAll('.export-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const type = this.getAttribute('data-type');
        exportChartData(type);
    });
});

// Print button functionality
document.querySelector('.print-btn').addEventListener('click', function(e) {
    e.preventDefault();
    window.print();
});

// Helper functions
function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function fetchDashboardData(period) {
    fetch('includes/dashboard-data.php?period=' + period, {
        headers: {
            'X-CSRF-TOKEN': '<?php echo $_SESSION['csrf_token']; ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Data loaded for:', period, data);
    })
    .catch(error => console.error('Error:', error));
}

function generateReport() {
    const btn = document.getElementById('generate-report');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
    btn.disabled = true;
    
    fetch('includes/generate-report.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo $_SESSION['csrf_token']; ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        if (data.success) {
            showToast('Report generated successfully!', 'success');
            if (data.download_url) {
                window.location.href = data.download_url;
            }
        } else {
            showToast('Error generating report: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.innerHTML = originalText;
        btn.disabled = false;
        showToast('Error generating report', 'danger');
    });
}

function exportChartData(type) {
    const chartData = {
        visitors: visitorsChart.data,
        contentStatus: contentStatusChart.data,
        calories: caloriesChart.data
    };
    
    fetch('includes/export-data.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo $_SESSION['csrf_token']; ?>'
        },
        body: JSON.stringify({
            type: type,
            data: chartData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.download_url) {
            window.location.href = data.download_url;
            showToast(`Data exported as ${type.toUpperCase()}`, 'success');
        } else {
            showToast('Error exporting data', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error exporting data', 'danger');
    });
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `toast show align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 5000);
    
    toast.querySelector('button').addEventListener('click', () => {
        toast.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    });
}
</script>