<?php
session_start();
require_once 'config/database.php';

// إعادة التوجيه إذا كان المستخدم مسجلاً للدخول مسبقًا
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// التعامل مع إرسال نموذج تسجيل الدخول
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    try {
        // التحقق من وجود المستخدم وصلاحية نوعه
        $stmt = $pdo->prepare("SELECT * FROM usersmanage WHERE email = ? AND user_type = 'admin' LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            // تعيين متغيرات الجلسة
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_role'] = $admin['user_type'];
            
            // (اختياري) تحديث آخر تسجيل دخول
            // $update_stmt = $pdo->prepare("UPDATE usersmanage SET last_login = NOW() WHERE id = ?");
            // $update_stmt->execute([$admin['id']]);
            
            // إعادة التوجيه إلى لوحة التحكم
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Invalid email or password.";
        }
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optimal Lifestyle - Admin Login</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">
    
    <style>
        .login-container {
            max-width: 400px;
            margin: 0 auto;
            margin-top: 100px;
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-logo img {
            max-height: 80px;
        }
        
        .login-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .login-card-header {
            background-color: #28a745;
            color: white;
            text-align: center;
            padding: 15px;
            border-radius: 10px 10px 0 0 !important;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container login-container">
        <div class="login-logo">
            <img src="../assets/images/logo.png" alt="Optimal Lifestyle Logo">
        </div>
        
        <div class="card login-card">
            <div class="card-header login-card-header">
                <h4><i class="fas fa-lock"></i> Admin Login</h4>
            </div>
            
            <div class="card-body">
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <a href="forgot-password.php">Forgot your password?</a>
                </div>
                
                <div class="text-center mt-3">
                    <a href="register.php">Register here</a>           
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // تبديل إظهار/إخفاء كلمة السر
        document.querySelector('.toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>