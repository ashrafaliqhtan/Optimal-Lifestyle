<?php
session_start();
require_once 'config/database.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Initialize error array
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and trim form inputs
    $name            = trim($_POST['name']);
    $email           = trim($_POST['email']);
    $username        = trim($_POST['username']);
    $password        = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $gender          = trim($_POST['gender']);
    $initial_weight  = trim($_POST['initial_weight']);
    $height          = trim($_POST['height']);
    $kcal_objective  = trim($_POST['kcal_objective']);

    // Validate required fields and input format
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }
    if (empty($gender)) {
        $errors[] = "Gender is required.";
    }
    if (empty($initial_weight) || !is_numeric($initial_weight)) {
        $errors[] = "Initial weight must be a number.";
    }
    if (empty($height) || !is_numeric($height)) {
        $errors[] = "Height must be a number.";
    }
    if (empty($kcal_objective) || !is_numeric($kcal_objective)) {
        $errors[] = "Calorie objective must be a number.";
    }

    if (empty($errors)) {
        try {
            // Check if the email or username already exists
            $stmt = $pdo->prepare("SELECT id FROM usersmanage WHERE email = ? OR username = ? LIMIT 1");
            $stmt->execute([$email, $username]);
            if ($stmt->fetch()) {
                $errors[] = "Email or username already exists.";
            } else {
                // Securely hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user record into the database
                $stmt = $pdo->prepare("INSERT INTO usersmanage (name, email, password, username, gender, initial_weight, height, kcal_objective, created_at, user_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'admin', 'active')");
                $stmt->execute([
                    $name,
                    $email,
                    $hashedPassword,
                    $username,
                    $gender,
                    $initial_weight,
                    $height,
                    $kcal_objective
                ]);
                
                // Optionally set session variables and redirect to dashboard
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id']       = $pdo->lastInsertId();
                $_SESSION['admin_name']     = $name;
                $_SESSION['admin_email']    = $email;
                $_SESSION['admin_role']     = 'admin';
                
                header("Location: index.php");
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optimal Lifestyle - Admin Registration</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS (if any) -->
    <link rel="stylesheet" href="../assets/css/admin.css">
    
    <style>
        .register-container {
            max-width: 500px;
            margin: 0 auto;
            margin-top: 50px;
        }
        .register-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
        .register-card-header {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 15px;
            border-radius: 10px 10px 0 0;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container register-container">
        <div class="card register-card">
            <div class="card-header register-card-header">
                <h4><i class="fas fa-user-plus"></i> Admin Registration</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <div><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= isset($name) ? htmlspecialchars($name) : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select name="gender" id="gender" class="form-select" required>
                            <option value="">Select Gender</option>
                            <option value="M" <?= (isset($gender) && $gender === 'M') ? 'selected' : '' ?>>Male</option>
                            <option value="F" <?= (isset($gender) && $gender === 'F') ? 'selected' : '' ?>>Female</option>
                            <option value="NB" <?= (isset($gender) && $gender === 'NB') ? 'selected' : '' ?>>Non-binary</option>
                            <option value="O" <?= (isset($gender) && $gender === 'O') ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="initial_weight" class="form-label">Initial Weight (kg)</label>
                        <input type="number" name="initial_weight" id="initial_weight" class="form-control" step="0.1" value="<?= isset($initial_weight) ? htmlspecialchars($initial_weight) : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="height" class="form-label">Height (cm)</label>
                        <input type="number" name="height" id="height" class="form-control" value="<?= isset($height) ? htmlspecialchars($height) : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="kcal_objective" class="form-label">Calorie Objective (kcal/day)</label>
                        <input type="number" name="kcal_objective" id="kcal_objective" class="form-control" value="<?= isset($kcal_objective) ? htmlspecialchars($kcal_objective) : '' ?>" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                    </div>
                </form>
                <div class="mt-3 text-center">
                    <a href="login.php">Already have an account? Login here</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>