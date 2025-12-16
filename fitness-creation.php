<?php
// Include database connection and config
include_once "db_connection.php";
include "config.php";
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login-page.php");
    exit();
}

// Safely retrieve user info
$user_id = intval($_SESSION['user_id'] ?? 0);
$user_name = $_SESSION['user_name'] ?? 'Guest';
$nazwa = $_COOKIE['user_name'] ?? $user_name;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    // Validate user_id
    if ($user_id <= 0) {
        die("Invalid user ID");
    }

    // Validate exercise count
    $exercise_count = isset($_POST['exercise_count']) ? intval($_POST['exercise_count']) : 0;
    if ($exercise_count <= 0 || $exercise_count > 20) {
        die("Invalid exercise count. Must be between 1 and 20.");
    }

    // Start transaction
    $connection->begin_transaction();

    try {
        // Get next day number
        $stmt = $connection->prepare("SELECT COALESCE(MAX(day), 0) + 1 AS next_day FROM FitnessRecords WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $day_row = $result->fetch_assoc();
        $next_day = $day_row['next_day'] ?? 1;
        $stmt->close();

        // Insert fitness record
        $stmt = $connection->prepare("INSERT INTO FitnessRecords (day, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $next_day, $user_id);
        $stmt->execute();
        $fitness_id = $connection->insert_id;
        $stmt->close();

        if ($fitness_id <= 0) {
            throw new Exception("Failed to create fitness record");
        }

        // Prepare exercise statement once
        $stmt = $connection->prepare("INSERT INTO Exercise (exercise_type, amount, time, fitness_id) VALUES (?, ?, ?, ?)");
        
        // Process each exercise
        $valid_exercises = 0;
        for ($i = 1; $i <= $exercise_count; $i++) {
            $exercise_typ = trim($_POST["exerNumber_$i"] ?? '');
            $amount = trim($_POST["amNumber_$i"] ?? '');
            $time_in_seconds = intval($_POST["timeNumber_$i"] ?? 0);

            // Validate exercise data
            if (empty($exercise_typ) || empty($amount) || $time_in_seconds <= 0) {
                continue;
            }

            // Convert time to HH:MM:SS
            $time = gmdate("H:i:s", $time_in_seconds);

            // Insert exercise
            $stmt->bind_param("sssi", $exercise_typ, $amount, $time, $fitness_id);
            $stmt->execute();
            $valid_exercises++;
        }

        if ($valid_exercises === 0) {
            throw new Exception("No valid exercises provided");
        }

        // Commit transaction
        $connection->commit();
        
        // Redirect to success page
        header("Location: fitness.php?success=1");
        exit();
    } catch (Exception $e) {
        // Rollback on error
        $connection->rollback();
        die("Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Schedule Creation</title>
    <link rel="stylesheet" href="Styles/fitness-creation.css">
    <script src="Scripts/jquery-3.6.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" 
          crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
</head>


<body class="d-flex flex-column min-vh-100">
    <!-- Navbar remains the same -->

    <!-- Heading -->
    <a href="fitness.php" id="return">Go back</a>
    <div class="container text-center">
        <h1 id="heading">Fitness Schedule Creation</h1>
        <p>Enter the quantity of exercises and fill out the form to create your plan!</p>
    </div>

    <!-- Exercise Input -->
    <div class="container text-center">
        <label class="form-label">Number of exercises:</label>
        <form id="exerciseForm">
            <input type="number" min="1" max="20" class="form-control" id="exerciseQuantity" required>
            <button type="submit" class="btn btn-primary mt-2">Submit</button>
        </form>
    </div>

    <!-- Exercises Form -->
    <div>
        <form class="container" id="exerciseDetailsForm" method="POST">
            <input type="hidden" name="exercise_count" id="exercise_count">
            <div id="exerciseFields"></div>
            <button type="submit" name="submit" class="btn btn-success mt-3">Submit Exercises</button>
        </form>
    </div>

    <!-- Footer remains the same -->

    <!-- Scripts remain the same -->

<!-- Footer -->
<nav class="navbar bottom bg-success mt-auto">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">BrandName</a>
    </div>
</nav>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"
        integrity="sha384-IDwe1+LCz02ROU9k972gdyvl+AESN10+x7tBKgc9I5HFtuNz0wWnPclzo6p9vxnk"
        crossorigin="anonymous"></script>
<script src="Scripts/fitness.js"></script>

<script>
    document.getElementById("exerciseForm").addEventListener("submit", function (e) {
        e.preventDefault();
        let quantity = document.getElementById("exerciseQuantity").value;
        let fieldsDiv = document.getElementById("exerciseFields");
        fieldsDiv.innerHTML = "";
        document.getElementById("exercise_count").value = quantity;

        for (let i = 1; i <= quantity; i++) {
            fieldsDiv.innerHTML += `
                <div class="mb-2">
                    <input type="text" name="exerNumber_${i}" placeholder="Exercise Type" class="form-control" required>
                    <input type="text" name="amNumber_${i}" placeholder="Amount" class="form-control" required>
                    <input type="text" name="timeNumber_${i}" placeholder="Time" class="form-control" required>
                </div>`;
        }
    });
</script>
</body>
</html>