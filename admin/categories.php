<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

// Initialize variables
$page_title = "Categories Management";
$errors = [];
$success = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle delete action
    if (isset($_POST['delete'])) {
        $category_id = (int)$_POST['category_id'];
        
        try {
            // Check if category is used in any articles
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Articles WHERE category_id = ?");
            $stmt->execute([$category_id]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                $errors[] = "Cannot delete category because it's being used in existing articles";
            } else {
                $stmt = $pdo->prepare("DELETE FROM categories WHERE category_id = ?");
                $stmt->execute([$category_id]);
                $success = true;
                $_SESSION['success_message'] = "Category deleted successfully";
                header("Location: categories.php");
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    // Handle add/edit action
    else {
        $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
        $name = trim($_POST['name']);
        $slug = trim($_POST['slug']);
        $description = trim($_POST['description']);
        
        // Validation
        if (empty($name)) {
            $errors[] = "Category name is required";
        }
        if (empty($slug)) {
            $errors[] = "Slug is required";
        }
        if (strlen($name) > 100) {
            $errors[] = "Category name must be less than 100 characters";
        }
        
        if (empty($errors)) {
            try {
                $pdo->beginTransaction();
                
                if ($category_id > 0) {
                    // Update existing category
                    $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ? WHERE category_id = ?");
                    $stmt->execute([$name, $slug, $description, $category_id]);
                    $message = "Category updated successfully";
                } else {
                    // Insert new category
                    $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, created_at) VALUES (?, ?, ?, NOW())");
                    $stmt->execute([$name, $slug, $description]);
                    $message = "Category added successfully";
                }
                
                $pdo->commit();
                $success = true;
                $_SESSION['success_message'] = $message;
                header("Location: categories.php");
                exit();
            } catch (PDOException $e) {
                $pdo->rollBack();
                if ($e->getCode() == 23000) {
                    $errors[] = "Slug already exists, please choose a different one";
                } else {
                    $errors[] = "Database error: " . $e->getMessage();
                }
            }
        }
    }
}

// Get all categories
try {
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Failed to load categories: " . $e->getMessage());
}

// Get category for editing
$edit_category = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $category_id = (int)$_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id = ?");
        $stmt->execute([$category_id]);
        $edit_category = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $errors[] = "Failed to load category: " . $e->getMessage();
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= htmlspecialchars($page_title) ?></h1>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h5 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Errors Found</h5>
        <ul class="mb-0 pl-3">
            <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h5 class="alert-heading"><i class="fas fa-check-circle"></i> Success!</h5>
        <p class="mb-0"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
    </div>
    <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <?= $edit_category ? 'Edit Category' : 'Add New Category' ?>
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php if ($edit_category): ?>
                        <input type="hidden" name="category_id" value="<?= $edit_category['category_id'] ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="name">Category Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   value="<?= htmlspecialchars($edit_category['name'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="slug">Slug *</label>
                            <input type="text" class="form-control" id="slug" name="slug" required
                                   value="<?= htmlspecialchars($edit_category['slug'] ?? '') ?>">
                            <small class="form-text text-muted">Used in URLs (must be unique)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= 
                                htmlspecialchars($edit_category['description'] ?? '') ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            <?= $edit_category ? 'Update' : 'Save' ?>
                        </button>
                        
                        <?php if ($edit_category): ?>
                        <a href="categories.php" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Categories List</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" 
                             aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#helpModal">
                                <i class="fas fa-question-circle mr-2"></i>Help
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Category Name</th>
                                    <th>Slug</th>
                                    <th>Articles Count</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">No categories found</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?= htmlspecialchars($category['name']) ?></td>
                                    <td><?= htmlspecialchars($category['slug']) ?></td>
                                    <td>
                                        <?php 
                                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Articles WHERE category_id = ?");
                                        $stmt->execute([$category['category_id']]);
                                        echo $stmt->fetchColumn();
                                        ?>
                                    </td>
                                    <td><?= date('Y/m/d', strtotime($category['created_at'])) ?></td>
                                    <td>
                                        <div class="dropdown no-arrow">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                                                    id="dropdownMenuButton-<?= $category['category_id'] ?>" 
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-cog"></i> Actions
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" 
                                                 aria-labelledby="dropdownMenuButton-<?= $category['category_id'] ?>">
                                                <a class="dropdown-item" href="categories.php?edit=<?= $category['category_id'] ?>">
                                                    <i class="fas fa-edit mr-2"></i>Edit
                                                </a>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="category_id" value="<?= $category['category_id'] ?>">
                                                    <button type="submit" name="delete" class="dropdown-item text-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this category?')">
                                                        <i class="fas fa-trash-alt mr-2"></i>Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Help Modal -->
<div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="helpModalLabel"><i class="fas fa-question-circle mr-2"></i>Help</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6>Categories Management</h6>
                <p>This page allows you to add, edit, and delete content categories on your website.</p>
                
                <h6>Adding a New Category</h6>
                <p>Use the form on the left to add a new category. Category name and slug are required fields.</p>
                
                <h6>Editing Categories</h6>
                <p>Click the actions button next to any category and select "Edit" to modify its details.</p>
                
                <h6>Deleting Categories</h6>
                <p>You can only delete categories that are not in use. Categories containing articles cannot be deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Got it!</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- DataTables Script -->
<link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#dataTable').DataTable({
        "columnDefs": [
            { "orderable": false, "targets": [4] } // Actions column not sortable
        ]
    });
    
    // Auto-generate slug from name
    $('#name').on('blur', function() {
        if (!$('#slug').val()) {
            const name = $(this).val();
            const slug = name.toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove non-word chars
                .replace(/\s+/g, '-')     // Replace spaces with -
                .replace(/--+/g, '-')      // Replace multiple - with single -
                .replace(/^-+|-+$/g, '');  // Trim - from start/end
            $('#slug').val(slug);
        }
    });
});
</script>