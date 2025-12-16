<?php
session_start();
require_once '../config.php';

if (
    isset($_POST['user']) && 
    isset($_POST['pass']) && 
    isset($_POST['name']) && 
    isset($_POST['email']) && 
    isset($_POST['height']) && 
    isset($_POST['initial_weight']) && 
    isset($_POST['gender'])
) {
    // Sanitize all inputs
    $utype = (isset($_POST['is_admin']) && $_POST['is_admin'] === 'on') ? 'admin' : 'user';
    $uuser = mysqli_real_escape_string($link, $_POST['user']);
    $uname = mysqli_real_escape_string($link, $_POST['name']);
    $uemail = mysqli_real_escape_string($link, $_POST['email']);
    $uheight = mysqli_real_escape_string($link, $_POST['height']);
    $uinitial_weight = mysqli_real_escape_string($link, $_POST['initial_weight']);
    $upass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
    $ugender = strtolower(mysqli_real_escape_string($link, $_POST['gender']));

    // Validate inputs
    if (!validate_inputs($ugender, $uemail, $uheight, $uinitial_weight)) {
        failed('Invalid input data.');
        exit();
    }

    try {
        // Check if username already exists
        $check_sql = "SELECT user FROM usersmanage WHERE user = '$uuser'";
        $check_result = mysqli_query($link, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            failed('Username already exists.');
            exit();
        }

        // Query to create user in the database
        $sql = "INSERT INTO usersmanage (name, email, password, user, gender, initial_weight, height, user_type) 
                VALUES ('$uname', '$uemail', '$upass', '$uuser', '$ugender', '$uinitial_weight', '$uheight', '$utype')";
        $result = mysqli_query($link, $sql);

        if ($result === true) {
            success('Account created successfully.');
        } else {
            failed('Failed to create account. Please try again.');
            exit();
        }
    } catch (Exception $e) {
        failed('Error occurred: ' . $e->getMessage());
    }
} else {
    failed('Some required fields are missing.');
    exit();
}

function failed($text)
{
    $_SESSION['registerError'] = $text;
    header('Location: ../index.php');
    exit();
}

function success($text)
{
    $_SESSION['successMessage'] = $text;
    header('Location: ../index.php');
    exit();
}

function validate_inputs($ugender, $uemail, $uheight, $uinitial_weight)
{
    // Validate gender
    if ($ugender !== 'm' && $ugender !== 'f') {
        return false;
    }

    // Validate email format
    if (!filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    // Validate height and initial weight to be numeric and positive
    if (!is_numeric($uheight) || $uheight <= 0 || !is_numeric($uinitial_weight) || $uinitial_weight <= 0) {
        return false;
    }

    return true;
}
?>