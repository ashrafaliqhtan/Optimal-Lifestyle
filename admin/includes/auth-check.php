<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Check user role if needed
if (isset($_SESSION['admin_role'])) {
    $allowed_roles = ['admin', 'super_admin'];
    if (!in_array($_SESSION['admin_role'], $allowed_roles)) {
        header("Location: unauthorized.php");
        exit();
    }
}
?>