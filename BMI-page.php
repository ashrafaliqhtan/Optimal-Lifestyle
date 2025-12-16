<?php
session_start();
require_once 'config.php';

// Security check - redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login-page.php");
    exit();
}

$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Calculate your Body Mass Index (BMI) to assess your weight status">
    <title>BMI Calculator | Optimal Lifestyle</title>
    
    <!-- Favicon -->
    <link rel="icon" href="Styles/pictures/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #48bb78;
            --primary-dark: #38a169;
            --info-color: #3182ce;
            --light-bg: #f8fafc;
        }
        
        body {
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)), 
                        url('Styles/pictures/wallpaper/wallpaper1.jpg') center center no-repeat fixed;
            background-size: cover;
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
        }
        
        .navbar {
            background: var(--primary-color) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .calculator-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }
        
        .calculator-card:hover {
            transform: translateY(-5px);
        }
        
        .result-display {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .bmi-table {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .bmi-table th {
            background: var(--primary-color);
            color: white;
        }
        
        .formula-box {
            background: #edf2f7;
            border-left: 4px solid var(--info-color);
        }
        
        footer {
            background: var(--primary-color);
            color: white;
        }
        
        .input-group-text {
            min-width: 45px;
            justify-content: center;
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
                        <a class="nav-link active" href="BMI-page.php"><i class="bi bi-calculator me-1"></i>BMI Calculator</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="articles-page.php"><i class="bi bi-newspaper me-1"></i>Articles</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">

                    <a class="btn btn-outline-light" href="account-page.php">
                        <i class="bi bi-person-circle me-1"></i><?= $user_name ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-4 flex-grow-1">
        <!-- Page Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold"><i class="bi bi-calculator me-2"></i>BMI Calculator</h1>
            <p class="lead text-muted">Calculate your Body Mass Index to assess your weight status</p>
        </div>

        <!-- Calculator Card -->
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="calculator-card p-4 mb-5">
                    <h2 class="text-center mb-4"><i class="bi bi-speedometer2 me-2"></i>Calculate Your BMI</h2>
                    <p class="text-center mb-4">Enter your height and weight to get your BMI score</p>
                    
                    <form id="bmiForm">
                        <!-- Height Input -->
                        <div class="mb-3">
                            <label for="height" class="form-label">Height (cm)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-rulers"></i></span>
                                <input type="number" class="form-control" id="height" placeholder="e.g., 175" min="100" max="250" step="0.1" required>
                            </div>
                        </div>
                        
                        <!-- Weight Input -->
                        <div class="mb-4">
                            <label for="weight" class="form-label">Weight (kg)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-speedometer"></i></span>
                                <input type="number" class="form-control" id="weight" placeholder="e.g., 70" min="30" max="200" step="0.1" required>
                            </div>
                        </div>
                        
                        <!-- Calculate Button -->
                        <button type="button" onclick="calculateBMI()" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-calculator me-2"></i>Calculate BMI
                        </button>
                        
                        <!-- Result Display -->
                        <div id="result" class="result-display text-center mt-4 p-3 rounded"></div>
                    </form>
                </div>
            </div>
        </div>

        <!-- BMI Information -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="mb-5">
                    <h2 class="text-center mb-4"><i class="bi bi-info-circle me-2"></i>About BMI</h2>
                    <article class="text-justify">
                        <p>Body Mass Index (BMI) is a measurement of body fat based on height and weight that applies to adult men and women. It is used to screen for weight categories that may lead to health problems.</p>
                        
                        <p>This calculator is suitable for most adults with average body types and low to moderate physical activity. However, it may not be accurate for:</p>
                        <ul>
                            <li>Athletes or bodybuilders (who may have high muscle mass)</li>
                            <li>Pregnant or breastfeeding women</li>
                            <li>People with physical disabilities</li>
                            <li>Children and teenagers</li>
                        </ul>
                        
                        <h4 class="mt-4 text-center">BMI Formula</h4>
                        <div class="formula-box p-3 my-3">
                            <p class="text-center mb-1 fw-bold">weight (kg) / [height (m)]²</p>
                            <p class="text-center mb-0">or alternatively:</p>
                            <p class="text-center fw-bold">[weight (kg) / height (cm) / height (cm)] × 10,000</p>
                        </div>
                    </article>
                </div>
                
                <!-- BMI Range Table -->
                <h3 class="text-center mb-4"><i class="bi bi-table me-2"></i>BMI Classification</h3>
                <div class="table-responsive">
                    <table class="table bmi-table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">BMI Range</th>
                                <th class="text-center">Weight Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">Below 18.5</td>
                                <td class="text-center">Underweight</td>
                            </tr>
                            <tr>
                                <td class="text-center">18.5 - 24.9</td>
                                <td class="text-center">Healthy Weight</td>
                            </tr>
                            <tr>
                                <td class="text-center">25 - 29.9</td>
                                <td class="text-center">Overweight</td>
                            </tr>
                            <tr>
                                <td class="text-center">30 or above</td>
                                <td class="text-center">Obesity</td>
                            </tr>
                        </tbody>
                    </table>
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
    
    <!-- BMI Calculation Script -->
    <script>
        function calculateBMI() {
            // Get input values
            const height = parseFloat(document.getElementById('height').value);
            const weight = parseFloat(document.getElementById('weight').value);
            
            // Validate inputs
            if (isNaN(height) || isNaN(weight) || height <= 0 || weight <= 0) {
                showResult("Please enter valid height and weight values.", "danger");
                return;
            }
            
            if (height < 100 || height > 250) {
                showResult("Height should be between 100cm and 250cm.", "danger");
                return;
            }
            
            if (weight < 30 || weight > 200) {
                showResult("Weight should be between 30kg and 200kg.", "danger");
                return;
            }
            
            // Calculate BMI
            const bmi = (weight / Math.pow(height / 100, 2)).toFixed(1);
            
            // Determine status
            let status, statusClass;
            if (bmi < 18.5) {
                status = "Underweight";
                statusClass = "info";
            } else if (bmi < 25) {
                status = "Healthy Weight";
                statusClass = "success";
            } else if (bmi < 30) {
                status = "Overweight";
                statusClass = "warning";
            } else {
                status = "Obesity";
                statusClass = "danger";
            }
            
            // Show result
            const resultHTML = `
                <div class="alert alert-${statusClass}">
                    <strong>Your BMI:</strong> ${bmi}<br>
                    <strong>Status:</strong> ${status}
                </div>
                <p class="small text-muted mt-2">BMI is a screening tool but not a diagnostic of body fatness or health.</p>
            `;
            
            showResult(resultHTML, statusClass);
        }
        
        function showResult(message, type = "success") {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = message;
            resultDiv.style.display = "block";
        }
        
        // Initialize - focus on height input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('height').focus();
        });
    </script>
</body>
</html>