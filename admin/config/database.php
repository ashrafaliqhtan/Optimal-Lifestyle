<?php
/**
 * Database Configuration File
 * 
 * This file handles the database connection using PDO with enhanced security
 */

// Database configuration - should ideally be in environment variables
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lifestyle1');
define('DB_CHARSET', 'utf8mb4');

// Create connection with enhanced settings
try {
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // Disable emulation for security
        PDO::ATTR_PERSISTENT         => false, // Disable persistent connections
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Set timezone if needed
    $pdo->exec("SET time_zone = '+00:00'");
    
} catch(PDOException $e) {
    // Log the error securely instead of showing details
    error_log("Database connection error: " . $e->getMessage());
    
    // Display generic error message
    die("Could not connect to the database. Please try again later.");
}

// Function to safely close connection (optional)
function closeDatabaseConnection(&$pdo) {
    $pdo = null;
}