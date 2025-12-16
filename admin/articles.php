<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total number of articles
$total_stmt = $pdo->query("SELECT COUNT(*) FROM Articles");
$total_articles = $total_stmt->fetchColumn();
$total_pages = ceil($total_articles / $limit);

// Get articles with pagination
$stmt = $pdo->prepare("
    SELECT a.*, u.name as author_name 
    FROM Articles a 
    LEFT JOIN usersmanage u ON a.author_id = u.id 
    ORDER BY a.created_at DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();

// Search functionality
if (isset($_GET['search'])) {
    $search = '%' . $_GET['search'] . '%';
    $stmt = $pdo->prepare("
        SELECT a.*, u.name as author_name 
        FROM Articles a 
        LEFT JOIN usersmanage u ON a.author_id = u.id 
        WHERE a.title LIKE :search OR a.content LIKE :search 
        ORDER BY a.created_at DESC
    ");
    $stmt->bindParam(':search', $search);
    $stmt->execute();
    $articles = $stmt->fetchAll();
}

include 'includes/header.php';
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Article Management</h6>
        <div>
            <a href="articles-add.php" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle fa-sm"></i> Add New Article
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Search Form -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search articles..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
                <?php if (isset($_GET['search'])): ?>
                    <a href="articles.php" class="btn btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-bordered" id="articlesTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($articles as $article): ?>
                    <tr>
                        <td><?= $article['article_id'] ?></td>
                        <td><?= htmlspecialchars($article['title']) ?></td>
                        <td><?= $article['author_name'] ? htmlspecialchars($article['author_name']) : 'System' ?></td>
                        <td><?= date('M j, Y', strtotime($article['created_at'])) ?></td>
                        <td><?= date('M j, Y', strtotime($article['updated_at'])) ?></td>
                        <td>
                            <span class="badge bg-success">Published</span>
                        </td>
                        <td>
                            <a href="articles-view.php?id=<?= $article['article_id'] ?>" target="_blank" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="articles-edit.php?id=<?= $article['article_id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-article" data-id="<?= $article['article_id'] ?>" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if (!isset($_GET['search'])): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>" tabindex="-1">Previous</a>
                    </li>
                    
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteArticleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this article? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmArticleDelete" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#articlesTable').DataTable({
        paging: false,
        searching: false,
        info: false
    });
    
    // Delete article confirmation
    $('.delete-article').click(function() {
        const articleId = $(this).data('id');
        $('#confirmArticleDelete').attr('href', 'articles-delete.php?id=' + articleId);
        $('#deleteArticleModal').modal('show');
    });
});
</script>

<?php include 'includes/footer.php'; ?>