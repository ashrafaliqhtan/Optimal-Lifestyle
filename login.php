<?php
include "config.php";

if (isset($_POST['submit'])) {
    // Check if email and password are set and not empty
    if (isset($_POST['email']) && isset($_POST['password']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        $email = mysqli_real_escape_string($connection, $_POST['email']);
        $password = mysqli_real_escape_string($connection, $_POST['password']);

        // Correct table name 'users' might need to be replaced with the actual table name
        $select_query = "SELECT * FROM Users WHERE email = '$email';";
        
        // Check if the query runs correctly
        $select_query_result = mysqli_query($connection, $select_query);
        
        // Check if the email exists in the database
        if ($select_query_result && mysqli_num_rows($select_query_result) > 0) {
            $user_data = mysqli_fetch_assoc($select_query_result);
            
            // Verify password using password_verify
            if (password_verify($password, $user_data['password'])) {
                $id = $user_data['user_id'];
                $name = $user_data['name'];

                // Start session and set session variables
                session_start();
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;

                // Set cookies for user data
                setcookie("user_id", $id, time() + (86400 * 30), "/");
                setcookie("user_name", $name, time() + (86400 * 30), "/");

                // Redirect to account page
                header("Location: account-page.php");
                exit();
            } else {
                echo "<script type='text/javascript'>alert('Wrong password.');</script>";
            }
        } else {
            echo "<script type='text/javascript'>alert('No user found with that email address.');</script>";
        }
    } else {
        echo "<script type='text/javascript'>alert('Please enter both email and password.');</script>";
    }
}
?>
