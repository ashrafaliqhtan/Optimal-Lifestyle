<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

// Initialize variables with default values
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Simplified query to get all articles without filtering
$query = "SELECT a.*, u.name as author_name, c.name as category_name 
          FROM Articles a
          LEFT JOIN usersmanage u ON a.author_id = u.id
          LEFT JOIN categories c ON a.category_id = c.category_id
          ORDER BY a.created_at DESC LIMIT ? OFFSET ?";

// Prepare and execute statement
$stmt = $pdo->prepare($query);
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$countQuery = "SELECT COUNT(*) as total FROM Articles";
$totalStmt = $pdo->query($countQuery);
$totalArticles = $totalStmt->fetchColumn();
$totalPages = ceil($totalArticles / $limit);

// Get categories and authors for display (not for filtering)
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$authors = $pdo->query("SELECT id, name FROM usersmanage ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

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

$page_title = "Content Management";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Content Management</h1>
        <a href="content-add.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Content
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Content</h6>
        </div>
        <div class="card-body">
            <!-- Content Table -->
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($articles)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No articles found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($articles as $article): ?>
                            <tr>
                                <td><?= htmlspecialchars($article['title']) ?></td>
                                <td><?= htmlspecialchars($article['category_name'] ?? 'Uncategorized') ?></td>
                                <td><?= htmlspecialchars($article['author_name'] ?? 'No Author') ?></td>
                                <td>
                                    <span class="badge <?= getStatusBadgeClass($article['status']) ?>">
                                        <?= ucfirst($article['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M j, Y', strtotime($article['created_at'])) ?></td>
                                <td>
                                    <a href="articles-view.php?id=<?= $article['article_id'] ?>" target="_blank" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="content-edit.php?id=<?= $article['article_id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="content-delete.php?id=<?= $article['article_id'] ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this article?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>