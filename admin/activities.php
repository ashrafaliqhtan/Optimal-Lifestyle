<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

$page_title = "Activity Logs";
include 'includes/header.php';
include 'includes/sidebar.php';

// Initialize filters
$filter_user = $_GET['user'] ?? '';
$filter_type = $_GET['type'] ?? '';
$filter_date_from = $_GET['date_from'] ?? '';
$filter_date_to = $_GET['date_to'] ?? '';

// Build where clause
$where = [];
$params = [];

if (!empty($filter_user)) {
    $where[] = "al.user_id = :user_id";
    $params['user_id'] = $filter_user;
}

if (!empty($filter_type)) {
    $where[] = "al.activity_type = :activity_type";
    $params['activity_type'] = $filter_type;
}

if (!empty($filter_date_from)) {
    $where[] = "DATE(al.created_at) >= :date_from";
    $params['date_from'] = $filter_date_from;
}

if (!empty($filter_date_to)) {
    $where[] = "DATE(al.created_at) <= :date_to";
    $params['date_to'] = $filter_date_to;
}

$where_clause = $where ? "WHERE " . implode(" AND ", $where) : "";

// Get activity logs
$activities = [];
$total_activities = 0;

try {
    // Count total activities for pagination
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM activity_logs al
        $where_clause
    ");
    $stmt->execute($params);
    $total_activities = $stmt->fetchColumn();

    // Get paginated activities
    $per_page = 20;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($page - 1) * $per_page;

    $stmt = $pdo->prepare("
        SELECT 
            al.*,
            u.name as user_name,
            u.email as user_email
        FROM activity_logs al
        LEFT JOIN usersmanage u ON al.user_id = u.id
        $where_clause
        ORDER BY al.created_at DESC
        LIMIT :offset, :per_page
    ");
    
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get distinct activity types for filter dropdown
    $stmt = $pdo->query("SELECT DISTINCT activity_type FROM activity_logs ORDER BY activity_type");
    $activity_types = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Get users for filter dropdown
    $stmt = $pdo->query("
        SELECT u.id, u.name 
        FROM usersmanage u
        JOIN activity_logs al ON u.id = al.user_id
        GROUP BY u.id
        ORDER BY u.name
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Error fetching activity logs: " . $e->getMessage();
}
?>

<div id="content">
    <?php include 'includes/topbar.php'; ?>

    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Activity Logs</h1>
            <div>
                <button class="btn btn-sm btn-outline-danger" id="clearLogsBtn">
                    <i class="fas fa-trash fa-sm"></i> Clear Logs
                </button>
                <button class="btn btn-sm btn-primary shadow-sm ml-2" id="exportLogsBtn">
                    <i class="fas fa-download fa-sm text-white-50"></i> Export
                </button>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
            </div>
            <div class="card-body">
                <form id="activityFilters" method="get" action="activities.php">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filterUser">User</label>
                                <select class="form-control" id="filterUser" name="user">
                                    <option value="">All Users</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>" <?= $filter_user == $user['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($user['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filterType">Activity Type</label>
                                <select class="form-control" id="filterType" name="type">
                                    <option value="">All Types</option>
                                    <?php foreach ($activity_types as $type): ?>
                                        <option value="<?= $type ?>" <?= $filter_type == $type ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($type) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filterDateFrom">Date From</label>
                                <input type="date" class="form-control" id="filterDateFrom" name="date_from" value="<?= $filter_date_from ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filterDateTo">Date To</label>
                                <input type="date" class="form-control" id="filterDateTo" name="date_to" value="<?= $filter_date_to ?>">
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter fa-sm text-white-50"></i> Apply Filters
                        </button>
                        <a href="activities.php" class="btn btn-secondary ml-2">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Activity Logs Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Activity Logs</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                        <div class="dropdown-header">Actions:</div>
                        <a class="dropdown-item" href="#" id="refreshActivities">
                            <i class="fas fa-sync-alt mr-2"></i>Refresh
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" id="printActivities">
                            <i class="fas fa-print mr-2"></i>Print
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="activitiesTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Activity</th>
                                <th>Details</th>
                                <th>IP Address</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activities as $activity): ?>
                            <tr>
                                <td><?= $activity['id'] ?></td>
                                <td>
                                    <?php if ($activity['user_id']): ?>
                                        <a href="user-view.php?id=<?= $activity['user_id'] ?>">
                                            <?= htmlspecialchars($activity['user_name']) ?>
                                        </a>
                                    <?php else: ?>
                                        System
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($activity['activity_type']) ?></td>
                                <td><?= htmlspecialchars(truncateString($activity['details'], 50)) ?></td>
                                <td><?= htmlspecialchars($activity['ip_address']) ?></td>
                                <td><?= formatDateTime($activity['created_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="dataTables_info">
                            Showing <?= $offset + 1 ?> to <?= min($offset + $per_page, $total_activities) ?> of <?= $total_activities ?> entries
                        </div>
                    </div>
                    <div class="col-md-6">
                        <nav class="float-right">
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Previous</a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= ceil($total_activities / $per_page); $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < ceil($total_activities / $per_page)): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Details Modal -->
<div class="modal fade" id="activityDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Activity Details</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>User</h6>
                        <p id="detailUser"></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Activity Type</h6>
                        <p id="detailType"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>IP Address</h6>
                        <p id="detailIp"></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Timestamp</h6>
                        <p id="detailTimestamp"></p>
                    </div>
                </div>
                <div class="mb-3">
                    <h6>Details</h6>
                    <pre id="detailDetails" class="bg-light p-3 rounded"></pre>
                </div>
                <div class="mb-3">
                    <h6>Additional Data</h6>
                    <pre id="detailData" class="bg-light p-3 rounded"></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#activitiesTable').DataTable({
        responsive: true,
        paging: false,
        info: false,
        searching: false,
        order: [],
        columnDefs: [
            { responsivePriority: 1, targets: 2 }, // Activity type
            { responsivePriority: 2, targets: 1 }, // User
            { responsivePriority: 3, targets: 5 }, // Timestamp
            { targets: 0, visible: false } // Hide ID column
        ]
    });

    // View activity details
    $('#activitiesTable').on('click', 'tr', function() {
        const row = $(this);
        const id = row.find('td:eq(0)').text();
        const user = row.find('td:eq(1)').text().trim();
        const type = row.find('td:eq(2)').text().trim();
        const details = row.find('td:eq(3)').text().trim();
        const ip = row.find('td:eq(4)').text().trim();
        const timestamp = row.find('td:eq(5)').text().trim();

        $('#detailUser').text(user);
        $('#detailType').text(type);
        $('#detailIp').text(ip);
        $('#detailTimestamp').text(timestamp);
        $('#detailDetails').text(details);
        
        // In a real app, this would fetch additional data via AJAX
        $('#detailData').text('No additional data available');
        
        $('#activityDetailsModal').modal('show');
    });

    // Clear logs confirmation
    $('#clearLogsBtn').click(function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete all activity logs. This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, clear all logs!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'includes/clear-activity-logs.php';
            }
        });
    });

    // Export logs
    $('#exportLogsBtn').click(function(e) {
        e.preventDefault();
        
        // Build export URL with current filters
        const params = new URLSearchParams(window.location.search);
        params.set('export', 'csv');
        
        window.location.href = 'includes/export-activity-logs.php?' + params.toString();
    });

    // Refresh button
    $('#refreshActivities').click(function(e) {
        e.preventDefault();
        window.location.reload();
    });

    // Print button
    $('#printActivities').click(function(e) {
        e.preventDefault();
        window.print();
    });

    // Date validation
    $('#activityFilters').submit(function(e) {
        const dateFrom = $('#filterDateFrom').val();
        const dateTo = $('#filterDateTo').val();
        
        if (dateFrom && dateTo && dateFrom > dateTo) {
            e.preventDefault();
            toastr.error('"Date From" cannot be after "Date To"');
        }
    });
});
</script>