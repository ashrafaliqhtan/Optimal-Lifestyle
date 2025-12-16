<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';
require_once 'includes/helpers.php';
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_id = (int)$_GET['id'];

// Get user data
$stmt = $pdo->prepare("SELECT * FROM usersmanage WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: users.php");
    exit;
}

// Get user statistics
$stats = [
    'articles'   => $pdo->query("SELECT COUNT(*) FROM Articles WHERE author_id = $user_id")->fetchColumn(),
    'workouts'   => $pdo->query("SELECT COUNT(*) FROM exercises WHERE user_id = $user_id")->fetchColumn(),
    'calories'   => $pdo->query("SELECT AVG(calorie_count) FROM Calories WHERE user_id = $user_id")->fetchColumn(),
    // Retrieve last login from the usersmanage table (adjust the field name if needed)
    'last_login' => !empty($user['last_login']) ? $user['last_login'] : null
];

$page_title = "User Details";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User Details</h1>
        <div>
            <a href="user-edit.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit User
            </a>
            <a href="users.php" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Users
            </a>
        </div>
    </div>

    <div class="row">
        <!-- User Profile -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Profile</h6>
                </div>
                <div class="card-body text-center">
                    <img class="img-profile rounded-circle mb-3" 
                         src="<?= getGravatar($user['email'], 200) ?>" 
                         alt="Profile" style="width: 150px; height: 150px;">
                    
                    <h4><?= htmlspecialchars($user['name']) ?></h4>
                    <p class="text-muted mb-1">
                        <span class="badge <?= $user['user_type'] == 'admin' ? 'bg-danger' : 'bg-primary' ?>">
                            <?= ucfirst($user['user_type']) ?>
                        </span>
                    </p>
                    <p class="mb-1"><i class="fas fa-envelope mr-2"></i><?= htmlspecialchars($user['email']) ?></p>
                    <p class="mb-1"><i class="fas fa-user mr-2"></i><?= htmlspecialchars($user['username']) ?></p>
                    <p class="mb-1"><i class="fas fa-venus-mars mr-2"></i><?= getGenderLabel($user['gender']) ?></p>
                    <p class="mb-1"><i class="fas fa-weight mr-2"></i><?= $user['initial_weight'] ?> kg</p>
                    <p class="mb-1"><i class="fas fa-ruler-vertical mr-2"></i><?= $user['height'] ?> cm</p>
                    <p class="mb-1"><i class="fas fa-burn mr-2"></i><?= $user['kcal_objective'] ?> kcal/day</p>
                    <p class="text-muted mt-3">
                        <small>Member since <?= date('M j, Y', strtotime($user['created_at'])) ?></small>
                    </p>
                </div>
            </div>
        </div>

        <!-- User Statistics and Activity -->
        <div class="col-lg-8">
            <!-- Statistics Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-4">
                            <div class="stat-circle bg-primary mx-auto">
                                <i class="fas fa-newspaper"></i>
                            </div>
                            <h4 class="mt-3"><?= $stats['articles'] ?></h4>
                            <p class="text-muted mb-0">Articles</p>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="stat-circle bg-success mx-auto">
                                <i class="fas fa-dumbbell"></i>
                            </div>
                            <h4 class="mt-3"><?= $stats['workouts'] ?></h4>
                            <p class="text-muted mb-0">Workouts</p>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="stat-circle bg-info mx-auto">
                                <i class="fas fa-fire"></i>
                            </div>
                            <h4 class="mt-3"><?= round($stats['calories']) ?></h4>
                            <p class="text-muted mb-0">Avg Calories</p>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="stat-circle bg-warning mx-auto">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h4 class="mt-3">
                                <?= $stats['last_login'] ? date('M j', strtotime($stats['last_login'])) : 'Never' ?>
                            </h4>
                            <p class="text-muted mb-0">Last Login</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                    <a href="user-activity.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body">
                    <?php
                    $activities = $pdo->query("
                        SELECT * FROM user_activities 
                        WHERE user_id = $user_id 
                        ORDER BY activity_date DESC 
                        LIMIT 5
                    ")->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (empty($activities)): ?>
                        <p class="text-muted text-center">No recent activity</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($activities as $activity): ?>
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= htmlspecialchars($activity['activity_type']) ?></h6>
                                    <small><?= formatDateTime($activity['activity_date']) ?></small>
                                </div>
                                <p class="mb-0"><?= htmlspecialchars(truncateString($activity['activity_details'], 100)) ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>