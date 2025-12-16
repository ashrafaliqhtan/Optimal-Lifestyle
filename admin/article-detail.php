<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header('Location: articles-view.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT a.*, u.name as author_name, c.name as category_name 
    FROM Articles a
    LEFT JOIN usersmanage u ON a.author_id = u.id
    LEFT JOIN categories c ON a.category_id = c.category_id
    WHERE a.article_id = ?
");
$stmt->execute([$_GET['id']]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header('Location: articles-view.php');
    exit;
}

$page_title = $article['title'];
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= htmlspecialchars($article['title']) ?></h1>
        <a href="articles-view.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to All Articles
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Article Details</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item" href="content-edit.php?id=<?= $article['article_id'] ?>">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Category:</strong> <?= htmlspecialchars($article['category_name'] ?? 'Uncategorized') ?></p>
                    <p><strong>Author:</strong> <?= htmlspecialchars($article['author_name'] ?? 'Unknown') ?></p>
                </div>
                <div class="col-md-6 text-md-right">
                    <p><strong>Status:</strong> 
                        <span class="badge <?= getStatusBadgeClass($article['status']) ?>">
                            <?= ucfirst($article['status']) ?>
                        </span>
                    </p>
                    <p><strong>Created:</strong> <?= date('M j, Y H:i', strtotime($article['created_at'])) ?></p>
                </div>
            </div>
            
            <?php if ($article['image_url']): ?>
            <div class="text-center mb-4">
                <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="Featured Image" class="img-fluid rounded">
            </div>
            <?php endif; ?>
            
            <div class="article-content">
                <?= $article['content'] ?>
            </div>
        </div>
    </div>
</div>

<style>
.article-content {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    font-size: 1rem;
    line-height: 1.6;
    color: #212529;
}

.article-content h1,
.article-content h2,
.article-content h3,
.article-content h4 {
    margin-top: 1.5em;
    margin-bottom: 0.75em;
    font-weight: 600;
}

.article-content h1 { font-size: 2rem; }
.article-content h2 { font-size: 1.75rem; }
.article-content h3 { font-size: 1.5rem; }
.article-content h4 { font-size: 1.25rem; }

.article-content p {
    margin-bottom: 1em;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
}

.article-content table {
    width: 100%;
    margin-bottom: 1rem;
    border-collapse: collapse;
}

.article-content table td,
.article-content table th {
    padding: 0.75rem;
    border: 1px solid #dee2e6;
}

.article-content table th {
    background-color: #f8f9fa;
    font-weight: bold;
}

.article-content blockquote {
    padding: 0.5em 1em;
    margin: 0 0 1em;
    font-size: 1.1em;
    border-left: 4px solid #ddd;
    color: #777;
}

.article-content ul,
.article-content ol {
    margin-bottom: 1em;
    padding-left: 2em;
}

.article-content ul { list-style-type: disc; }
.article-content ol { list-style-type: decimal; }

.article-content a {
    color: #007bff;
    text-decoration: none;
}

.article-content a:hover {
    text-decoration: underline;
}

.article-content .image {
    text-align: center;
    margin: 1em 0;
}

.article-content .image img {
    max-width: 100%;
    height: auto;
}

.article-content .image-style-side {
    float: right;
    margin-left: 1.5em;
    max-width: 50%;
}

.article-content .table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
}

.article-content .table th,
.article-content .table td {
    padding: 0.75rem;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}

.article-content .table thead th {
    vertical-align: bottom;
    border-bottom: 2px solid #dee2e6;
}

.article-content .table tbody + tbody {
    border-top: 2px solid #dee2e6;
}
</style>

<?php include 'includes/footer.php'; ?>