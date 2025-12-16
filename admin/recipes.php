<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

// Build query
$query = "SELECT r.*, c.name as category_name 
          FROM recipes r
          LEFT JOIN recipe_categories c ON r.category_id = c.id
          WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (r.name LIKE ? OR r.description LIKE ?)";
    $params = array_fill(0, 2, "%$search%");
}

if (!empty($category)) {
    $query .= " AND r.category_id = ?";
    $params[] = $category;
}

$query .= " ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$countQuery = str_replace('r.*, c.name as category_name', 'COUNT(*) as total', explode('LIMIT', $query)[0]);
$totalStmt = $pdo->prepare($countQuery);
$totalStmt->execute(array_slice($params, 0, -2));
$totalRecipes = $totalStmt->fetchColumn();
$totalPages = ceil($totalRecipes / $limit);

// Get categories for filter
$categories = $pdo->query("SELECT * FROM recipe_categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_recipe'])) {
        $name = trim($_POST['name']);
        $category_id = $_POST['category_id'] ? (int)$_POST['category_id'] : null;
        $description = trim($_POST['description']);
        $ingredients = trim($_POST['ingredients']);
        $instructions = trim($_POST['instructions']);
        $prep_time = (int)$_POST['prep_time'];
        $cook_time = (int)$_POST['cook_time'];
        $servings = (int)$_POST['servings'];
        $calories = (int)$_POST['calories'];
        
        // Validation
        if (empty($name)) {
            $errors[] = "Recipe name is required";
        } elseif (empty($ingredients)) {
            $errors[] = "Ingredients are required";
        } elseif (empty($instructions)) {
            $errors[] = "Instructions are required";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO recipes 
                (name, category_id, description, ingredients, instructions, 
                 prep_time, cook_time, servings, calories, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            if ($stmt->execute([$name, $category_id, $description, $ingredients, $instructions, 
                               $prep_time, $cook_time, $servings, $calories])) {
                $recipe_id = $pdo->lastInsertId();
                $success = true;
                $_POST = []; // Clear form
                header("Location: recipe-view.php?id=$recipe_id");
                exit;
            } else {
                $errors[] = "Failed to add recipe";
            }
        }
    }
}

$page_title = "Recipe Management";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Recipe Management</h1>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addRecipeModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Recipe
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
        Recipe added successfully!
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Recipes</h6>
            <form class="d-flex">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search recipes..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <select class="form-control" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
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
                            <th>Name</th>
                            <th>Category</th>
                            <th>Prep Time</th>
                            <th>Calories</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recipes as $recipe): ?>
                        <tr>
                            <td><?= htmlspecialchars($recipe['name']) ?></td>
                            <td><?= htmlspecialchars($recipe['category_name'] ?? 'Uncategorized') ?></td>
                            <td><?= $recipe['prep_time'] ?> mins</td>
                            <td><?= $recipe['calories'] ?></td>
                            <td><?= date('M j, Y', strtotime($recipe['created_at'])) ?></td>
                            <td>
                                <a href="recipe-view.php?id=<?= $recipe['id'] ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="recipe-edit.php?id=<?= $recipe['id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="recipe-delete.php?id=<?= $recipe['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
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
                        <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&category=<?= $category ?>">Previous</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= $category ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&category=<?= $category ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Add Recipe Modal -->
<div class="modal fade" id="addRecipeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Recipe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Recipe Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-control" id="category_id" name="category_id">
                                    <option value="">-- Select Category --</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= ($_POST['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="prep_time" class="form-label">Prep Time (mins)</label>
                                <input type="number" class="form-control" id="prep_time" name="prep_time" 
                                       value="<?= htmlspecialchars($_POST['prep_time'] ?? '15') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cook_time" class="form-label">Cook Time (mins)</label>
                                <input type="number" class="form-control" id="cook_time" name="cook_time" 
                                       value="<?= htmlspecialchars($_POST['cook_time'] ?? '30') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="servings" class="form-label">Servings</label>
                                <input type="number" class="form-control" id="servings" name="servings" 
                                       value="<?= htmlspecialchars($_POST['servings'] ?? '4') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="calories" class="form-label">Calories per Serving</label>
                        <input type="number" class="form-control" id="calories" name="calories" 
                               value="<?= htmlspecialchars($_POST['calories'] ?? '300') ?>">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="ingredients" class="form-label">Ingredients *</label>
                        <textarea class="form-control" id="ingredients" name="ingredients" rows="5" required><?= htmlspecialchars($_POST['ingredients'] ?? '') ?></textarea>
                        <small class="text-muted">One ingredient per line</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="instructions" class="form-label">Instructions *</label>
                        <textarea class="form-control" id="instructions" name="instructions" rows="5" required><?= htmlspecialchars($_POST['instructions'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_recipe" class="btn btn-primary">Add Recipe</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>