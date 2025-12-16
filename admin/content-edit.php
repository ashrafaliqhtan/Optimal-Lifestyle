<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header("Location: content.php");
    exit;
}

$article_id = (int)$_GET['id'];
$errors = [];
$success = false;

// Get article data
$stmt = $pdo->prepare("SELECT * FROM Articles WHERE article_id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header("Location: content.php");
    exit;
}

// Get categories for dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = $_POST['category_id'] ? (int)$_POST['category_id'] : null;
    $status = $_POST['status'];
    $image_url = $_POST['image_url'];

    // Validation
    if (empty($title)) $errors[] = "Title is required";
    if (empty($content)) $errors[] = "Content is required";
    if (!in_array($status, ['draft', 'published', 'archived'])) $errors[] = "Invalid status";

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE Articles 
            SET title = ?, content = ?, image_url = ?, category_id = ?, status = ?, updated_at = NOW()
            WHERE article_id = ?
        ");
        
        if ($stmt->execute([$title, $content, $image_url, $category_id, $status, $article_id])) {
            $success = true;
            // Refresh article data
            $stmt = $pdo->prepare("SELECT * FROM Articles WHERE article_id = ?");
            $stmt->execute([$article_id]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $errors[] = "Failed to update content. Please try again.";
        }
    }
}

$page_title = "Edit Content";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Content</h1>
        <div>
            <a href="../article.php?id=<?= $article['article_id'] ?>" target="_blank" class="btn btn-sm btn-info">
                <i class="fas fa-eye fa-sm text-white-50"></i> View
            </a>
            <a href="content.php" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Content
            </a>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
    <div class="alert alert-success">
        Content updated successfully!
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Content Information</h6>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="<?= htmlspecialchars($article['title']) ?>" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control" id="category_id" name="category_id">
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= $article['category_id'] == $category['id'] ? 'selected' : '' ?>>
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
                                <option value="draft" <?= $article['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="published" <?= $article['status'] == 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="archived" <?= $article['status'] == 'archived' ? 'selected' : '' ?>>Archived</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="image_url">Featured Image URL</label>
                    <input type="text" class="form-control" id="image_url" name="image_url" 
                           value="<?= htmlspecialchars($article['image_url']) ?>">
                    <small class="text-muted">Leave blank for no image</small>
                </div>
                
                <div class="form-group">
                    <label for="content">Content *</label>
                    <textarea class="form-control" id="content" name="content" rows="10" required><?= htmlspecialchars($article['content']) ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Content</button>
                <a href="content.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<!-- Include CKEditor for rich text editing -->
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('content');
</script>

<?php include 'includes/footer.php'; ?>