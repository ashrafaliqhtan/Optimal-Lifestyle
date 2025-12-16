<?php
session_start();
require_once 'config/database.php';

// Log activity before destroying session
if (isset($_SESSION['admin_id'])) {
    try {
        $activity_stmt = $pdo->prepare("INSERT INTO AdminActivityLog (admin_id, admin_name, activity_type, activity_details) VALUES (?, ?, ?, ?)");
        $activity_stmt->execute([
            $_SESSION['admin_id'],
            $_SESSION['admin_name'],
            'Logout',
            'Logged out from admin dashboard'
        ]);
    } catch (PDOException $e) {
        // Log error if unable to record activity
        error_log("Error logging admin logout: " . $e->getMessage());
    }
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>