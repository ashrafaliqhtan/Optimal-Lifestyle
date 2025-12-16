<?php
session_start();
require_once '../config.php';

// Security check - redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login-page.php");
    exit();
}

$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES, 'UTF-8');
$current_page = 'Effective Cardio Workouts';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Maximize your cardiovascular fitness with these effective workout routines">
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
            --danger-color: #e53e3e;
            --info-color: #3182ce;
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
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('../Styles/pictures/articles/cardio-banner.jpg');
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
        
        .workout-card {
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .workout-card:hover {
            transform: translateY(-5px);
        }
        
        .workout-card-header {
            background: var(--primary-color);
            color: white;
            padding: 1rem;
            font-weight: 600;
        }
        
        .workout-card-body {
            background: white;
            padding: 1.5rem;
        }
        
        .benefit-badge {
            background: var(--info-color);
            color: white;
            padding: 0.35em 0.65em;
            border-radius: 50rem;
            font-size: 0.875em;
            font-weight: 600;
            display: inline-block;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .intensity-badge {
            background: var(--danger-color);
            color: white;
            padding: 0.35em 0.65em;
            border-radius: 50rem;
            font-size: 0.875em;
            font-weight: 600;
            display: inline-block;
        }
        
        .key-point {
            background: white;
            border-left: 4px solid var(--primary-color);
            padding: 1.5rem;
            margin: 2rem 0;
            border-radius: 0 8px 8px 0;
        }
        
        footer {
            background: var(--primary-color);
            color: white;
        }
        
        .equipment-list {
            list-style-type: none;
            padding-left: 0;
        }
        
        .equipment-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .equipment-list li:last-child {
            border-bottom: none;
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
            <a href="../articles-page.php" class="btn btn-light mb-4">
                <i class="bi bi-arrow-left me-2"></i>Back to Articles
            </a>
            <h1 class="display-3 fw-bold"><?= $current_page ?></h1>
            <p class="lead mb-0">Boost your endurance, burn calories, and improve heart health with these proven routines</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mb-5 flex-grow-1">
        <div class="article-content">
            <!-- Article Meta -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge bg-primary me-2"><i class="bi bi-clock me-1"></i>10 min read</span>
                    <span class="badge bg-success"><i class="bi bi-calendar me-1"></i>Updated: <?= date('M j, Y', strtotime('-1 week')) ?></span>
                </div>
                <div class="text-muted">
                    <i class="bi bi-person-circle me-1"></i>By Coach Mark Williams
                </div>
            </div>
            
            <!-- Introduction -->
            <section class="mb-5">
                <p class="lead">Cardiovascular exercise is essential for maintaining a healthy heart, boosting endurance, and supporting fat loss. But not all cardio is created equal.</p>
                
                <img src="../Styles/pictures/articles/cardio-types.jpg" alt="Types of Cardio" class="img-fluid rounded mb-4">
                
                <p>In this guide, we'll explore the most effective cardio workouts for different fitness goals, from high-intensity interval training (HIIT) that torches calories in minimal time to steady-state cardio that builds endurance. You'll learn how to structure each workout, what equipment you might need, and how to progress over time.</p>
            </section>
            
            <!-- Key Points -->
            <div class="key-point">
                <h3 class="h4"><i class="bi bi-lightbulb text-primary me-2"></i>Key Benefits of Regular Cardio</h3>
                <div class="d-flex flex-wrap">
                    <span class="benefit-badge"><i class="bi bi-heart-pulse me-1"></i>Improves heart health</span>
                    <span class="benefit-badge"><i class="bi bi-fire me-1"></i>Burns calories</span>
                    <span class="benefit-badge"><i class="bi bi-lungs me-1"></i>Increases lung capacity</span>
                    <span class="benefit-badge"><i class="bi bi-emoji-smile me-1"></i>Boosts mood</span>
                    <span class="benefit-badge"><i class="bi bi-moon-stars me-1"></i>Improves sleep</span>
                    <span class="benefit-badge"><i class="bi bi-activity me-1"></i>Enhances endurance</span>
                </div>
            </div>
            
            <!-- Workout 1 -->
            <div class="workout-card">
                <div class="workout-card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-lightning-charge me-2"></i>High-Intensity Interval Training (HIIT)</span>
                    <span class="intensity-badge">High Intensity</span>
                </div>
                <div class="workout-card-body">
                    <h3 class="h5">The Ultimate Fat Burner</h3>
                    <p>HIIT alternates between short bursts of maximum effort and brief recovery periods. This approach keeps your heart rate elevated while allowing for higher overall intensity.</p>
                    
                    <h4 class="h6 mt-4">Sample Workout:</h4>
                    <ul>
                        <li>Warm-up: 5 minutes light cardio</li>
                        <li>20 seconds sprint (or maximum effort)</li>
                        <li>40 seconds active recovery (walking or slow pace)</li>
                        <li>Repeat for 8-12 rounds</li>
                        <li>Cool-down: 5 minutes light cardio + stretching</li>
                    </ul>
                    
                    <h4 class="h6 mt-4">Equipment Options:</h4>
                    <ul class="equipment-list">
                        <li><i class="bi bi-check-circle text-primary me-2"></i>None (running in place, burpees, jump squats)</li>
                        <li><i class="bi bi-check-circle text-primary me-2"></i>Treadmill</li>
                        <li><i class="bi bi-check-circle text-primary me-2"></i>Stationary bike</li>
                        <li><i class="bi bi-check-circle text-primary me-2"></i>Rowing machine</li>
                    </ul>
                    
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i><strong>Pro Tip:</strong> Start with just 4-6 intervals and gradually increase as your fitness improves.
                    </div>
                </div>
            </div>
            
            <!-- Workout 2 -->
            <div class="workout-card">
                <div class="workout-card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-speedometer2 me-2"></i>Moderate-Intensity Steady State (MISS)</span>
                    <span class="intensity-badge" style="background: #f6ad55;">Moderate Intensity</span>
                </div>
                <div class="workout-card-body">
                    <h3 class="h5">The Endurance Builder</h3>
                    <p>Maintaining a consistent, moderate pace for an extended period improves cardiovascular endurance and is excellent for active recovery days.</p>
                    
                    <h4 class="h6 mt-4">Sample Workout:</h4>
                    <ul>
                        <li>Warm-up: 5 minutes light cardio</li>
                        <li>30-60 minutes at a pace where you can maintain a conversation (60-70% max heart rate)</li>
                        <li>Cool-down: 5 minutes light cardio + stretching</li>
                    </ul>
                    
                    <h4 class="h6 mt-4">Equipment Options:</h4>
                    <ul class="equipment-list">
                        <li><i class="bi bi-check-circle text-primary me-2"></i>Treadmill</li>
                        <li><i class="bi bi-check-circle text-primary me-2"></i>Elliptical machine</li>
                        <li><i class="bi bi-check-circle text-primary me-2"></i>Stationary bike</li>
                        <li><i class="bi bi-check-circle text-primary me-2"></i>Swimming</li>
                    </ul>
                    
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i><strong>Pro Tip:</strong> Add incline or resistance to increase intensity without increasing speed.
                    </div>
                </div>
            </div>
            
            <!-- Workout 3 -->
            <div class="workout-card">
                <div class="workout-card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-arrow-repeat me-2"></i>Circuit Training</span>
                    <span class="intensity-badge" style="background: #68d391;">Variable Intensity</span>
                </div>
                <div class="workout-card-body">
                    <h3 class="h5">The Full-Body Conditioner</h3>
                    <p>Combining cardio bursts with strength exercises keeps your heart rate up while building muscular endurance.</p>
                    
                    <h4 class="h6 mt-4">Sample Workout:</h4>
                    <ul>
                        <li>Warm-up: 5 minutes light cardio</li>
                        <li>Jump rope: 1 minute</li>
                        <li>Bodyweight squats: 15 reps</li>
                        <li>Push-ups: 10 reps</li>
                        <li>Mountain climbers: 30 seconds</li>
                        <li>Plank: 30 seconds</li>
                        <li>Rest: 1 minute</li>
                        <li>Repeat circuit 3-5 times</li>
                        <li>Cool-down: 5 minutes light cardio + stretching</li>
                    </ul>
                    
                    <h4 class="h6 mt-4">Equipment Options:</h4>
                    <ul class="equipment-list">
                        <li><i class="bi bi-check-circle text-primary me-2"></i>Jump rope</li>
                        <li><i class="bi bi-check-circle text-primary me-2"></i>Kettlebells</li>
                        <li><i class="bi bi-check-circle text-primary me-2"></i>Dumbbells</li>
                        <li><i class="bi bi-check-circle text-primary me-2"></i>Resistance bands</li>
                    </ul>
                    
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i><strong>Pro Tip:</strong> Adjust the work-to-rest ratio based on your fitness level (beginners 1:2, advanced 2:1).
                    </div>
                </div>
            </div>
            
            <!-- Progression Tips -->
            <section class="mb-5">
                <h2 class="mb-4"><i class="bi bi-graph-up text-primary me-2"></i>Progression Strategies</h2>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-arrow-up-right text-primary me-2"></i>Increase Duration</h5>
                                <p class="card-text">Gradually add 5-10% more time to your workouts each week. For HIIT, add more intervals before increasing intensity.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-lightning-charge text-primary me-2"></i>Increase Intensity</h5>
                                <p class="card-text">Once comfortable, increase speed, resistance, or reduce rest periods between intervals.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-collection text-primary me-2"></i>Variety</h5>
                                <p class="card-text">Rotate between different cardio methods to prevent plateaus and overuse injuries.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-calendar-check text-primary me-2"></i>Consistency</h5>
                                <p class="card-text">Aim for 3-5 cardio sessions per week, allowing for proper recovery between intense sessions.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Safety Tips -->
            <section class="mb-5">
                <h2 class="mb-4"><i class="bi bi-shield-check text-primary me-2"></i>Safety Considerations</h2>
                
                <div class="alert alert-warning">
                    <h3 class="h5"><i class="bi bi-exclamation-triangle me-2"></i>Important Precautions</h3>
                    <ul class="mb-0">
                        <li>Consult your doctor before starting any new exercise program, especially if you have heart conditions or other health concerns</li>
                        <li>Stay hydrated before, during, and after workouts</li>
                        <li>Listen to your body - sharp pain means stop immediately</li>
                        <li>Proper form is more important than speed or intensity</li>
                        <li>Allow for at least one full rest day per week</li>
                    </ul>
                </div>
            </section>
            
            <!-- Related Articles -->
            <section class="mt-5 pt-4 border-top">
                <h3 class="h4 mb-4"><i class="bi bi-link-45deg text-primary me-2"></i>Related Articles</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="muscle-building.php" class="card border-0 shadow-sm text-decoration-none h-100">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Diet Tips for Muscle Building</h5>
                                <p class="card-text text-dark">Nutrition strategies to support your fitness goals.</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="sleep-science.php" class="card border-0 shadow-sm text-decoration-none h-100">
                            <div class="card-body">
                                <h5 class="card-title text-primary">The Science of Sleep & Recovery</h5>
                                <p class="card-text text-dark">How sleep impacts your workout results.</p>
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