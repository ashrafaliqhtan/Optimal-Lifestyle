<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

// Check if article ID is provided
if (!isset($_GET['id'])) {
    header("Location: content.php");
    exit();
}

$article_id = (int)$_GET['id'];

// Get the article details
$stmt = $pdo->prepare("
    SELECT a.*, u.name as author_name, c.name as category_name 
    FROM Articles a
    LEFT JOIN usersmanage u ON a.author_id = u.id
    LEFT JOIN categories c ON a.category_id = c.category_id
    WHERE a.article_id = ?
");
$stmt->execute([$article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header("Location: content.php");
    exit();
}

// Function to get status badge class
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'published':
            return 'bg-success';
        case 'draft':
            return 'bg-warning text-dark';
        case 'archived':
            return 'bg-secondary';
        default:
            return 'bg-info';
    }
}

$page_title = htmlspecialchars($article['title']);
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= htmlspecialchars($article['title']) ?></h1>
        <div>
            <a href="content-edit.php?id=<?= $article['article_id'] ?>" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit
            </a>
            <a href="content.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-list fa-sm text-white-50"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Article Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Article Content</h6>
                    <span class="badge <?= getStatusBadgeClass($article['status']) ?>">
                        <?= ucfirst($article['status']) ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if ($article['image_url']): ?>
                    <div class="text-center mb-4">
                        <img src="<?= htmlspecialchars($article['image_url']) ?>" class="img-fluid rounded" alt="Featured image">
                    </div>
                    <?php endif; ?>
                    
                    <!-- Display the formatted content -->
                    <div class="article-content">
                        <?= $article['content'] ?>
                    </div>
                    
                    <hr>
                    
                    <div class="text-muted small mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Author:</strong> <?= htmlspecialchars($article['author_name'] ?? 'No Author') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Category:</strong> <?= htmlspecialchars($article['category_name'] ?? 'Uncategorized') ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Created:</strong> <?= date('M j, Y \a\t g:i a', strtotime($article['created_at'])) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Last Updated:</strong> <?= date('M j, Y \a\t g:i a', strtotime($article['updated_at'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Article Details Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Article Details</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge <?= getStatusBadgeClass($article['status']) ?>">
                                        <?= ucfirst($article['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Author</th>
                                <td><?= htmlspecialchars($article['author_name'] ?? 'No Author') ?></td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td><?= htmlspecialchars($article['category_name'] ?? 'Uncategorized') ?></td>
                            </tr>
                            <tr>
                                <th>Created</th>
                                <td><?= date('M j, Y \a\t g:i a', strtotime($article['created_at'])) ?></td>
                            </tr>
                            <tr>
                                <th>Last Updated</th>
                                <td><?= date('M j, Y \a\t g:i a', strtotime($article['updated_at'])) ?></td>
                            </tr>
                            <tr>
                                <th>Views</th>
                                <td><?= number_format($article['views'] ?? 0) ?></td>
                            </tr>
                            <?php if ($article['image_url']): ?>
                            <tr>
                                <th>Featured Image</th>
                                <td>
                                    <a href="<?= htmlspecialchars($article['image_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        View Image
                                    </a>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="content-edit.php?id=<?= $article['article_id'] ?>" class="btn btn-warning btn-icon-split">
                            <span class="icon text-white-50">
                                <i class="fas fa-edit"></i>
                            </span>
                            <span class="text">Edit Article</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <a href="content-edit.php?id=<?= $article['article_id'] ?>" class="btn btn-warning btn-icon-split mb-3">
                            <span class="icon text-white-50">
                                <i class="fas fa-edit"></i>
                            </span>
                            <span class="text">Edit Article</span>
                        </a>
                        
                        <a href="content-delete.php?id=<?= $article['article_id'] ?>" class="btn btn-danger btn-icon-split mb-3" onclick="return confirm('Are you sure you want to delete this article?')">
                            <span class="icon text-white-50">
                                <i class="fas fa-trash"></i>
                            </span>
                            <span class="text">Delete Article</span>
                        </a>
                        
                        <?php if ($article['status'] !== 'published'): ?>
                        <a href="content-publish.php?id=<?= $article['article_id'] ?>" class="btn btn-success btn-icon-split mb-3">
                            <span class="icon text-white-50">
                                <i class="fas fa-check"></i>
                            </span>
                            <span class="text">Publish Now</span>
                        </a>
                        <?php endif; ?>
                        
                        <a href="content.php" class="btn btn-secondary btn-icon-split">
                            <span class="icon text-white-50">
                                <i class="fas fa-list"></i>
                            </span>
                            <span class="text">Back to List</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Style the article content to match CKEditor's output */
    .article-content {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
    }
    
    .article-content h1,
    .article-content h2,
    .article-content h3,
    .article-content h4,
    .article-content h5,
    .article-content h6 {
        margin-top: 1.5em;
        margin-bottom: 0.5em;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .article-content h1 { font-size: 2.2em; }
    .article-content h2 { font-size: 1.8em; }
    .article-content h3 { font-size: 1.5em; }
    .article-content h4 { font-size: 1.3em; }
    .article-content h5 { font-size: 1.1em; }
    .article-content h6 { font-size: 1em; }
    
    .article-content p {
        margin-bottom: 1.2em;
    }
    
    .article-content ul,
    .article-content ol {
        margin-bottom: 1.2em;
        padding-left: 2em;
    }
    
    .article-content blockquote {
        border-left: 4px solid #ddd;
        padding-left: 1em;
        margin-left: 0;
        color: #777;
        font-style: italic;
    }
    
    .article-content table {
        width: 100%;
        margin-bottom: 1.2em;
        border-collapse: collapse;
    }
    
    .article-content table th,
    .article-content table td {
        padding: 8px 12px;
        border: 1px solid #ddd;
    }
    
    .article-content table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    
    .article-content img {
        max-width: 100%;
        height: auto;
        margin: 1em 0;
    }
    
    .article-content a {
        color: #3498db;
        text-decoration: none;
    }
    
    .article-content a:hover {
        text-decoration: underline;
    }
</style>

<?php include 'includes/footer.php'; ?>