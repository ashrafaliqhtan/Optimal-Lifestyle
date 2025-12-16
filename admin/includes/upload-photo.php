<?php
// includes/upload-photo.php

session_start();

require_once 'auth-check.php';
require_once '../config/database.php';

// Check if the user is authenticated
if (!isset($_SESSION['admin_id'])) {
    die("User not authenticated.");
}

$user_id = $_SESSION['admin_id'];

// Check if a file was uploaded without errors
if (!isset($_FILES['profilePhoto']) || $_FILES['profilePhoto']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = "Error uploading file.";
    header("Location: ../profile.php");
    exit;
}

$file = $_FILES['profilePhoto'];

// Validate file size (maximum 2MB)
$maxSize = 2 * 1024 * 1024; // 2MB in bytes
if ($file['size'] > $maxSize) {
    $_SESSION['error'] = "File size exceeds the maximum allowed size of 2MB.";
    header("Location: ../profile.php");
    exit;
}

// Validate file MIME type (allow only JPG, PNG, and GIF)
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedMimeTypes)) {
    $_SESSION['error'] = "Invalid file type. Only JPG, PNG, and GIF files are allowed.";
    header("Location: ../profile.php");
    exit;
}

// Generate a unique file name to avoid overwriting existing files
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$newFileName = 'profile_' . $user_id . '_' . time() . '.' . $extension;

// Define the upload directory (ensure this directory exists and is writable)
$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$destination = $uploadDir . $newFileName;

// Move the uploaded file to the destination
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    $_SESSION['error'] = "Failed to move uploaded file.";
    header("Location: ../profile.php");
    exit;
}

// Update the user's profile image in the database
try {
    $stmt = $pdo->prepare("UPDATE usersmanage SET profile_image = :profile_image WHERE id = :id");
    $stmt->execute([
        'profile_image' => $newFileName,
        'id' => $user_id
    ]);
    $_SESSION['success'] = "Profile photo updated successfully.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    // Optionally, remove the uploaded file if the database update fails
    if (file_exists($destination)) {
        unlink($destination);
    }
}

// Redirect back to the profile page
header("Location: ../profile.php");
exit;
?>