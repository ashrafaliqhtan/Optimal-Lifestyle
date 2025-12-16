<?php
require_once 'includes/auth-check.php';
//require_once 'includes/admin-check.php';
require_once 'config/database.php';

// Check if ID parameter exists
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "User ID not specified";
    header("Location: users.php");
    exit();
}

$user_id = (int)$_GET['id'];

// Prevent self-deletion
if ($user_id === (int)$_SESSION['user_id']) {
    $_SESSION['error'] = "You cannot delete your own account";
    header("Location: users.php");
    exit();
}

try {
    // Begin transaction
    $pdo->beginTransaction();

    // First check if user exists
    $stmt = $pdo->prepare("SELECT id FROM usersmanage WHERE id = ?");
    $stmt->execute([$user_id]);
    
    if ($stmt->rowCount() === 0) {
        $_SESSION['error'] = "User not found";
        header("Location: users.php");
        exit();
    }

    // Delete related records first (example: user activities)
    // $pdo->prepare("DELETE FROM user_activities WHERE user_id = ?")->execute([$user_id]);
    
    // Then delete the user
    $stmt = $pdo->prepare("DELETE FROM usersmanage WHERE id = ?");
    $stmt->execute([$user_id]);

    // Commit transaction
    $pdo->commit();

    $_SESSION['success'] = "User deleted successfully";
    
} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    
    error_log("User deletion failed: " . $e->getMessage());
    $_SESSION['error'] = "Failed to delete user. Please try again.";
} catch (Exception $e) {
    error_log("Error in user deletion: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred";
}

header("Location: users.php");
exit();
?>