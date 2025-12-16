<?php
require_once 'includes/auth-check.php';
//require_once 'includes/superadmin-check.php';
require_once 'config/database.php';

// Get all roles
$roles = $pdo->query("SELECT * FROM user_roles ORDER BY role_name")->fetchAll(PDO::FETCH_ASSOC);

// Get all permissions
$permissions = $pdo->query("SELECT * FROM permissions ORDER BY permission_name")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_role'])) {
        $role_name = trim($_POST['role_name']);
        $description = trim($_POST['description']);
        
        if (!empty($role_name)) {
            $stmt = $pdo->prepare("INSERT INTO user_roles (role_name, description) VALUES (?, ?)");
            $stmt->execute([$role_name, $description]);
            header("Location: user-roles.php");
            exit;
        }
    } elseif (isset($_POST['update_permissions'])) {
        $role_id = (int)$_POST['role_id'];
        $selected_permissions = $_POST['permissions'] ?? [];
        
        // Delete existing permissions for this role
        $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?")->execute([$role_id]);
        
        // Add new permissions
        $stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
        foreach ($selected_permissions as $perm_id) {
            $stmt->execute([$role_id, (int)$perm_id]);
        }
        
        header("Location: user-roles.php");
        exit;
    } elseif (isset($_POST['delete_role'])) {
        $role_id = (int)$_POST['role_id'];
        
        // First check if any users have this role
        $user_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = $role_id")->fetchColumn();
        
        if ($user_count == 0) {
            // Delete role permissions first
            $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?")->execute([$role_id]);
            // Then delete the role
            $pdo->prepare("DELETE FROM user_roles WHERE id = ?")->execute([$role_id]);
            header("Location: user-roles.php");
            exit;
        } else {
            $error = "Cannot delete role assigned to users";
        }
    }
}

$page_title = "Role Management";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Role Management</h1>
        <a href="users.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Users
        </a>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Roles</h6>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                        <i class="fas fa-plus"></i> Add Role
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Role Name</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roles as $role): ?>
                                <tr>
                                    <td><?= htmlspecialchars($role['role_name']) ?></td>
                                    <td><?= htmlspecialchars($role['description']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                                data-bs-target="#editPermissionsModal" 
                                                data-role-id="<?= $role['id'] ?>"
                                                data-role-name="<?= htmlspecialchars($role['role_name']) ?>">
                                            <i class="fas fa-key"></i> Permissions
                                        </button>
                                        <?php if ($role['is_system'] == 0): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="role_id" value="<?= $role['id'] ?>">
                                            <button type="submit" name="delete_role" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this role?')">
                                                <i class="fas fa-trash"></i>
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
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">All Permissions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($permissions as $permission): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-primary h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                <?= htmlspecialchars($permission['permission_name']) ?>
                                            </div>
                                            <div class="text-muted small">
                                                <?= htmlspecialchars($permission['description']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="role_name">Role Name *</label>
                        <input type="text" class="form-control" id="role_name" name="role_name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_role" class="btn btn-primary">Add Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Permissions Modal -->
<div class="modal fade" id="editPermissionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Permissions for <span id="modalRoleName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" id="modalRoleId" name="role_id">
                <div class="modal-body">
                    <div class="row">
                        <?php foreach ($permissions as $permission): ?>
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                       name="permissions[]" value="<?= $permission['id'] ?>" 
                                       id="perm-<?= $permission['id'] ?>">
                                <label class="form-check-label" for="perm-<?= $permission['id'] ?>">
                                    <?= htmlspecialchars($permission['permission_name']) ?>
                                </label>
                                <small class="d-block text-muted">
                                    <?= htmlspecialchars($permission['description']) ?>
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_permissions" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Handle edit permissions modal
document.getElementById('editPermissionsModal').addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const roleId = button.getAttribute('data-role-id');
    const roleName = button.getAttribute('data-role-name');
    
    document.getElementById('modalRoleName').textContent = roleName;
    document.getElementById('modalRoleId').value = roleId;
    
    // Reset all checkboxes
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Fetch current permissions for this role
    fetch(`includes/get-role-permissions.php?role_id=${roleId}`)
        .then(response => response.json())
        .then(permissions => {
            permissions.forEach(permId => {
                const checkbox = document.getElementById(`perm-${permId}`);
                if (checkbox) checkbox.checked = true;
            });
        });
});
</script>

<?php include 'includes/footer.php'; ?>