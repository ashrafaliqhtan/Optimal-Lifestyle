<?php
session_start();
include "db_connection.php";

if (isset($_POST['submit'])) {
    // Check required fields
    $name = trim(mysqli_real_escape_string($connection, $_POST['name']));
    $email = trim(mysqli_real_escape_string($connection, $_POST['email']));
    $password = trim(mysqli_real_escape_string($connection, $_POST['password']));

    if (empty($email) || empty($password) || empty($name)) {
        die("All fields are required.");
    }

    // Validate email uniqueness
    $checkEmailQuery = "SELECT * FROM Users WHERE email = '$email'";
    $result = mysqli_query($connection, $checkEmailQuery);
    if (mysqli_num_rows($result) > 0) {
        die("This email is already registered. Please try another.");
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $insertQuery = "INSERT INTO Users (user_id, calorie_id, name, email, password) 
                    VALUES (NULL, 0, '$name', '$email', '$hashedPassword')";
    if (mysqli_query($connection, $insertQuery)) {
        $_SESSION['email'] = $email;
        header("Location: registration-success.php");
        exit();
    } else {
        die("Database error: " . mysqli_error($connection));
    }
}
?>
