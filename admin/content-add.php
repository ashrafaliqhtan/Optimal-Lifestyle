<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

// Initialize variables
$errors = [];
$success = false;
$title = $content = $image_url = '';
$category_id = null;
$status = 'draft'; // Default status

// Get categories for dropdown
try {
    $categories = $pdo->query("SELECT * FROM categories ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Failed to load categories: " . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));
    $content = trim($_POST['content']); // Will be sanitized by CKEditor
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $status = in_array($_POST['status'], ['draft', 'published', 'archived']) ? $_POST['status'] : 'draft';
    $image_url = trim(filter_input(INPUT_POST, 'image_url', FILTER_SANITIZE_URL));
    $author_id = $_SESSION['user_id'];

    // Validation
    if (empty($title)) $errors[] = "Title is required";
    if (empty($content)) $errors[] = "Content is required";
    if (strlen($title) > 255) $errors[] = "Title must be less than 255 characters";
    if ($image_url && !filter_var($image_url, FILTER_VALIDATE_URL)) $errors[] = "Invalid image URL";

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("
                INSERT INTO Articles 
                (title, content, image_url, author_id, category_id, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            if ($stmt->execute([$title, $content, $image_url, $author_id, $category_id, $status])) {
                $pdo->commit();
                $success = true;
                // Reset form fields
                $title = $content = $image_url = '';
                $category_id = null;
                $status = 'draft';
            } else {
                $pdo->rollBack();
                $errors[] = "Failed to create content. Please try again.";
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

$page_title = "Add New Content";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= htmlspecialchars($page_title) ?></h1>
        <a href="content.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-list fa-sm text-white-50"></i> View All Content
        </a>
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
    
    <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h5 class="alert-heading"><i class="fas fa-check-circle"></i> Success!</h5>
        <p class="mb-0">Content created successfully. <a href="content.php" class="alert-link">View all content</a> or <a href="add-content.php" class="alert-link">create another</a>.</p>
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Content Details</h6>
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
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="content.php">
                        <i class="fas fa-list mr-2"></i>View All Content
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" id="contentForm">
                <div class="form-group">
                    <label for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="<?= htmlspecialchars($title) ?>" required
                           placeholder="Enter a descriptive title">
                    <small class="form-text text-muted">Maximum 255 characters</small>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control" id="category_id" name="category_id">
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= ($_POST['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="draft" <?= ($_POST['status'] ?? '') == 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="published" <?= ($_POST['status'] ?? '') == 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="archived" <?= ($_POST['status'] ?? '') == 'archived' ? 'selected' : '' ?>>Archived</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="image_url">Featured Image URL</label>
                    <div class="input-group">
                        <input type="url" class="form-control" id="image_url" name="image_url" 
                               value="<?= htmlspecialchars($image_url) ?>"
                               placeholder="https://example.com/image.jpg">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="previewImageBtn">
                                <i class="fas fa-eye"></i> Preview
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">Leave blank for no image</small>
                    <div id="imagePreview" class="mt-2" style="display:none;">
                        <img src="" alt="Image preview" class="img-thumbnail" style="max-height: 150px;">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="content">Content <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="content" name="content" rows="10" required><?= htmlspecialchars($content) ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save mr-2"></i>Create Content
                    </button>
                    <a href="content.php" class="btn btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="button" class="btn btn-outline-info float-right" id="previewContentBtn">
                        <i class="fas fa-eye mr-2"></i>Preview Content
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Content Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                <h6>Creating Content</h6>
                <p>Fill out all required fields (marked with *) to create new content. Use the rich text editor to format your content.</p>
                
                <h6>Status Options</h6>
                <ul>
                    <li><strong>Draft:</strong> Content is saved but not visible to the public</li>
                    <li><strong>Published:</strong> Content is live and visible to visitors</li>
                    <li><strong>Archived:</strong> Content is preserved but not publicly visible</li>
                </ul>
                
                <h6>Image Tips</h6>
                <p>For best results, use high-quality images with a 16:9 aspect ratio. Recommended size: 1200x675 pixels.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Got it!</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Include CKEditor for rich text editing -->
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
<!-- Select2 for enhanced dropdowns -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Initialize CKEditor
    CKEDITOR.replace('content', {
        toolbar: [
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Blockquote'] },
            { name: 'links', items: ['Link', 'Unlink'] },
            { name: 'insert', items: ['Image', 'Table', 'HorizontalRule'] },
            { name: 'styles', items: ['Styles', 'Format'] },
            { name: 'document', items: ['Source'] }
        ],
        height: 300,
        removePlugins: 'elementspath',
        resize_enabled: false
    });

    // Initialize Select2
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: '-- Select Category --',
            allowClear: true
        });

        // Image preview functionality
        $('#previewImageBtn').click(function() {
            const imageUrl = $('#image_url').val();
            if (imageUrl) {
                $('#imagePreview img').attr('src', imageUrl);
                $('#imagePreview').show();
            } else {
                $('#imagePreview').hide();
            }
        });

        // Content preview functionality
        $('#previewContentBtn').click(function() {
            const title = $('#title').val() || 'No title';
            const content = CKEDITOR.instances.content.getData() || '<p>No content yet</p>';
            
            $('#previewModal .modal-title').text(title);
            $('#previewContent').html(`
                <h2>${title}</h2>
                <hr>
                ${content}
            `);
            
            $('#previewModal').modal('show');
        });

        // Form submission handling
        $('#contentForm').on('submit', function() {
            $('#submitBtn').prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Saving...');
        });
    });
</script>