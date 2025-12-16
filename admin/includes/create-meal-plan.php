<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-check.php';

// Initialize variables
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $planName = trim($_POST['planName'] ?? '');
    $planDescription = trim($_POST['planDescription'] ?? '');
    $dayNames = $_POST['dayNames'] ?? [];
    $mealNames = $_POST['mealNames'] ?? [];
    $mealTimes = $_POST['mealTimes'] ?? [];
    $mealCalories = $_POST['mealCalories'] ?? [];
    $mealNotes = $_POST['mealNotes'] ?? [];

    // Validate inputs
    if (empty($planName)) {
        $errors[] = "Meal plan name is required";
    }

    if (empty($mealNames) || count($mealNames) === 0) {
        $errors[] = "At least one meal is required";
    }

    // If no errors, proceed with database operations
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // 1. Insert the main meal plan
            $stmt = $pdo->prepare("
                INSERT INTO meal_plans 
                (name, description, created_by, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$planName, $planDescription, $_SESSION['user_id']]);
            $mealPlanId = $pdo->lastInsertId();

            // 2. Process days and meals
            $mealsPerDay = count($mealTimes) / count($dayNames);
            $dayCounter = 0;
            
            for ($i = 0; $i < count($mealNames); $i++) {
                // Check if we need to start a new day
                if ($i % $mealsPerDay === 0) {
                    $dayName = $dayNames[$dayCounter] ?? "Day " . ($dayCounter + 1);
                    
                    // Insert meal plan day
                    $stmt = $pdo->prepare("
                        INSERT INTO meal_plan_days 
                        (meal_plan_id, day_number, day_name) 
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([$mealPlanId, $dayCounter + 1, $dayName]);
                    $dayId = $pdo->lastInsertId();
                    $dayCounter++;
                }
                
                // Insert meal
                $stmt = $pdo->prepare("
                    INSERT INTO meal_plan_meals 
                    (day_id, meal_name, meal_time, calories, notes) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $dayId,
                    $mealNames[$i],
                    $mealTimes[$i],
                    $mealCalories[$i],
                    $mealNotes[$i] ?? null
                ]);
            }

            $pdo->commit();
            $success = true;
            
            // Redirect to view the new plan
            header("Location: ../meal-plan-view.php?id=$mealPlanId");
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Error creating meal plan: " . $e->getMessage();
            error_log("Meal Plan Creation Error: " . $e->getMessage());
            
            // Store errors in session to display on redirect
            $_SESSION['meal_plan_errors'] = $errors;
            $_SESSION['meal_plan_form_data'] = $_POST;
            header("Location: ../meal-plans.php");
            exit;
        }
    } else {
        // Store errors in session to display on redirect
        $_SESSION['meal_plan_errors'] = $errors;
        $_SESSION['meal_plan_form_data'] = $_POST;
        header("Location: ../meal-plans.php");
        exit;
    }
} else {
    // Not a POST request - redirect back
    header("Location: ../meal-plans.php");
    exit;
}