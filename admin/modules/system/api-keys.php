// admin/modules/system/api-keys.php
<?php
require_once '../../../includes/auth-check.php';
require_once '../../../config/database.php';

// Generate new API key
if (isset($_POST['generate_key'])) {
    $apiKey = bin2hex(random_bytes(32));
    $apiSecret = password_hash(bin2hex(random_bytes(64)), PASSWORD_BCRYPT);
    $description = trim($_POST['description']);
    $permissions = json_encode($_POST['permissions'] ?? []);
    $expiry = $_POST['expiry'] ? date('Y-m-d H:i:s', strtotime($_POST['expiry'])) : null;
    
    $stmt = $pdo->prepare("INSERT INTO ApiKeys (user_id, api_key, api_secret, description, permissions, expires_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['admin_id'],
        $apiKey,
        $apiSecret,
        $description,
        $permissions,
        $expiry
    ]);
    
    // Log activity
    $activity = $pdo->prepare("INSERT INTO AdminActivityLog (admin_id, admin_name, activity_type, activity_details) VALUES (?, ?, ?, ?)");
    $activity->execute([
        $_SESSION['admin_id'],
        $_SESSION['admin_name'],
        'API Keys',
        'Generated new API key: ' . substr($description, 0, 50)
    ]);
    
    $_SESSION['new_api_key'] = $apiKey;
    header("Location: api-keys.php");
    exit();
}

// Revoke API key
if (isset($_POST['revoke_key'])) {
    $keyId = (int)$_POST['key_id'];
    
    $stmt = $pdo->prepare("UPDATE ApiKeys SET is_active = FALSE WHERE key_id = ? AND user_id = ?");
    $stmt->execute([$keyId, $_SESSION['admin_id']]);
    
    // Log activity
    $activity = $pdo->prepare("INSERT INTO AdminActivityLog (admin_id, admin_name, activity_type, activity_details) VALUES (?, ?, ?, ?)");
    $activity->execute([
        $_SESSION['admin_id'],
        $_SESSION['admin_name'],
        'API Keys',
        'Revoked API key ID: ' . $keyId
    ]);
    
    header("Location: api-keys.php");
    exit();
}

// Get API keys
$stmt = $pdo->prepare("SELECT * FROM ApiKeys WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['admin_id']]);
$apiKeys = $stmt->fetchAll();

include '../../../includes/header.php';
?>

<div class="container-fluid">
    <!-- API Keys List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">API Keys Management</h6>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#generateKeyModal">
                <i class="fas fa-key"></i> Generate New Key
            </button>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['new_api_key'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <h4 class="alert-heading">API Key Generated!</h4>
                <p>Your new API key is: <code><?= $_SESSION['new_api_key'] ?></code></p>
                <p class="mb-0">Make sure to copy it now as it won't be shown again.</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['new_api_key']); endif; ?>
            
            <div class="table-responsive">
                <table class="table table-bordered" id="apiKeysTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Key ID</th>
                            <th>Description</th>
                            <th>Permissions</th>
                            <th>Created</th>
                            <th>Last Used</th>
                            <th>Expires</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($apiKeys as $key): ?>
                        <tr>
                            <td><?= $key['key_id'] ?></td>
                            <td><?= htmlspecialchars($key['description']) ?></td>
                            <td>
                                <?php 
                                $perms = json_decode($key['permissions'], true);
                                foreach ($perms as $perm): 
                                ?>
                                <span class="badge bg-info me-1"><?= $perm ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td><?= formatDate($key['created_at']) ?></td>
                            <td><?= $key['last_used'] ? formatDate($key['last_used']) : 'Never' ?></td>
                            <td><?= $key['expires_at'] ? formatDate($key['expires_at']) : 'Never' ?></td>
                            <td>
                                <span class="badge bg-<?= $key['is_active'] ? 'success' : 'danger' ?>">
                                    <?= $key['is_active'] ? 'Active' : 'Revoked' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($key['is_active']): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="key_id" value="<?= $key['key_id'] ?>">
                                    <button type="submit" name="revoke_key" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Are you sure you want to revoke this API key?')">
                                        <i class="fas fa-ban"></i> Revoke
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- API Documentation -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">API Documentation</h6>
        </div>
        <div class="card-body">
            <div class="accordion" id="apiDocsAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                            Authentication
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body">
                            <p>All API requests must include the following headers:</p>
                            <pre><code>X-API-Key: your_api_key_here
X-API-Timestamp: current_unix_timestamp
X-API-Signature: signature_here</code></pre>
                            <p>The signature is generated as follows:</p>
                            <pre><code>signature = HMAC-SHA256(api_secret, request_method + ":" + request_path + ":" + timestamp)</code></pre>
                        </div>
                    </div>
                </div>
                <!-- More API documentation sections... -->
            </div>
        </div>
    </div>
</div>

<!-- Generate Key Modal -->
<div class="modal fade" id="generateKeyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate New API Key</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" name="description" required placeholder="What's this key for?">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="read" id="permRead" checked>
                                    <label class="form-check-label" for="permRead">Read Access</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="write" id="permWrite">
                                    <label class="form-check-label" for="permWrite">Write Access</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="delete" id="permDelete">
                                    <label class="form-check-label" for="permDelete">Delete Access</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="users" id="permUsers">
                                    <label class="form-check-label" for="permUsers">User Management</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="content" id="permContent">
                                    <label class="form-check-label" for="permContent">Content Management</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="analytics" id="permAnalytics">
                                    <label class="form-check-label" for="permAnalytics">Analytics Access</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Expiration (optional)</label>
                        <input type="datetime-local" class="form-control" name="expiry">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="generate_key" class="btn btn-primary">Generate Key</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#apiKeysTable').DataTable({
        order: [[0, 'desc']],
        responsive: true
    });
});
</script>

<?php include '../../../includes/footer.php'; ?>