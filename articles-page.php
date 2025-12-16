<?php
require_once 'admin/config/database.php';

// Initialize pagination variables
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

// Query to get only published articles
$query = "SELECT 
            a.article_id, 
            a.title, 
            a.content, 
            a.image_url, 
            a.category_id, 
            a.created_at,
            c.name as category_name
          FROM Articles a
          LEFT JOIN categories c ON a.category_id = c.category_id
          WHERE a.status = 'published'
          ORDER BY a.created_at DESC 
          LIMIT ? OFFSET ?";

$stmt = $pdo->prepare($query);
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count of published articles for pagination
$countQuery = "SELECT COUNT(*) as total FROM Articles WHERE status = 'published'";
$totalArticles = $pdo->query($countQuery)->fetchColumn();
$totalPages = ceil($totalArticles / $limit);

// Get recent articles for sidebar
$recentArticles = $pdo->query("
    SELECT 
        article_id, 
        title, 
        image_url,
        created_at
    FROM Articles 
    WHERE status = 'published'
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Articles";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Collection of helpful articles about health, nutrition, and fitness">
    <title><?= htmlspecialchars($page_title) ?> | Optimal Lifestyle</title>
    
    <!-- Favicon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <meta name="description" content="Collection of helpful articles about health, nutrition, and fitness">
    <title>Helpful Articles | Optimal Lifestyle</title>
    
    <!-- Favicon -->
    <link rel="icon" href="Styles/pictures/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
  /* الصورة تغطي عرض العنصر بالكامل، وتُقص على الارتفاع لتعبئة المساحة */
  .article-card .article-img {
    width: 100%;
    height: 200px;          /* ارتفاع ثابت، عدّله حسب التصميم */
    object-fit: cover;      /* تغطية المساحة دون تشويه */
    display: block;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
  }

  /* تنسيق شارة NEW في الزاوية العلوية اليسرى */
  .article-card .new-badge {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    background-color: #dc3545; /* لون أحمر Bootstrap “danger” */
    color: white;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    text-transform: uppercase;
  }
  /* نجعل البطاقة لها أقصى عرض محدود */
.article-card {
  max-width: 900px;    /* غيّر القيمة حسب الحاجة */
  margin: auto;        /* لجعلها متوسّطة ضمن العمود */
}

/* الصورة تُكيّف عرضها مع عرض البطاقة دون تجاوزه */
.article-card .article-img {
  width: 100%;
  height: 180px;       /* أو أي ارتفاع يناسب التصميم */
  object-fit: cover;
}
  

/* Navbar */
        .navbar {
            background-color: rgba(40, 167, 69, 0.9);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand, .nav-link {
            color: var(--text-light) !important;
            transition: color 0.3s ease;
        }
        
        .navbar-brand:hover, .nav-link:hover {
            color: #d4d4d4 !important;
        }
        
</style>    

    
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <i class="bi bi-activity me-2"></i>Optimal Lifestyle
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="BMI-page.php"><i class="bi bi-calculator me-1"></i>BMI Calculator</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="articles-page.php"><i class="bi bi-newspaper me-1"></i>Articles</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">

                    <a class="btn btn-outline-light" href="account-page.php">
                        <i class="bi bi-person-circle me-1"></i>

                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-4 flex-grow-1">
        <!-- Page Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold"><i class="bi bi-newspaper me-2"></i>Helpful Articles</h1>
            
            <!-- Quote -->
            <div class="quote-container">
                <figure class="mb-0">
                    <blockquote class="blockquote">
                        <p class="mb-0">"Wisdom is not a product of schooling but of the lifelong attempt to acquire it."</p>
                    </blockquote>
                    <figcaption class="blockquote-footer mt-2">
                        Albert Einstein
                    </figcaption>
                </figure>
            </div>
            
            <article class="lead text-muted">
                Research is fundamental to success in every field - from law and writing to finance and fitness. 
                The main purposes of research are to inform action, gather evidence for theories, and contribute 
                to developing knowledge in a field of study. While many avoid research, for those committed to 
                learning and self-improvement, conducting research is not just important—it's essential.
            </article>
        </div>

        <!-- Articles Grid -->
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-4"><i class="bi bi-bookmarks me-2"></i>Available Articles</h2>
                
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
                    <!-- Article 1 -->
                    <div class="col">
                        <div class="card article-card h-100">
                            <div class="position-relative">
                                <img src="Styles/pictures/articles/muscle-building.jpg" class="article-img" alt="Muscle Building">
                                <span class="new-badge">NEW</span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Diet Tips for Muscle Building</h5>
                                <p class="card-text">Want to build more muscle and achieve a leaner physique? Discover essential diet tips to transform your body.</p>
                                <a href="Articles/muscle-building.php" class="btn btn-primary stretched-link">
                                    <i class="bi bi-arrow-right me-1"></i>Read Article
                                </a>
                            </div>
                            <div class="card-footer text-muted">
                                <small><i class="bi bi-calendar me-1"></i>Updated 2 days ago</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Article 2 -->
                    <div class="col">
                        <div class="card article-card h-100">
                            <div class="position-relative">
                                <img src="Styles/pictures/articles/nutrition.jpg" class="article-img" alt="Healthy Nutrition">
                                <span class="new-badge">NEW</span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Essential Nutrients for Health</h5>
                                <p class="card-text">Learn about the vital nutrients your body needs for optimal health and effective weight management.</p>
                                <a href="Articles/healthy-food.php" class="btn btn-primary stretched-link">
                                    <i class="bi bi-arrow-right me-1"></i>Read Article
                                </a>
                            </div>
                            <div class="card-footer text-muted">
                                <small><i class="bi bi-calendar me-1"></i>Updated 2 days ago</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Article 3 -->
                    <div class="col">
                        <div class="card article-card h-100">
                            <div class="position-relative">
                                <img src="Styles/pictures/articles/cardio.jpg" class="article-img" alt="Cardio Workouts">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Effective Cardio Workouts</h5>
                                <p class="card-text">Maximize your cardiovascular fitness with these time-efficient and effective workout routines.</p>
                                <a href="Articles/cardio-workouts.php" class="btn btn-primary stretched-link">
                                    <i class="bi bi-arrow-right me-1"></i>Read Article
                                </a>
                            </div>
                            <div class="card-footer text-muted">
                                <small><i class="bi bi-calendar me-1"></i>Updated 1 week ago</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Article 4 -->
                    <div class="col">
                        <div class="card article-card h-100">
                            <div class="position-relative">
                                <img src="Styles/pictures/articles/sleep.jpg" class="article-img" alt="Sleep Health">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">The Science of Sleep & Recovery</h5>
                                <p class="card-text">Understand how quality sleep impacts your fitness results and overall health.</p>
                                <a href="Articles/sleep-science.php" class="btn btn-primary stretched-link">
                                    <i class="bi bi-arrow-right me-1"></i>Read Article
                                </a>
                            </div>
                            <div class="card-footer text-muted">
                                <small><i class="bi bi-calendar me-1"></i>Updated 2 weeks ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Main Content -->
    <main class="container my-4 flex-grow-1">
        <!-- Page Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold"><i class="fas fa-newspaper me-2"></i>Helpful Articles</h1>
            
            <article class="lead text-muted">
                Discover our collection of articles about health, nutrition, and fitness to help you achieve 
                your optimal lifestyle. Our content is carefully curated by experts in the field.
            </article>
        </div>

        <div class="row">
            <!-- Articles List -->
            <div class="col-lg-8">
                <div class="mb-4">
                    <h2 class="fw-bold border-bottom pb-2">Latest Articles</h2>
                </div>

                <?php if (empty($articles)): ?>
                    <div class="alert alert-info">No articles available at this time</div>
                <?php else: ?>
                    <?php foreach ($articles as $article): ?>
                    <article class="card article-card mb-4">
                        <?php if (!empty($article['image_url'])): ?>
                        <div class="article-image">
                            <img src="<?= htmlspecialchars($article['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($article['title']) ?>">
                            <?php if (strtotime($article['created_at']) > strtotime('-7 days')): ?>
                                <span class="new-badge">NEW</span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="article-meta mb-3">
                                <?php if (!empty($article['category_name'])): ?>
                                <span class="category-badge">
                                    <i class="fas fa-tag me-1"></i>
                                    <?= htmlspecialchars($article['category_name']) ?>
                                </span>
                                <?php endif; ?>
                                <span class="date-posted">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    <?= date('F j, Y', strtotime($article['created_at'])) ?>
                                </span>
                            </div>
                            <h3 class="card-title">
                                <a href="article.php?id=<?= $article['article_id'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($article['title']) ?>
                                </a>
                            </h3>
                            <div class="article-excerpt mb-3">
                                <?= substr(strip_tags($article['content']), 0, 200) ?>...
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="article.php?id=<?= $article['article_id'] ?>" class="btn btn-outline-primary">
                                    Read More <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Recent Articles -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i> Recent Articles</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php foreach ($recentArticles as $recent): ?>
                            <a href="article.php?id=<?= $recent['article_id'] ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($recent['image_url'])): ?>
                                    <img src="<?= htmlspecialchars($recent['image_url']) ?>" class="rounded me-3" width="60" height="60" alt="<?= htmlspecialchars($recent['title']) ?>">
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($recent['title']) ?></h6>
                                        <small class="text-muted">
                                            <?= date('M j', strtotime($recent['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Categories Widget -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-tags me-2"></i> Categories</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <?php foreach ($categories as $category): ?>
                            <a href="category.php?id=<?= $category['category_id'] ?>" class="btn btn-sm btn-outline-secondary m-1">
                                <?= htmlspecialchars($category['name']) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-3 mt-auto">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> Optimal Lifestyle. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>