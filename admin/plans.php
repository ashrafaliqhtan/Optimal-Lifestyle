<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

$search = $_GET['search'] ?? '';
$difficulty = $_GET['difficulty'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

// Build query
$query = "SELECT * FROM training_plans WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $params = array_fill(0, 2, "%$search%");
}

if (!empty($difficulty)) {
    $query .= " AND difficulty = ?";
    $params[] = $difficulty;
}

$query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$countQuery = str_replace('*', 'COUNT(*) as total', explode('LIMIT', $query)[0]);
$totalStmt = $pdo->prepare($countQuery);
$totalStmt->execute(array_slice($params, 0, -2));
$totalPlans = $totalStmt->fetchColumn();
$totalPages = ceil($totalPlans / $limit);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_plan'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $difficulty = $_POST['difficulty'];
        $duration = (int)$_POST['duration'];
        $goal = $_POST['goal'];
        
        // Validation
        if (empty($name)) {
            $errors[] = "Plan name is required";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO training_plans 
                (name, description, difficulty, duration_weeks, goal, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            if ($stmt->execute([$name, $description, $difficulty, $duration, $goal])) {
                $plan_id = $pdo->lastInsertId();
                $success = true;
                $_POST = []; // Clear form
                header("Location: plans.php?id=$plan_id");
                exit;
            } else {
                $errors[] = "Failed to add training plan";
            }
        }
    }
}

$page_title = "Training Plans";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Training Plans</h1>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPlanModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Plan
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
        Training plan added successfully!
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Training Plans</h6>
            <form class="d-flex">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search plans..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <select class="form-control" name="difficulty">
                        <option value="">All Levels</option>
                        <option value="beginner" <?= $difficulty == 'beginner' ? 'selected' : '' ?>>Beginner</option>
                        <option value="intermediate" <?= $difficulty == 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                        <option value="advanced" <?= $difficulty == 'advanced' ? 'selected' : '' ?>>Advanced</option>
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
                            <th>Difficulty</th>
                            <th>Duration</th>
                            <th>Goal</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($plans as $plan): ?>
                        <tr>
                            <td><?= htmlspecialchars($plan['name']) ?></td>
                            <td>
                                <span class="badge <?= getDifficultyBadge($plan['difficulty']) ?>">
                                    <?= ucfirst($plan['difficulty']) ?>
                                </span>
                            </td>
                            <td><?= $plan['duration_weeks'] ?> weeks</td>
                            <td><?= ucfirst($plan['goal']) ?></td>
                            <td><?= date('M j, Y', strtotime($plan['created_at'])) ?></td>
                            <td>
                                <a href="plan-view.php?id=<?= $plan['id'] ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="plan-edit.php?id=<?= $plan['id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="plan-delete.php?id=<?= $plan['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
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
                        <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&difficulty=<?= $difficulty ?>">Previous</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&difficulty=<?= $difficulty ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&difficulty=<?= $difficulty ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Add Plan Modal -->
<div class="modal fade" id="addPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Training Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Plan Name *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="difficulty" class="form-label">Difficulty *</label>
                                <select class="form-control" id="difficulty" name="difficulty" required>
                                    <option value="beginner" <?= ($_POST['difficulty'] ?? '') == 'beginner' ? 'selected' : '' ?>>Beginner</option>
                                    <option value="intermediate" <?= ($_POST['difficulty'] ?? '') == 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                                    <option value="advanced" <?= ($_POST['difficulty'] ?? '') == 'advanced' ? 'selected' : '' ?>>Advanced</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="duration" class="form-label">Duration (weeks) *</label>
                                <input type="number" class="form-control" id="duration" name="duration" 
                                       value="<?= htmlspecialchars($_POST['duration'] ?? '4') ?>" min="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="goal" class="form-label">Goal *</label>
                        <select class="form-control" id="goal" name="goal" required>
                            <option value="weight_loss" <?= ($_POST['goal'] ?? '') == 'weight_loss' ? 'selected' : '' ?>>Weight Loss</option>
                            <option value="muscle_gain" <?= ($_POST['goal'] ?? '') == 'muscle_gain' ? 'selected' : '' ?>>Muscle Gain</option>
                            <option value="endurance" <?= ($_POST['goal'] ?? '') == 'endurance' ? 'selected' : '' ?>>Endurance</option>
                            <option value="general_fitness" <?= ($_POST['goal'] ?? '') == 'general_fitness' ? 'selected' : '' ?>>General Fitness</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_plan" class="btn btn-primary">Add Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>