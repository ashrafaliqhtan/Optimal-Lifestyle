<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

$page_title = "Reports";
include 'includes/header.php';
include 'includes/sidebar.php';

// Get available report types
$report_types = [
    'user_activity' => 'User Activity Report',
    'content_performance' => 'Content Performance Report',
    'fitness_progress' => 'Fitness Progress Report',
    'nutrition_tracking' => 'Nutrition Tracking Report',
    'system_usage' => 'System Usage Report'
];

// Get recent reports
$recent_reports = [];
try {
    $stmt = $pdo->prepare("
        SELECT * FROM generated_reports
        WHERE user_id = :user_id
        ORDER BY generated_at DESC
        LIMIT 5
    ");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $recent_reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching recent reports: " . $e->getMessage();
}
?>

<div id="content">
    <?php include 'includes/topbar.php'; ?>

    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Report Generation</h1>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#newReportModal">
                <i class="fas fa-plus fa-sm text-white-50"></i> New Report
            </button>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Report Generator -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Create New Report</h6>
                    </div>
                    <div class="card-body">
                        <form id="reportForm" action="includes/generate-report.php" method="POST">
                            <div class="form-group">
                                <label for="reportType">Report Type</label>
                                <select class="form-control" id="reportType" name="reportType" required>
                                    <option value="">-- Select Report Type --</option>
                                    <?php foreach ($report_types as $value => $label): ?>
                                        <option value="<?= $value ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="startDate">Start Date</label>
                                        <input type="date" class="form-control" id="startDate" name="startDate">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="endDate">End Date</label>
                                        <input type="date" class="form-control" id="endDate" name="endDate">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="reportFormat">Format</label>
                                <select class="form-control" id="reportFormat" name="reportFormat" required>
                                    <option value="pdf">PDF</option>
                                    <option value="csv">CSV</option>
                                    <option value="excel">Excel</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="reportName">Report Name</label>
                                <input type="text" class="form-control" id="reportName" name="reportName" placeholder="Enter a name for this report" required>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="emailReport" name="emailReport">
                                    <label class="form-check-label" for="emailReport">
                                        Email me a copy of this report
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-download fa-sm text-white-50"></i> Generate Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Recently Generated</h6>
                        <a href="report-history.php" class="btn btn-sm btn-link">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php foreach ($recent_reports as $report): ?>
                            <a href="report-view.php?id=<?= $report['id'] ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= htmlspecialchars($report['report_name']) ?></h6>
                                    <small><?= formatDate($report['generated_at']) ?></small>
                                </div>
                                <p class="mb-1">
                                    <span class="badge bg-secondary"><?= strtoupper($report['report_format']) ?></span>
                                    <span class="badge bg-<?= getReportTypeColor($report['report_type']) ?>">
                                        <?= htmlspecialchars($report_types[$report['report_type']] ?? $report['report_type']) ?>
                                    </span>
                                </p>
                                <small>Size: <?= formatFileSize($report['file_size']) ?></small>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Report Templates -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Report Templates</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action template-item" data-template="weekly_user_summary">
                                <i class="fas fa-users mr-2"></i> Weekly User Summary
                            </a>
                            <a href="#" class="list-group-item list-group-item-action template-item" data-template="monthly_content_performance">
                                <i class="fas fa-chart-line mr-2"></i> Monthly Content Performance
                            </a>
                            <a href="#" class="list-group-item list-group-item-action template-item" data-template="fitness_engagement">
                                <i class="fas fa-dumbbell mr-2"></i> Fitness Engagement
                            </a>
                            <a href="#" class="list-group-item list-group-item-action template-item" data-template="nutrition_trends">
                                <i class="fas fa-utensils mr-2"></i> Nutrition Trends
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scheduled Reports -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Scheduled Reports</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#scheduleReportModal">
                    <i class="fas fa-plus fa-sm text-white-50"></i> New Schedule
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="scheduledReportsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Report Name</th>
                                <th>Type</th>
                                <th>Frequency</th>
                                <th>Next Run</th>
                                <th>Recipients</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Weekly User Activity</td>
                                <td>User Activity</td>
                                <td>Weekly</td>
                                <td><?= date('Y-m-d', strtotime('next monday')) ?></td>
                                <td>admin@example.com</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Monthly Content Report</td>
                                <td>Content Performance</td>
                                <td>Monthly</td>
                                <td><?= date('Y-m-01', strtotime('next month')) ?></td>
                                <td>admin@example.com, editor@example.com</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Report Modal -->
<div class="modal fade" id="scheduleReportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Schedule New Report</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="scheduleReportForm" action="includes/schedule-report.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="scheduleReportName">Report Name</label>
                        <input type="text" class="form-control" id="scheduleReportName" name="scheduleReportName" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="scheduleReportType">Report Type</label>
                        <select class="form-control" id="scheduleReportType" name="scheduleReportType" required>
                            <option value="">-- Select Report Type --</option>
                            <?php foreach ($report_types as $value => $label): ?>
                                <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="scheduleFrequency">Frequency</label>
                                <select class="form-control" id="scheduleFrequency" name="scheduleFrequency" required>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" id="dayOfWeekContainer" style="display: none;">
                                <label for="dayOfWeek">Day of Week</label>
                                <select class="form-control" id="dayOfWeek" name="dayOfWeek">
                                    <option value="monday">Monday</option>
                                    <option value="tuesday">Tuesday</option>
                                    <option value="wednesday">Wednesday</option>
                                    <option value="thursday">Thursday</option>
                                    <option value="friday">Friday</option>
                                </select>
                            </div>
                            
                            <div class="form-group" id="dayOfMonthContainer" style="display: none;">
                                <label for="dayOfMonth">Day of Month</label>
                                <select class="form-control" id="dayOfMonth" name="dayOfMonth">
                                    <?php for ($i = 1; $i <= 28; $i++): ?>
                                        <option value="<?= $i ?>" <?= $i == 1 ? 'selected' : '' ?>><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="scheduleFormat">Format</label>
                        <select class="form-control" id="scheduleFormat" name="scheduleFormat" required>
                            <option value="pdf">PDF</option>
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="scheduleRecipients">Recipients (comma separated)</label>
                        <textarea class="form-control" id="scheduleRecipients" name="scheduleRecipients" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="scheduleStartDate">Start Date</label>
                        <input type="date" class="form-control" id="scheduleStartDate" name="scheduleStartDate" min="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Schedule Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#scheduledReportsTable').DataTable({
        responsive: true
    });

    // Frequency change handler
    $('#scheduleFrequency').change(function() {
        const frequency = $(this).val();
        $('#dayOfWeekContainer, #dayOfMonthContainer').hide();
        
        if (frequency === 'weekly') {
            $('#dayOfWeekContainer').show();
        } else if (frequency === 'monthly') {
            $('#dayOfMonthContainer').show();
        }
    });

    // Template click handler
    $('.template-item').click(function(e) {
        e.preventDefault();
        const template = $(this).data('template');
        
        // In a real app, this would load template settings
        switch (template) {
            case 'weekly_user_summary':
                $('#reportType').val('user_activity');
                $('#reportName').val('Weekly User Summary');
                $('#startDate').val('<?= date('Y-m-d', strtotime('-7 days')) ?>');
                $('#endDate').val('<?= date('Y-m-d') ?>');
                break;
                
            case 'monthly_content_performance':
                $('#reportType').val('content_performance');
                $('#reportName').val('Monthly Content Performance');
                $('#startDate').val('<?= date('Y-m-01', strtotime('-1 month')) ?>');
                $('#endDate').val('<?= date('Y-m-t', strtotime('-1 month')) ?>');
                break;
                
            case 'fitness_engagement':
                $('#reportType').val('fitness_progress');
                $('#reportName').val('Fitness Engagement');
                $('#startDate').val('<?= date('Y-m-01') ?>');
                $('#endDate').val('<?= date('Y-m-d') ?>');
                break;
                
            case 'nutrition_trends':
                $('#reportType').val('nutrition_tracking');
                $('#reportName').val('Nutrition Trends');
                $('#startDate').val('<?= date('Y-m-01', strtotime('-3 months')) ?>');
                $('#endDate').val('<?= date('Y-m-d') ?>');
                break;
        }
        
        toastr.success('Template loaded: ' + $(this).text().trim());
    });

    // Form validation
    $('#reportForm').submit(function(e) {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        
        if (startDate && endDate && startDate > endDate) {
            e.preventDefault();
            toastr.error('Start date cannot be after end date');
        }
    });

    // Set default start date in schedule modal
    $('#scheduleStartDate').val('<?= date('Y-m-d') ?>');
});
</script>