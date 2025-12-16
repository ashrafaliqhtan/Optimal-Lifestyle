<?php
session_start();
require_once 'config/database.php';

// Initialize message variables
$message = "";
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Validate email input
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        try {
            // Check if the email exists in the usersmanage table
            $stmt = $pdo->prepare("SELECT id FROM usersmanage WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Generate a unique token
                $token = bin2hex(random_bytes(16));
                // Set token expiration to one hour from now
                $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

                // Insert token into password_resets table
                $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
                $stmt->execute([$user['id'], $token, $expires_at]);

                // Construct the reset link (update domain as needed)
                $resetLink = "https://yourdomain.com/reset-password.php?token=" . urlencode($token);

                // Prepare the email content
                $subject = "Password Reset Request";
                $message_body = "You have requested a password reset. Please click the link below to reset your password:\n\n" . $resetLink . "\n\nIf you did not request a password reset, please ignore this email.";

                // Send the email (ensure mail() is configured or replace with your mailing library)
                if (mail($email, $subject, $message_body)) {
                    $message = "A password reset link has been sent to your email address.";
                } else {
                    $errors[] = "Failed to send email. Please try again later.";
                }
            } else {
                $errors[] = "No account found with that email address.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        } catch (Exception $e) {
            $errors[] = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Optimal Lifestyle</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .forgot-container {
            max-width: 500px;
            margin: 0 auto;
            margin-top: 100px;
        }
        .forgot-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
        .forgot-card-header {
            background-color: #ffc107;
            color: #fff;
            text-align: center;
            padding: 15px;
            border-radius: 10px 10px 0 0;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container forgot-container">
        <div class="card forgot-card">
            <div class="card-header forgot-card-header">
                <h4><i class="fas fa-unlock-alt"></i> Forgot Password</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <div><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Enter your email address</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="yourname@example.com" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-paper-plane"></i> Send Reset Link
                        </button>
                    </div>
                </form>
                <div class="mt-3 text-center">
                    <a href="login.php">Back to Login</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>