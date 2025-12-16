// admin/modules/system/backup.php
<?php
require_once '../../../includes/auth-check.php';
require_once '../../../config/database.php';

// Check if this is a super admin
if ($_SESSION['admin_role'] !== 'super_admin') {
    die("Access denied. Only super administrators can access this feature.");
}

// Backup actions
if (isset($_POST['create_backup'])) {
    // Generate filename with timestamp
    $backupFile = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
    $backupPath = '../../../backups/' . $backupFile;
    
    // Get all tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    $output = '';
    foreach ($tables as $table) {
        // Table structure
        $output .= "--\n-- Table structure for table `$table`\n--\n";
        $output .= "DROP TABLE IF EXISTS `$table`;\n";
        $createTable = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
        $output .= $createTable['Create Table'] . ";\n\n";
        
        // Table data
        $output .= "--\n-- Dumping data for table `$table`\n--\n";
        $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $output .= "INSERT INTO `$table` VALUES(";
            $values = array_map(function($value) use ($pdo) {
                return $value === null ? 'NULL' : $pdo->quote($value);
            }, array_values($row));
            $output .= implode(',', $values) . ");\n";
        }
        $output .= "\n";
    }
    
    // Save to file
    file_put_contents($backupPath, $output);
    
    // Log activity
    $activity = $pdo->prepare("INSERT INTO AdminActivityLog (admin_id, admin_name, activity_type, activity_details) VALUES (?, ?, ?, ?)");
    $activity->execute([
        $_SESSION['admin_id'],
        $_SESSION['admin_name'],
        'Backup',
        'Created database backup: ' . $backupFile
    ]);
    
    $_SESSION['backup_success'] = "Backup created successfully: " . $backupFile;
    header("Location: backup.php");
    exit();
}

// Restore action
if (isset($_POST['restore_backup']) && !empty($_FILES['backup_file']['name'])) {
    $backupFile = $_FILES['backup_file']['tmp_name'];
    
    // Temporary variable, used to store current query
    $templine = '';
    $lines = file($backupFile);
    
    // Loop through each line
    foreach ($lines as $line) {
        // Skip it if it's a comment
        if (substr($line, 0, 2) == '--' || $line == '') {
            continue;
        }
        
        // Add this line to the current segment
        $templine .= $line;
        
        // If it has a semicolon at the end, it's the end of the query
        if (substr(trim($line), -1, 1) == ';') {
            // Perform the query
            try {
                $pdo->exec($templine);
            } catch (PDOException $e) {
                $_SESSION['backup_error'] = "Error restoring backup: " . $e->getMessage();
                header("Location: backup.php");
                exit();
            }
            
            // Reset temp variable to empty
            $templine = '';
        }
    }
    
    // Log activity
    $activity = $pdo->prepare("INSERT INTO AdminActivityLog (admin_id, admin_name, activity_type, activity_details) VALUES (?, ?, ?, ?)");
    $activity->execute([
        $_SESSION['admin_id'],
        $_SESSION['admin_name'],
        'Backup',
        'Restored database backup: ' . $_FILES['backup_file']['name']
    ]);
    
    $_SESSION['backup_success'] = "Backup restored successfully from: " . $_FILES['backup_file']['name'];
    header("Location: backup.php");
    exit();
}

// Get existing backups
$backupDir = '../../../backups/';
$backups = [];
if (is_dir($backupDir)) {
    $files = scandir($backupDir, SCANDIR_SORT_DESCENDING);
    foreach ($files as $file) {
        if (preg_match('/^backup-.*\.sql$/', $file)) {
            $backups[] = [
                'name' => $file,
                'path' => $backupDir . $file,
                'size' => filesize($backupDir . $file),
                'modified' => filemtime($backupDir . $file)
            ];
        }
    }
}

include '../../../includes/header.php';
?>

<div class="container-fluid">
    <!-- Backup Alerts -->
    <?php if (isset($_SESSION['backup_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= $_SESSION['backup_success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['backup_success']); endif; ?>
    
    <?php if (isset($_SESSION['backup_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['backup_error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['backup_error']); endif; ?>
    
    <div class="row">
        <!-- Create Backup -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Create New Backup</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Creating a backup may take several minutes depending on your database size.
                    </div>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Backup Options</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="backup_database" id="backupDatabase" checked disabled>
                                <label class="form-check-label" for="backupDatabase">Database</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="backup_files" id="backupFiles">
                                <label class="form-check-label" for="backupFiles">Website Files (Coming Soon)</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Backup Name</label>
                            <input type="text" class="form-control" value="backup-<?= date('Y-m-d-H-i-s') ?>" disabled>
                        </div>
                        
                        <button type="submit" name="create_backup" class="btn btn-primary">
                            <i class="fas fa-database me-2"></i> Create Backup Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Restore Backup -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Restore Backup</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Warning: Restoring a backup will overwrite all current data. This cannot be undone!
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Select Backup File</label>
                            <input type="file" class="form-control" name="backup_file" accept=".sql" required>
                        </div>
                        
                        <button type="submit" name="restore_backup" class="btn btn-danger" 
                                onclick="return confirm('WARNING: This will overwrite all current data. Are you sure?')">
                            <i class="fas fa-undo me-2"></i> Restore Backup
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Existing Backups -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Existing Backups</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="backupsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Backup Name</th>
                            <th>Date</th>
                            <th>Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($backups as $backup): ?>
                        <tr>
                            <td><?= $backup['name'] ?></td>
                            <td><?= date('Y-m-d H:i:s', $backup['modified']) ?></td>
                            <td><?= formatBytes($backup['size']) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?= str_replace('../../../', '../', $backup['path']) ?>" class="btn btn-primary" download>
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button class="btn btn-info" onclick="previewBackup('<?= $backup['name'] ?>')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteBackup('<?= $backup['name'] ?>')">
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

<!-- Backup Preview Modal -->
<div class="modal fade" id="backupPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Backup Preview: <span id="backupPreviewTitle"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="backupPreviewContent" style="height: 60vh; overflow: auto;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#backupsTable').DataTable({
        order: [[1, 'desc']],
        responsive: true
    });
});

function previewBackup(filename) {
    $('#backupPreviewTitle').text(filename);
    $('#backupPreviewContent').text('Loading...');
    
    $.get('backup-preview.php', { file: filename }, function(data) {
        $('#backupPreviewContent').text(data);
    });
    
    $('#backupPreviewModal').modal('show');
}

function deleteBackup(filename) {
    if (confirm('Are you sure you want to delete this backup?\n\n' + filename)) {
        $.post('backup-delete.php', { file: filename }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json');
    }
}

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
</script>

<?php include '../../../includes/footer.php'; ?>