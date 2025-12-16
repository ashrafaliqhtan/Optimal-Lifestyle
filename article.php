<?php
require_once 'admin/config/database.php';

// Check if article ID is provided
if (!isset($_GET['id'])) {
    header("Location: articles.php");
    exit();
}

$article_id = (int)$_GET['id'];

// Get the article details (only published articles)
$stmt = $pdo->prepare("
    SELECT 
        a.article_id, 
        a.title, 
        a.content, 
        a.image_url, 
        a.created_at, 
        a.updated_at,
        c.name as category_name
    FROM Articles a
    LEFT JOIN categories c ON a.category_id = c.category_id
    WHERE a.article_id = ? AND a.status = 'published'
");
$stmt->execute([$article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header("Location: articles.php");
    exit();
}

// Update view count (if you add this column later)
// $pdo->prepare("UPDATE Articles SET views = views + 1 WHERE article_id = ?")->execute([$article_id]);

$page_title = htmlspecialchars($article['title']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars(substr(strip_tags($article['content']), 0, 160)) ?>">
    <title><?= htmlspecialchars($article['title']) ?> | Optimal Lifestyle</title>
    
    <!-- Favicon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {

          
            --primary-color: #28a745;
            --primary-dark: #218838;
            --secondary-color: #28a745;
            --accent-color: #4895ef;
            --dark-color: #2b2d42;
            --light-color: #f8f9fa;
            --gray-color: #6c757d;
            --border-radius: 12px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            background-color: #fafafa;
            font-family: 'Poppins', sans-serif;
            color: #333;
            line-height: 1.7;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
        }
        
        .nav-link {
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: var(--transition);
            border-radius: 6px;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
        }
        
        /* Article Header */
        .article-header {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 3rem 2rem;
            margin-bottom: 3rem;
            text-align: center;
            border-bottom: 4px solid var(--primary-color);
        }
        
        .article-title {
            font-size: 2.5rem;
            line-height: 1.3;
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
        }
        
        .article-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--accent-color);
            border-radius: 3px;
        }
        
        .article-image {
            max-height: 500px;
            width: 100%;
            object-fit: cover;
            border-radius: var(--border-radius);
            margin: 2rem 0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }
        
        .article-image:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        /* Article Meta */
        .article-meta-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .article-meta {
            display: flex;
            align-items: center;
            color: var(--gray-color);
            font-size: 0.95rem;
        }
        
        .article-meta i {
            margin-right: 8px;
            color: var(--accent-color);
        }
        
        .category-badge {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: var(--transition);
        }
        
        .category-badge:hover {
            background-color: rgba(67, 97, 238, 0.2);
            transform: translateY(-2px);
        }
        
        .category-badge i {
            margin-right: 6px;
        }
        
        /* Article Content */
        .article-content {
            font-family: 'Poppins', sans-serif;
            line-height: 1.8;
            color: #444;
            background-color: white;
            padding: 3rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 3rem;
        }
        
        .article-content h1 {
            font-size: 2.2rem;
            margin-top: 2.5rem;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
        }
        
        .article-content h2 {
            font-size: 1.8rem;
            margin-top: 2.2rem;
            margin-bottom: 1.2rem;
            color: var(--dark-color);
        }
        
        .article-content h3 {
            font-size: 1.5rem;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        
        .article-content p {
            margin-bottom: 1.8rem;
            font-size: 1.05rem;
        }
        
        .article-content img {
            max-width: 100%;
            height: auto;
            margin: 2rem 0;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .article-content a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .article-content a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .article-content ul,
        .article-content ol {
            margin-bottom: 1.8rem;
            padding-left: 2.2rem;
        }
        
        .article-content li {
            margin-bottom: 0.8rem;
        }
        
        .article-content blockquote {
            border-left: 4px solid var(--primary-color);
            padding: 1.5rem;
            margin: 2rem 0;
            background-color: rgba(67, 97, 238, 0.05);
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
            font-style: italic;
            color: #555;
        }
        
        .article-content table {
            width: 100%;
            margin: 2rem 0;
            border-collapse: collapse;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
        
        .article-content table th,
        .article-content table td {
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
        }
        
        .article-content table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
        }
        
        .article-content table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Article Footer */
        .article-footer {
            background-color: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .article-footer .article-meta {
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            border-radius: 50px;
            transition: var(--transition);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--dark-color), #1a1a2e);
            padding: 3rem 0;
            margin-top: 5rem;
        }
        
        footer p {
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .article-title {
                font-size: 2rem;
            }
            
            .article-content {
                padding: 2rem 1.5rem;
            }
            
            .article-header {
                padding: 2rem 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .article-title {
                font-size: 1.8rem;
            }
            
            .article-meta-container {
                flex-direction: column;
                gap: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">OptimalLifestyle</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="BMI-page.php">BMI Calculator</a></li>
                    <li class="nav-item"><a class="nav-link" href="articles-page.php">Articles</a></li>
                    <li class="nav-item"><a class="nav-link active" href="fitness.php">Fitness Planner</a></li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-light me-3 d-none d-sm-inline">Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                    <a class="btn btn-light btn-sm" href="account-page.php">
                        <i class="bi bi-person-circle"></i> My Account
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <!-- Article Header -->
                <div class="article-header">
                    <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>
                    
                    <div class="article-meta-container">
                        <?php if (!empty($article['category_name'])): ?>
                        <a href="#" class="category-badge">
                            <i class="fas fa-tag"></i>
                            <?= htmlspecialchars($article['category_name']) ?>
                        </a>
                        <?php endif; ?>
                        
                        <div class="article-meta">
                            <i class="far fa-calendar-alt"></i>
                            <?= date('F j, Y', strtotime($article['created_at'])) ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($article['image_url'])): ?>
                    <img src="<?= htmlspecialchars($article['image_url']) ?>" class="article-image" alt="<?= htmlspecialchars($article['title']) ?>">
                    <?php endif; ?>
                </div>
                
                <!-- Article Content -->
                <div class="article-content">
                    <?= $article['content'] ?>
                </div>
                
                <!-- Article Footer -->
                <div class="article-footer">
                    <div class="article-meta">
                        <i class="far fa-clock"></i>
                        <span>Published: <?= date('F j, Y \a\t g:i a', strtotime($article['created_at'])) ?></span>
                    </div>
                    <?php if ($article['updated_at'] != $article['created_at']): ?>
                    <div class="article-meta">
                        <i class="fas fa-sync-alt"></i>
                        <span>Updated: <?= date('F j, Y \a\t g:i a', strtotime($article['updated_at'])) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Back Button -->
                <div class="text-center">
                    <a href="articles-page.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i> Back to Articles
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-5">
        <div class="container text-center">
            <p>&copy; <?= date('Y') ?> Optimal Lifestyle. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Smooth scroll and animations -->
    <script>
        // Add smooth scrolling to all links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        
        // Add animation to elements when they come into view
        const animateOnScroll = () => {
            const elements = document.querySelectorAll('.article-header, .article-content, .article-footer');
            
            elements.forEach(element => {
                const elementPosition = element.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.2;
                
                if (elementPosition < screenPosition) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }
            });
        };
        
        // Set initial state for animations
        window.addEventListener('DOMContentLoaded', () => {
            const elements = document.querySelectorAll('.article-header, .article-content, .article-footer');
            elements.forEach(element => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            });
            
            // Trigger animations after a short delay
            setTimeout(animateOnScroll, 300);
        });
        
        // Add scroll event listener
        window.addEventListener('scroll', animateOnScroll);
    </script>
</body>
</html>