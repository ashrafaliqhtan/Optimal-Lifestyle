<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

$search = $_GET['search'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query
$query = "SELECT * FROM Food WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (food_name LIKE ? OR description LIKE ?)";
    $params = array_fill(0, 2, "%$search%");
}

$query .= " ORDER BY food_name LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$countQuery = str_replace('*', 'COUNT(*) as total', explode('LIMIT', $query)[0]);
$totalStmt = $pdo->prepare($countQuery);
$totalStmt->execute(array_slice($params, 0, -2));
$totalFoods = $totalStmt->fetchColumn();
$totalPages = ceil($totalFoods / $limit);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_food'])) {
        $food_name = trim($_POST['food_name']);
        $calorie_amount = (int)$_POST['calorie_amount'];
        $description = trim($_POST['description']);
        $protein = (float)$_POST['protein'];
        $carbs = (float)$_POST['carbs'];
        $fat = (float)$_POST['fat'];
        
        // Validation
        if (empty($food_name)) {
            $errors[] = "Food name is required";
        } elseif ($calorie_amount <= 0) {
            $errors[] = "Calorie amount must be positive";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO Food 
                (food_name, calorie_amount, description, protein, carbs, fat, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            
            if ($stmt->execute([$food_name, $calorie_amount, $description, $protein, $carbs, $fat])) {
                $success = true;
                $_POST = []; // Clear form
                header("Location: foods.php");
                exit;
            } else {
                $errors[] = "Failed to add food";
            }
        }
    }
}

$page_title = "Food Database";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Food Database</h1>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addFoodModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Food
        </button>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <?php if (isset($success) && $success): ?>
    <div class="alert alert-success">
        Food added successfully!
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Foods</h6>
            <form class="d-flex">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search foods..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Food Name</th>
                            <th>Calories</th>
                            <th>Protein (g)</th>
                            <th>Carbs (g)</th>
                            <th>Fat (g)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($foods as $food): ?>
                        <tr>
                            <td><?= htmlspecialchars($food['food_name']) ?></td>
                            <td><?= $food['calorie_amount'] ?></td>
                            <td><?= $food['protein'] ?></td>
                            <td><?= $food['carbs'] ?></td>
                            <td><?= $food['fat'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                        data-bs-target="#viewFoodModal"
                                        data-name="<?= htmlspecialchars($food['food_name']) ?>"
                                        data-calories="<?= $food['calorie_amount'] ?>"
                                        data-protein="<?= $food['protein'] ?>"
                                        data-carbs="<?= $food['carbs'] ?>"
                                        data-fat="<?= $food['fat'] ?>"
                                        data-desc="<?= htmlspecialchars($food['description']) ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="food-edit.php?id=<?= $food['food_id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="food-delete.php?id=<?= $food['food_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">Previous</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Add Food Modal -->
<div class="modal fade" id="addFoodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Food</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="food_name" class="form-label">Food Name *</label>
                        <input type="text" class="form-control" id="food_name" name="food_name" 
                               value="<?= htmlspecialchars($_POST['food_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="calorie_amount" class="form-label">Calories *</label>
                        <input type="number" class="form-control" id="calorie_amount" name="calorie_amount" 
                               value="<?= htmlspecialchars($_POST['calorie_amount'] ?? '') ?>" min="1" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="protein" class="form-label">Protein (g)</label>
                                <input type="number" step="0.1" class="form-control" id="protein" name="protein" 
                                       value="<?= htmlspecialchars($_POST['protein'] ?? '0') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="carbs" class="form-label">Carbs (g)</label>
                                <input type="number" step="0.1" class="form-control" id="carbs" name="carbs" 
                                       value="<?= htmlspecialchars($_POST['carbs'] ?? '0') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fat" class="form-label">Fat (g)</label>
                                <input type="number" step="0.1" class="form-control" id="fat" name="fat" 
                                       value="<?= htmlspecialchars($_POST['fat'] ?? '0') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_food" class="btn btn-primary">Add Food</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Food Modal -->
<div class="modal fade" id="viewFoodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Food Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Food Name</th>
                        <td id="foodName"></td>
                    </tr>
                    <tr>
                        <th>Calories</th>
                        <td id="foodCalories"></td>
                    </tr>
                    <tr>
                        <th>Protein</th>
                        <td id="foodProtein"></td>
                    </tr>
                    <tr>
                        <th>Carbs</th>
                        <td id="foodCarbs"></td>
                    </tr>
                    <tr>
                        <th>Fat</th>
                        <td id="foodFat"></td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td id="foodDesc"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Handle view modal
document.getElementById('viewFoodModal').addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    document.getElementById('foodName').textContent = button.getAttribute('data-name');
    document.getElementById('foodCalories').textContent = button.getAttribute('data-calories');
    document.getElementById('foodProtein').textContent = button.getAttribute('data-protein') + 'g';
    document.getElementById('foodCarbs').textContent = button.getAttribute('data-carbs') + 'g';
    document.getElementById('foodFat').textContent = button.getAttribute('data-fat') + 'g';
    document.getElementById('foodDesc').textContent = button.getAttribute('data-desc') || 'No description';
});
</script>

<?php include 'includes/footer.php'; ?>