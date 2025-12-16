<?php
session_start();
require_once '../config.php';

// Security check - redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login-page.php");
    exit();
}

$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES, 'UTF-8');
$current_page = 'The Science of Sleep & Recovery';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Understanding how sleep impacts fitness recovery and overall health">
    <title><?= $current_page ?> | Optimal Lifestyle</title>
    
    <!-- Favicon -->
    <link rel="icon" href="../Styles/pictures/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #48bb78;
            --primary-dark: #38a169;
            --dark-color: #2d3748;
            --light-bg: #f8f9fa;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', system-ui, sans-serif;
            line-height: 1.6;
        }
        
        .navbar {
            background: var(--primary-color) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .article-header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('../Styles/pictures/articles/sleep-banner.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 5rem 0;
            margin-bottom: 3rem;
        }
        
        .article-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .section-img {
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            max-width: 100%;
            height: auto;
        }
        
        .key-point {
            background: white;
            border-left: 4px solid var(--primary-color);
            padding: 1.5rem;
            margin: 2rem 0;
            border-radius: 0 8px 8px 0;
        }
        
        .back-to-articles {
            transition: all 0.3s ease;
        }
        
        .back-to-articles:hover {
            transform: translateX(-5px);
        }
        
        footer {
            background: var(--primary-color);
            color: white;
        }
        
        .sleep-stage {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="../index.php">
                <i class="bi bi-activity me-2"></i>Optimal Lifestyle
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php"><i class="bi bi-house-door me-1"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../BMI-page.php"><i class="bi bi-calculator me-1"></i>BMI Calculator</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../articles-page.php"><i class="bi bi-newspaper me-1"></i>Articles</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <a class="btn btn-outline-light" href="../account-page.php">
                        <i class="bi bi-person-circle me-1"></i><?= $user_name ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Article Header -->
    <header class="article-header text-center">
        <div class="container">
            <a href="../articles-page.php" class="btn btn-light back-to-articles mb-4">
                <i class="bi bi-arrow-left me-2"></i>Back to Articles
            </a>
            <h1 class="display-3 fw-bold"><?= $current_page ?></h1>
            <p class="lead mb-0">How quality sleep transforms your fitness results and overall health</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mb-5 flex-grow-1">
        <div class="article-content">
            <!-- Article Meta -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-primary me-2"><i class="bi bi-clock me-1"></i>12 min read</span>
                    <span class="badge bg-success"><i class="bi bi-calendar me-1"></i>Updated: <?= date('M j, Y', strtotime('-2 weeks')) ?></span>
                </div>
                <div class="text-muted">
                    <i class="bi bi-person-circle me-1"></i>By Dr. Sarah Johnson
                </div>
            </div>
            
            <!-- Introduction -->
            <section class="mb-5">
                <p class="lead">Sleep is the foundation upon which all fitness progress is built. Without adequate recovery, even the most perfect training program and nutrition plan will fall short of delivering optimal results.</p>
                
                <img src="../Styles/pictures/articles/sleep-science.jpg" alt="Sleep Science" class="section-img">
                
                <p>In this comprehensive guide, we'll explore the intricate relationship between sleep and physical recovery, examine the different stages of sleep and their unique benefits, and provide practical strategies to enhance your sleep quality for better performance and health.</p>
            </section>
            
            <!-- Key Points -->
            <div class="key-point">
                <h3 class="h4"><i class="bi bi-lightbulb text-primary me-2"></i>Key Takeaways</h3>
                <ul class="mb-0">
                    <li>Adults need 7-9 hours of quality sleep nightly for optimal recovery</li>
                    <li>Deep sleep is crucial for physical restoration and muscle growth</li>
                    <li>REM sleep enhances cognitive function and skill consolidation</li>
                    <li>Sleep deprivation increases injury risk and reduces performance</li>
                    <li>Consistent sleep schedule improves sleep quality</li>
                </ul>
            </div>
            
            <!-- Sleep Stages -->
            <section class="mb-5">
                <h2 class="mb-4"><i class="bi bi-moon-stars text-primary me-2"></i>The Stages of Sleep</h2>
                
                <div class="sleep-stage">
                    <h3 class="h4">1. NREM Stage 1 (Light Sleep)</h3>
                    <p>The transition phase between wakefulness and sleep, lasting several minutes. Muscle activity slows down, and you can be easily awakened. This stage helps prepare your body for deeper sleep.</p>
                </div>
                
                <div class="sleep-stage">
                    <h3 class="h4">2. NREM Stage 2</h3>
                    <p>Your body temperature drops, eye movements stop, and brain waves slow with occasional bursts of rapid waves called sleep spindles. This stage accounts for about 50% of total sleep time and is important for memory consolidation.</p>
                </div>
                
                <div class="sleep-stage">
                    <h3 class="h4">3. NREM Stage 3 (Deep Sleep)</h3>
                    <p>The most restorative sleep stage, crucial for physical recovery. During deep sleep, your body repairs tissues, builds bone and muscle, and strengthens the immune system. Growth hormone is primarily secreted during this stage.</p>
                </div>
                
                <div class="sleep-stage">
                    <h3 class="h4">4. REM Sleep</h3>
                    <p>Characterized by rapid eye movements, increased brain activity, and vivid dreams. REM sleep enhances brain function, supports emotional health, and helps with learning and memory. Athletes particularly benefit from REM sleep for skill consolidation.</p>
                </div>
                
                <img src="../Styles/pictures/articles/sleep-cycle.jpg" alt="Sleep Cycle Diagram" class="section-img">
            </section>
            
            <!-- Sleep and Recovery -->
            <section class="mb-5">
                <h2 class="mb-4"><i class="bi bi-heart-pulse text-primary me-2"></i>Sleep's Role in Athletic Recovery</h2>
                
                <p>During sleep, particularly in deep NREM stages, your body undergoes critical recovery processes:</p>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-muscle text-primary me-2"></i>Muscle Repair</h5>
                                <p class="card-text">Microtears in muscle fibers from exercise are repaired, leading to muscle growth and strength gains. Protein synthesis increases during sleep.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-arrow-repeat text-primary me-2"></i>Glycogen Restoration</h5>
                                <p class="card-text">Muscle glycogen stores are replenished during sleep, ensuring energy availability for your next workout.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-shield-shaded text-primary me-2"></i>Immune Function</h5>
                                <p class="card-text">Sleep enhances immune system function, reducing illness risk that could interrupt training consistency.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-brain text-primary me-2"></i>Motor Skill Consolidation</h5>
                                <p class="card-text">REM sleep helps consolidate motor skills and movement patterns learned during training sessions.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Practical Tips -->
            <section class="mb-5">
                <h2 class="mb-4"><i class="bi bi-check-circle text-primary me-2"></i>Practical Sleep Optimization Tips</h2>
                
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card h-100 border-primary border-2">
                            <div class="card-header bg-primary text-white">
                                <h3 class="h5 mb-0"><i class="bi bi-sun me-2"></i>Daytime Habits</h3>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li><strong>Morning sunlight exposure:</strong> 15-30 minutes upon waking to regulate circadian rhythm</li>
                                    <li><strong>Limit caffeine:</strong> Avoid after 2pm as it can disrupt sleep onset</li>
                                    <li><strong>Exercise timing:</strong> Finish intense workouts at least 3 hours before bedtime</li>
                                    <li><strong>Nap strategically:</strong> Limit to 20-30 minutes before 3pm if needed</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card h-100 border-primary border-2">
                            <div class="card-header bg-primary text-white">
                                <h3 class="h5 mb-0"><i class="bi bi-moon me-2"></i>Evening Routine</h3>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li><strong>Consistent schedule:</strong> Go to bed and wake up at the same time daily</li>
                                    <li><strong>Wind down:</strong> 30-60 minute pre-sleep routine without screens</li>
                                    <li><strong>Cool environment:</strong> Keep bedroom temperature around 65°F (18°C)</li>
                                    <li><strong>Limit liquids:</strong> Reduce fluid intake 1-2 hours before bed</li>
                                    <li><strong>Dark environment:</strong> Use blackout curtains or eye mask</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Conclusion -->
            <section class="mb-5">
                <h2 class="mb-4"><i class="bi bi-bookmark-check text-primary me-2"></i>Final Thoughts</h2>
                <p>Quality sleep is not a luxury—it's a fundamental component of any successful fitness regimen. By prioritizing 7-9 hours of uninterrupted sleep and implementing these evidence-based strategies, you'll enhance recovery, improve performance, and accelerate progress toward your health and fitness goals.</p>
                <p>Remember that sleep needs are individual. Track how you feel with different amounts of sleep and adjust accordingly. Your body's recovery capacity and training results will thank you.</p>
            </section>
            
            <!-- Related Articles -->
            <section class="mt-5 pt-4 border-top">
                <h3 class="h4 mb-4"><i class="bi bi-link-45deg text-primary me-2"></i>Related Articles</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="muscle-building.php" class="card border-0 shadow-sm text-decoration-none h-100">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Diet Tips for Muscle Building</h5>
                                <p class="card-text text-dark">Nutrition strategies to support muscle growth and recovery.</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="healthy-food.php" class="card border-0 shadow-sm text-decoration-none h-100">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Essential Nutrients for Health</h5>
                                <p class="card-text text-dark">Key vitamins and minerals for optimal performance.</p>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
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