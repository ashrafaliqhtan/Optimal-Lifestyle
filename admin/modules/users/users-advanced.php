// admin/modules/users/users-advanced.php
<?php
require_once '../../../includes/auth-check.php';
require_once '../../../config/database.php';

// Advanced filters
$filters = [
    'status' => $_GET['status'] ?? 'all',
    'role' => $_GET['role'] ?? 'all',
    'registration_date_from' => $_GET['registration_date_from'] ?? '',
    'registration_date_to' => $_GET['registration_date_to'] ?? '',
    'last_activity_from' => $_GET['last_activity_from'] ?? '',
    'last_activity_to' => $_GET['last_activity_to'] ?? '',
    'search' => $_GET['search'] ?? ''
];

// Build query with filters
$query = "SELECT u.*, 
          (SELECT MAX(activity_date) FROM UserActivity WHERE user_id = u.id) as last_activity,
          (SELECT COUNT(*) FROM UserActivity WHERE user_id = u.id) as activity_count
          FROM usersmanage u WHERE 1=1";
$params = [];

// Apply filters
if ($filters['status'] !== 'all') {
    $query .= " AND u.status = :status";
    $params[':status'] = $filters['status'];
}

if ($filters['role'] !== 'all') {
    $query .= " AND u.user_type = :role";
    $params[':role'] = $filters['role'];
}

// Add date range filters and search...

// Execute query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Get statistics for dashboard
$stats = [
    'total' => $pdo->query("SELECT COUNT(*) FROM usersmanage")->fetchColumn(),
    'active' => $pdo->query("SELECT COUNT(DISTINCT user_id) FROM UserActivity WHERE activity_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn(),
    'new_today' => $pdo->query("SELECT COUNT(*) FROM usersmanage WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
    'inactive' => $pdo->query("SELECT COUNT(*) FROM usersmanage WHERE id NOT IN (SELECT DISTINCT user_id FROM UserActivity WHERE activity_date >= DATE_SUB(NOW(), INTERVAL 90 DAY))")->fetchColumn()
];

include '../../../includes/header.php';
?>

<div class="container-fluid">
    <!-- User Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Other stat cards... -->
    </div>

    <!-- Advanced Filter Panel -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Advanced Filters</h6>
            <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="fas fa-filter"></i> Toggle Filters
            </button>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="all" <?= $filters['status'] === 'all' ? 'selected' : '' ?>>All Statuses</option>
                            <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="suspended" <?= $filters['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                            <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        </select>
                    </div>
                    <!-- More filter fields... -->
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Apply Filters</button>
                        <a href="users-advanced.php" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">User Management</h6>
            <div>
                <a href="user-export.php?<?= http_build_query($_GET) ?>" class="btn btn-sm btn-success me-2">
                    <i class="fas fa-file-excel"></i> Export
                </a>
                <a href="users-add.php" class="btn btn-sm btn-primary">
                    <i class="fas fa-user-plus"></i> Add User
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="advancedUserTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Activity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?= getAvatar($user['email']) ?>" class="rounded-circle me-2" width="32" height="32">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($user['name']) ?></div>
                                        <div class="text-muted small">@<?= htmlspecialchars($user['username']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="badge bg-<?= $user['user_type'] === 'admin' ? 'warning' : 'info' ?>">
                                    <?= ucfirst($user['user_type']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= getUserStatusBadge($user['status']) ?>">
                                    <?= ucfirst($user['status'] ?? 'active') ?>
                                </span>
                            </td>
                            <td>
                                <?= $user['last_activity'] ? formatDate($user['last_activity']) : 'Never' ?>
                                <div class="text-muted small"><?= $user['activity_count'] ?> activities</div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="user-view.php?id=<?= $user['id'] ?>" class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="user-edit.php?id=<?= $user['id'] ?>" class="btn btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-warning" title="Send Message" data-bs-toggle="modal" data-bs-target="#messageModal" data-userid="<?= $user['id'] ?>">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    <button class="btn btn-danger delete-user" data-id="<?= $user['id'] ?>" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Message to User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="messageForm" method="POST" action="send-message.php">
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="messageUserId">
                    <div class="mb-3">
                        <label for="messageSubject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="messageSubject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="messageContent" class="form-label">Message</label>
                        <textarea class="form-control" id="messageContent" name="content" rows="5" required></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="sendEmailCopy" name="send_email">
                        <label class="form-check-label" for="sendEmailCopy">Send email copy</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkActionsModal" tabindex="-1" aria-hidden="true">
    <!-- Modal content for bulk actions -->
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable with advanced features
    $('#advancedUserTable').DataTable({
        dom: '<"top"lfB>rt<"bottom"ip>',
        buttons: [
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Columns',
                columns: ':not(.no-hide)'
            },
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copy'
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print'
            }
        ],
        responsive: true,
        stateSave: true,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 25
    });

    // Message modal user ID setup
    $('#messageModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var userId = button.data('userid');
        $('#messageUserId').val(userId);
    });

    // Bulk action handlers
    $('#bulkActionBtn').click(function() {
        var selected = $('.user-checkbox:checked').length;
        if (selected > 0) {
            $('#bulkActionsModal').modal('show');
        } else {
            alert('Please select at least one user');
        }
    });
});
</script>

<?php include '../../../includes/footer.php'; ?>