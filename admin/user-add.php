<?php
require_once 'includes/auth-check.php';
//require_once 'includes/admin-check.php';
require_once 'config/database.php';

$errors = [];
$success = false;

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
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters";
    if ($password !== $password_confirm) $errors[] = "Passwords don't match";
    if (!in_array($user_type, ['user', 'admin'])) $errors[] = "Invalid user type";

    // Check if email/username exists
    $stmt = $pdo->prepare("SELECT id FROM usersmanage WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) $errors[] = "Email or username already exists";

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO usersmanage 
            (name, email, password, username, gender, initial_weight, height, kcal_objective, user_type, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        if ($stmt->execute([$name, $email, $hashed_password, $username, $gender, $initial_weight, $height, $kcal_objective, $user_type])) {
            $success = true;
            $_POST = []; // Clear form
        } else {
            $errors[] = "Failed to create user. Please try again.";
        }
    }
}

$page_title = "Add New User";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New User</h1>
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
        User created successfully!
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
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Username *</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirm">Confirm Password *</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_type">User Type *</label>
                            <select class="form-control" id="user_type" name="user_type" required>
                                <option value="user" <?= ($_POST['user_type'] ?? '') == 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= ($_POST['user_type'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender">
                                <option value="M" <?= ($_POST['gender'] ?? '') == 'M' ? 'selected' : '' ?>>Male</option>
                                <option value="F" <?= ($_POST['gender'] ?? '') == 'F' ? 'selected' : '' ?>>Female</option>
                                <option value="NB" <?= ($_POST['gender'] ?? '') == 'NB' ? 'selected' : '' ?>>Non-binary</option>
                                <option value="O" <?= ($_POST['gender'] ?? '') == 'O' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="initial_weight">Initial Weight (kg)</label>
                            <input type="number" class="form-control" id="initial_weight" name="initial_weight" 
                                   value="<?= htmlspecialchars($_POST['initial_weight'] ?? '70') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="height">Height (cm)</label>
                            <input type="number" class="form-control" id="height" name="height" 
                                   value="<?= htmlspecialchars($_POST['height'] ?? '170') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="kcal_objective">Daily Calorie Objective</label>
                            <input type="number" class="form-control" id="kcal_objective" name="kcal_objective" 
                                   value="<?= htmlspecialchars($_POST['kcal_objective'] ?? '2000') ?>">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Create User</button>
                <a href="users.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>