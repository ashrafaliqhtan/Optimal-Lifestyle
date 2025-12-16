<?php
require_once 'includes/auth-check.php';
//require_once 'includes/admin-check.php';
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_id = (int)$_GET['id'];
$errors = [];
$success = false;

// Get user data
$stmt = $pdo->prepare("SELECT * FROM usersmanage WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: users.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $user_type = $_POST['user_type'];
    $gender = $_POST['gender'];
    $initial_weight = (int)$_POST['initial_weight'];
    $height = (int)$_POST['height'];
    $kcal_objective = (int)$_POST['kcal_objective'];

    // Validation
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email)) $errors[] = "Email is required";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($username)) $errors[] = "Username is required";
    if (!empty($password) && strlen($password) < 8) $errors[] = "Password must be at least 8 characters";
    if (!empty($password) && $password !== $password_confirm) $errors[] = "Passwords don't match";
    if (!in_array($user_type, ['user', 'admin'])) $errors[] = "Invalid user type";

    // Check if email/username exists for other users
    $stmt = $pdo->prepare("SELECT id FROM usersmanage WHERE (email = ? OR username = ?) AND id != ?");
    $stmt->execute([$email, $username, $user_id]);
    if ($stmt->fetch()) $errors[] = "Email or username already exists";

    if (empty($errors)) {
        $updateFields = [
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'user_type' => $user_type,
            'gender' => $gender,
            'initial_weight' => $initial_weight,
            'height' => $height,
            'kcal_objective' => $kcal_objective
        ];

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $updateFields['password'] = $hashed_password;
        }

        $setParts = [];
        $params = [];
        foreach ($updateFields as $field => $value) {
            $setParts[] = "$field = ?";
            $params[] = $value;
        }
        $params[] = $user_id;

        $query = "UPDATE usersmanage SET " . implode(', ', $setParts) . " WHERE id = ?";
        $stmt = $pdo->prepare($query);

        if ($stmt->execute($params)) {
            $success = true;
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM usersmanage WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $errors[] = "Failed to update user. Please try again.";
        }
    }
}

$page_title = "Edit User";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit User</h1>
        <a href="users.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Users
        </a>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
    <div class="alert alert-success">
        User updated successfully!
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Username *</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= htmlspecialchars($user['username']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="text-muted">Leave blank to keep current password</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirm">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_type">User Type *</label>
                            <select class="form-control" id="user_type" name="user_type" required>
                                <option value="user" <?= $user['user_type'] == 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= $user['user_type'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender">
                                <option value="M" <?= $user['gender'] == 'M' ? 'selected' : '' ?>>Male</option>
                                <option value="F" <?= $user['gender'] == 'F' ? 'selected' : '' ?>>Female</option>
                                <option value="NB" <?= $user['gender'] == 'NB' ? 'selected' : '' ?>>Non-binary</option>
                                <option value="O" <?= $user['gender'] == 'O' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="initial_weight">Initial Weight (kg)</label>
                            <input type="number" class="form-control" id="initial_weight" name="initial_weight" 
                                   value="<?= htmlspecialchars($user['initial_weight']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="height">Height (cm)</label>
                            <input type="number" class="form-control" id="height" name="height" 
                                   value="<?= htmlspecialchars($user['height']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="kcal_objective">Daily Calorie Objective</label>
                            <input type="number" class="form-control" id="kcal_objective" name="kcal_objective" 
                                   value="<?= htmlspecialchars($user['kcal_objective']) ?>">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="users.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>