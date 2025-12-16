<?php
require_once 'config/database.php';

// Get article ID from URL
$article_id = $_GET['id'] ?? 0;
$article_id = (int)$article_id;

if ($article_id <= 0) {
    header("Location: index.php");
    exit();
}

// Get article data
$stmt = $pdo->prepare("SELECT a.*, u.name as author_name, c.name as category_name 
                      FROM Articles a
                      LEFT JOIN usersmanage u ON a.author_id = u.id
                      LEFT JOIN categories c ON a.category_id = c.category_id
                      WHERE a.article_id = ? AND a.status = 'published'");
$stmt->execute([$article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header("Location: index.php");
    exit();
}

// Set page title
$page_title = htmlspecialchars($article['title']);

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Article Header -->
            <article>
                <header class="mb-4">
                    <h1 class="fw-bolder mb-1"><?= htmlspecialchars($article['title']) ?></h1>
                    
                    <div class="text-muted fst-italic mb-2">
                        Posted on <?= date('F j, Y', strtotime($article['created_at'])) ?>
                        <?php if (!empty($article['author_name'])): ?>
                            by <?= htmlspecialchars($article['author_name']) ?>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($article['category_name'])): ?>
                        <a class="badge bg-secondary text-decoration-none link-light" href="#!">
                            <?= htmlspecialchars($article['category_name']) ?>
                        </a>
                    <?php endif; ?>
                </header>
                
                <!-- Featured Image -->
                <?php if (!empty($article['image_url'])): ?>
                    <figure class="mb-4">
                        <img class="img-fluid rounded" src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" />
                    </figure>
                <?php endif; ?>
                
                <!-- Article Content -->
                <section class="mb-5">
                    <?= $article['content'] ?>
                </section>
            </article>
        </div>
        
        <!-- Sidebar Widgets -->
        <div class="col-lg-4">
            <!-- Categories Widget -->
            <div class="card mb-4">
                <div class="card-header">Categories</div>
                <div class="card-body">
                    <div class="row">
                        <?php
                        $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
                        $chunkedCategories = array_chunk($categories, ceil(count($categories)/2));
                        foreach ($chunkedCategories as $categoryChunk): ?>
                            <div class="col-sm-6">
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($categoryChunk as $category): ?>
                                        <li><a href="category.php?id=<?= $category['category_id'] ?>"><?= htmlspecialchars($category['name']) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Recent Articles Widget -->
            <div class="card mb-4">
                <div class="card-header">Recent Articles</div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <?php
                        $recentArticles = $pdo->query("SELECT article_id, title FROM Articles 
                                                      WHERE status = 'published' 
                                                      ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($recentArticles as $recent): ?>
                            <li>
                                <a href="article.php?id=<?= $recent['article_id'] ?>"><?= htmlspecialchars($recent['title']) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>