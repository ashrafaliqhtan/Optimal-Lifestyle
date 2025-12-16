<?php
/**
 * Admin Dashboard Header
 * Includes meta tags, CSS, and initial HTML structure
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Get admin details from session
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_email = $_SESSION['admin_email'] ?? '';
$admin_role = $_SESSION['admin_role'] ?? 'admin';
$admin_avatar = $_SESSION['admin_avatar'] ?? 'https://www.gravatar.com/avatar/' . md5(strtolower($admin_email)) . '?s=200&d=mp';

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Optimal Lifestyle Admin Dashboard">
    <meta name="author" content="Optimal Lifestyle">
    <meta name="robots" content="noindex, nofollow">

    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' | ' : '' ?>Optimal Lifestyle Admin</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">

    <!-- Font Awesome -->
<!-- Replace all local vendor links with CDN versions -->
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

<!-- Datepicker CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">

<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.css" rel="stylesheet">

    <!-- Custom Admin CSS -->
    <link href="./assets/css/admin.css" rel="stylesheet">

    <!-- DataTables CSS -->


    <!-- Custom styles for this page -->
    <?php if (isset($page_specific_css)): ?>
        <link href="<?php echo htmlspecialchars($page_specific_css); ?>" rel="stylesheet">
    <?php endif; ?>

    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #4e73df;
            --secondary: #858796;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
        }
        
        .stat-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .toast {
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-item .nav-link {
            transition: all 0.3s;
        }
        
        .sidebar .nav-item .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }
        
        .dropdown-menu {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">