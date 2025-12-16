// admin/modules/notifications/notifications.php
<?php
require_once '../../../includes/auth-check.php';
require_once '../../../config/database.php';

// Get notifications
$query = "SELECT * FROM Notifications 
          WHERE user_id = :user_id OR user_id IS NULL
          ORDER BY created_at DESC
          LIMIT 100";
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $_SESSION['admin_id']]);
$notifications = $stmt->fetchAll();

// Mark all as read
if (isset($_POST['mark_read'])) {
    $update = $pdo->prepare("UPDATE Notifications SET is_read = TRUE WHERE (user_id = :user_id OR user_id IS NULL) AND is_read = FALSE");
    $update->execute([':user_id' => $_SESSION['admin_id']]);
    
    // Log activity
    $activity = $pdo->prepare("INSERT INTO AdminActivityLog (admin_id, admin_name, activity_type, activity_details) VALUES (?, ?, ?, ?)");
    $activity->execute([
        $_SESSION['admin_id'],
        $_SESSION['admin_name'],
        'Notifications',
        'Marked all notifications as read'
    ]);
    
    header("Location: notifications.php");
    exit();
}

// Send notification
if (isset($_POST['send_notification'])) {
    $recipient = $_POST['recipient'] === 'all' ? null : (int)$_POST['recipient'];
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $type = $_POST['type'];
    
    $insert = $pdo->prepare("INSERT INTO Notifications (user_id, title, message, notification_type) VALUES (?, ?, ?, ?)");
    $insert->execute([$recipient, $title, $message, $type]);
    
    // Log activity
    $activity = $pdo->prepare("INSERT INTO AdminActivityLog (admin_id, admin_name, activity_type, activity_details) VALUES (?, ?, ?, ?)");
    $activity->execute([
        $_SESSION['admin_id'],
        $_SESSION['admin_name'],
        'Notifications',
        'Sent notification: ' . substr($title, 0, 50)
    ]);
    
    header("Location: notifications.php");
    exit();
}

include '../../../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Notification List -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Notifications</h6>
                    <form method="POST">
                        <button type="submit" name="mark_read" class="btn btn-sm btn-primary">
                            <i class="fas fa-check-circle"></i> Mark All as Read
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php foreach ($notifications as $notification): ?>
                        <a href="#" class="list-group-item list-group-item-action <?= !$notification['is_read'] ? 'list-group-item-warning' : '' ?>">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">
                                    <span class="badge bg-<?= getNotificationBadge($notification['notification_type']) ?> me-1">
                                        <?= ucfirst($notification['notification_type']) ?>
                                    </span>
                                    <?= htmlspecialchars($notification['title']) ?>
                                </h5>
                                <small><?= formatDateTime($notification['created_at']) ?></small>
                            </div>
                            <p class="mb-1"><?= htmlspecialchars($notification['message']) ?></p>
                            <?php if ($notification['user_id']): ?>
                            <small>Sent to: <?= getUserName($notification['user_id']) ?></small>
                            <?php else: ?>
                            <small>Sent to all users</small>
                            <?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Send Notification -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Send Notification</h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Recipient</label>
                            <select class="form-select" name="recipient">
                                <option value="all">All Users</option>
                                <option value="admins">Administrators Only</option>
                                <optgroup label="Specific User">
                                    <?php 
                                    $users = $pdo->query("SELECT id, name FROM usersmanage ORDER BY name")->fetchAll();
                                    foreach ($users as $user): 
                                    ?>
                                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notification Type</label>
                            <select class="form-select" name="type">
                                <option value="info">Information</option>
                                <option value="warning">Warning</option>
                                <option value="error">Error</option>
                                <option value="success">Success</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" name="message" rows="4" required></textarea>
                        </div>
                        <button type="submit" name="send_notification" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Notification
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Notification Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notification Settings</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                            <label class="form-check-label" for="emailNotifications">Email Notifications</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="systemNotifications" checked>
                            <label class="form-check-label" for="systemNotifications">System Notifications</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="mobileNotifications">
                            <label class="form-check-label" for="mobileNotifications">Mobile Push Notifications</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>