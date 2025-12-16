<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'auth-check.php';
require_once '../config/database.php';
require_once 'helpers.php';

// Initialize variables
$user = [];
$fitness_stats = [];
$nutrition_stats = [];
$recent_activities = [];

// Get user id from session (ensure that you have set this variable during login)
if (!isset($_SESSION['admin_id'])) {
    die("User ID not found in session.");
}
$user_id = $_SESSION['admin_id'];

try {
    // Get user data
    $stmt = $pdo->prepare("SELECT * FROM usersmanage WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get fitness stats
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) AS total_workouts,
            SUM(total_kcal) AS total_calories,
            SEC_TO_TIME(SUM(TIME_TO_SEC(total_time))) AS total_time
        FROM exercises
        WHERE user_id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    $fitness_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get nutrition stats
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) AS total_entries,
            SUM(calorie_amount) AS total_calories,
            AVG(calorie_amount) AS avg_calories
        FROM CalorieCalculator
        WHERE user_id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    $nutrition_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get recent activities
    $stmt = $pdo->prepare("
        SELECT * FROM activity_logs
        WHERE user_id = :user_id
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $stmt->execute(['user_id' => $user_id]);
    $recent_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching profile data: " . $e->getMessage();
}

$page_title = "My Profile";
include 'header.php';
include 'sidebar.php';
?>

