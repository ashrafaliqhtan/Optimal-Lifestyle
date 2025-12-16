<?php
session_start();
require_once '../config.php';

if (isset($_POST['user']) && isset($_POST['pass'])) {
    $uuser = mysqli_real_escape_string($link, $_POST['user']); // Sanitize username input
    $upass = $_POST['pass']; // Get password from POST (will hash and verify later)

    // Query to get the user data from the database
    $sql = mysqli_query($link, "SELECT id, password, user_type FROM usersmanage WHERE user='$uuser'");
    $user_found = mysqli_fetch_assoc($sql);

    if (!$user_found) {
        failed(); // User not found or incorrect credentials
        exit();
    } else {
        // Verify the password using password_verify
        if (password_verify($upass, $user_found['password'])) {
            $_SESSION['user'] = $uuser; // Set session for the user

            // Check user type and redirect accordingly
            if ($user_found['user_type'] === 'admin') {
                admin_dashboard(); // Redirect to the admin dashboard
            } else {
                successful(); // Redirect to the user homepage
            }
        } else {
            failed(); // Incorrect password
            exit();
        }
    }
} elseif (!isset($_SESSION['user'])) {
    // If no user is logged in, redirect to login page
    failed();
    exit();
}

function failed()
{
    // Set error message and redirect to the login page
    $_SESSION['errorMessage'] = "Invalid login credentials.";
    header('location: ../index.php'); // Redirect to login page
    exit();
}

function successful()
{
    // Redirect to the user homepage
    header('location: ../home/index.php'); 
    exit();
}

function admin_dashboard()
{
    // Redirect to the admin dashboard
    header('location: ../admin/index.php'); 
    exit();
}
?>
