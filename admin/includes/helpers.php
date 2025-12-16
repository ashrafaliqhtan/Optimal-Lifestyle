<?php
/**
 * Helper functions for the admin dashboard
 */

/**
 * Calculate percentage change between two values
 */
function getPercentageChange($current, $previous) {
    if ($previous == 0) return 0;
    return round((($current - $previous) / $previous) * 100, 1);
}

/**
 * Format date in a readable way
 */
function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

/**
 * Format datetime in a readable way
 */
function formatDateTime($datetime) {
    return date('M j, Y g:i a', strtotime($datetime));
}

/**
 * Get Gravatar URL for email
 */
function getGravatar($email, $size = 80) {
    $hash = md5(strtolower(trim($email)));
    return "https://www.gravatar.com/avatar/$hash?s=$size&d=mp";
}

/**
 * Get appropriate color for activity type
 */
function getActivityColor($type) {
    $colors = [
        'Login' => 'info',
        'Logout' => 'secondary',
        'Create' => 'success',
        'Update' => 'primary',
        'Delete' => 'danger',
        'Settings' => 'warning',
        'Registration' => 'success',
        'Password Reset' => 'warning',
        'User Registration' => 'primary',
        'Content Creation' => 'info',
        'Workout Logged' => 'success',
        'Meal Logged' => 'warning'
    ];
    return $colors[$type] ?? 'primary';
}

/**
 * Get appropriate icon for activity type
 */
function getActivityIcon($type) {
    $icons = [
        'Login' => 'sign-in-alt',
        'Logout' => 'sign-out-alt',
        'Create' => 'plus-circle',
        'Update' => 'edit',
        'Delete' => 'trash',
        'Settings' => 'cog',
        'Registration' => 'user-plus',
        'Password Reset' => 'key',
        'User Registration' => 'user-plus',
        'Content Creation' => 'file-alt',
        'Workout Logged' => 'dumbbell',
        'Meal Logged' => 'utensils'
    ];
    return $icons[$type] ?? 'bell';
}

/**
 * Truncate string with ellipsis
 */
function truncateString($string, $length = 100) {
    if (strlen($string) > $length) {
        return substr($string, 0, $length) . '...';
    }
    return $string;
}

/**
 * Generate a random password
 */
function generatePassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Check if string is a valid date
 */
function isValidDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Redirect with message
 */
function redirectWithMessage($url, $message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header("Location: $url");
    exit();
}

/**
 * Display flash message
 */
function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        echo '<div class="alert alert-' . htmlspecialchars($type) . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($message);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

/**
 * Get status badge class
 */
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'published': return 'bg-success';
        case 'draft': return 'bg-secondary';
        case 'archived': return 'bg-warning';
        default: return 'bg-info';
    }
}

/**
 * Get workout status badge
 */
function getWorkoutStatusBadge($status) {
    switch ($status) {
        case 'completed': return 'bg-success';
        case 'in_progress': return 'bg-primary';
        case 'skipped': return 'bg-warning';
        case 'planned': return 'bg-info';
        default: return 'bg-secondary';
    }
}

/**
 * Get difficulty badge
 */
function getDifficultyBadge($difficulty) {
    switch ($difficulty) {
        case 'beginner': return 'bg-success';
        case 'intermediate': return 'bg-primary';
        case 'advanced': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

/**
 * Get gender label
 */
function getGenderLabel($gender) {
    switch ($gender) {
        case 'M': return 'Male';
        case 'F': return 'Female';
        case 'NB': return 'Non-binary';
        case 'O': return 'Other';
        default: return 'Unknown';
    }
}

/**
 * Get random color for charts
 */
function getRandomColor() {
    $colors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
        '#5a5c69', '#858796', '#dddfeb', '#f8f9fc', '#e83e8c',
        '#6f42c1', '#fd7e14', '#20c997', '#17a2b8', '#ffc107'
    ];
    return $colors[array_rand($colors)];
}