<div id="content">
    <?php include 'topbar.php'; ?>

    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Profile
            </button>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Edit Profile Modal -->
        <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Profile</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editProfileForm" action="update-profile.php" method="POST">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="editName">Full Name</label>
                                <input type="text" class="form-control" id="editName" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="editEmail">Email</label>
                                <input type="email" class="form-control" id="editEmail" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="editUsername">Username</label>
                                <input type="text" class="form-control" id="editUsername" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editGender">Gender</label>
                                        <select class="form-control" id="editGender" name="gender" required>
                                            <option value="M" <?= $user['gender'] == 'M' ? 'selected' : '' ?>>Male</option>
                                            <option value="F" <?= $user['gender'] == 'F' ? 'selected' : '' ?>>Female</option>
                                            <option value="NB" <?= $user['gender'] == 'NB' ? 'selected' : '' ?>>Non-binary</option>
                                            <option value="O" <?= $user['gender'] == 'O' ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editHeight">Height (cm)</label>
                                        <input type="number" class="form-control" id="editHeight" name="height" value="<?= htmlspecialchars($user['height']) ?>" min="100" max="250" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editWeight">Weight (kg)</label>
                                        <input type="number" class="form-control" id="editWeight" name="weight" value="<?= htmlspecialchars($user['initial_weight']) ?>" min="30" max="300" step="0.1" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editCalories">Calorie Objective</label>
                                        <input type="number" class="form-control" id="editCalories" name="calories" value="<?= htmlspecialchars($user['kcal_objective']) ?>" min="1000" max="10000" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <!-- Profile Overview Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Profile Overview</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <i class="fas fa-key mr-2"></i>Change Password
                                </a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                                    <i class="fas fa-camera mr-2"></i>Upload Photo
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <img class="img-profile rounded-circle mb-3" 
                             src="<?= getGravatar($user['email'], 150) ?>" 
                             alt="Profile" width="150" height="150">
                        <h4><?= htmlspecialchars($user['name']) ?></h4>
                        <p class="text-muted mb-1"><?= htmlspecialchars($user['email']) ?></p>
                        <p class="mb-3">
                            <span class="badge bg-<?= $user['user_type'] == 'admin' ? 'danger' : 'primary' ?>">
                                <?= ucfirst($user['user_type']) ?>
                            </span>
                        </p>
                        <div class="row text-center mt-4">
                            <div class="col-6 border-right">
                                <h5><?= $fitness_stats['total_workouts'] ?? 0 ?></h5>
                                <span class="text-muted">Workouts</span>
                            </div>
                            <div class="col-6">
                                <h5><?= $nutrition_stats['total_entries'] ?? 0 ?></h5>
                                <span class="text-muted">Food Logs</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Details Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Account Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Username</label>
                            <p><?= htmlspecialchars($user['username']) ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Gender</label>
                            <p><?= htmlspecialchars($user['gender']) ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Height</label>
                            <p><?= htmlspecialchars($user['height']) ?> cm</p>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Weight</label>
                            <p><?= htmlspecialchars($user['initial_weight']) ?> kg</p>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Calorie Objective</label>
                            <p><?= htmlspecialchars($user['kcal_objective']) ?> kcal/day</p>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Member Since</label>
                            <p><?= formatDate($user['created_at']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 mb-4">
                <!-- Fitness Statistics Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Fitness Statistics</h6>
                        <a href="fitness.php" class="btn btn-sm btn-link">View Fitness Dashboard</a>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4 mb-4">
                                <div class="stat-circle bg-primary mx-auto">
                                    <i class="fas fa-dumbbell"></i>
                                </div>
                                <h4 class="mt-3"><?= $fitness_stats['total_workouts'] ?? 0 ?></h4>
                                <p class="text-muted mb-0">Workouts</p>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="stat-circle bg-success mx-auto">
                                    <i class="fas fa-fire"></i>
                                </div>
                                <h4 class="mt-3"><?= $fitness_stats['total_calories'] ?? 0 ?></h4>
                                <p class="text-muted mb-0">Calories Burned</p>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="stat-circle bg-info mx-auto">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h4 class="mt-3"><?= $fitness_stats['total_time'] ?? '00:00:00' ?></h4>
                                <p class="text-muted mb-0">Total Time</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nutrition Statistics Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Nutrition Statistics</h6>
                        <a href="nutrition.php" class="btn btn-sm btn-link">View Nutrition Dashboard</a>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4 mb-4">
                                <div class="stat-circle bg-warning mx-auto">
                                    <i class="fas fa-utensils"></i>
                                </div>
                                <h4 class="mt-3"><?= $nutrition_stats['total_entries'] ?? 0 ?></h4>
                                <p class="text-muted mb-0">Food Entries</p>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="stat-circle bg-danger mx-auto">
                                    <i class="fas fa-burn"></i>
                                </div>
                                <h4 class="mt-3"><?= $nutrition_stats['total_calories'] ?? 0 ?></h4>
                                <p class="text-muted mb-0">Total Calories</p>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="stat-circle bg-secondary mx-auto">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h4 class="mt-3"><?= round($nutrition_stats['avg_calories'] ?? 0) ?></h4>
                                <p class="text-muted mb-0">Avg Calories</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities Card -->
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
                                        <h6 class="mb-1"><?= htmlspecialchars($activity['activity_type']) ?></h6>
                                        <small><?= formatDateTime($activity['created_at']) ?></small>
                                    </div>
                                    <p class="mb-0"><?= htmlspecialchars(truncateString($activity['details'], 100)) ?></p>
                                    <small class="text-muted">IP: <?= htmlspecialchars($activity['ip_address']) ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="changePasswordForm" action="includes/change-password.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                        <small class="form-text text-muted">Minimum 8 characters</small>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload Photo Modal -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Profile Photo</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="uploadPhotoForm" action="includes/upload-photo.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="profilePhoto">Select Image</label>
                        <input type="file" class="form-control-file" id="profilePhoto" name="profilePhoto" accept="image/*" required>
                        <small class="form-text text-muted">Max size: 2MB (JPG, PNG, GIF)</small>
                    </div>
                    <div class="text-center">
                        <img id="imagePreview" src="<?= getGravatar($user['email'], 200) ?>" class="img-thumbnail" style="max-width: 200px; display: none;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload Photo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    // Image preview for profile photo upload
    $('#profilePhoto').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });

    // Change Password Validation
    $('#changePasswordForm').submit(function(e) {
        const newPass = $('#newPassword').val();
        const confirmPass = $('#confirmPassword').val();
        
        if (newPass !== confirmPass) {
            e.preventDefault();
            toastr.error('Passwords do not match');
        } else if (newPass.length < 8) {
            e.preventDefault();
            toastr.error('Password must be at least 8 characters');
        }
    });

    // Edit Profile Form Validation
    $('#editProfileForm').submit(function(e) {
        const height = $('#editHeight').val();
        const weight = $('#editWeight').val();
        const calories = $('#editCalories').val();
        
        if (height < 100 || height > 250) {
            e.preventDefault();
            toastr.error('Height must be between 100-250 cm');
        }
        
        if (weight < 30 || weight > 300) {
            e.preventDefault();
            toastr.error('Weight must be between 30-300 kg');
        }
        
        if (calories < 1000 || calories > 10000) {
            e.preventDefault();
            toastr.error('Calorie objective must be between 1000-10000 kcal');
        }
    });
});
</script>
