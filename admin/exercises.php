<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

$search = $_GET['search'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query
$query = "SELECT * FROM exercises_default WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (exercise_type LIKE ? OR description LIKE ?)";
    $params = array_fill(0, 2, "%$search%");
}

$query .= " ORDER BY exercise_type LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$countQuery = str_replace('*', 'COUNT(*) as total', explode('LIMIT', $query)[0]);
$totalStmt = $pdo->prepare($countQuery);
$totalStmt->execute(array_slice($params, 0, -2));
$totalExercises = $totalStmt->fetchColumn();
$totalPages = ceil($totalExercises / $limit);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_exercise'])) {
        $exercise_type = trim($_POST['exercise_type']);
        $kcal_hour = (int)$_POST['kcal_hour'];
        $description = trim($_POST['description']);
        $img_name = $_FILES['image']['name'];
        $img_tmp = $_FILES['image']['tmp_name'];
        
        // Validation
        if (empty($exercise_type)) {
            $errors[] = "Exercise type is required";
        } else {
            // Handle image upload
            $img_data = null;
            if (!empty($img_name)) {
                $img_info = getimagesize($img_tmp);
                if ($img_info === false) {
                    $errors[] = "File is not an image";
                } else {
                    $img_data = file_get_contents($img_tmp);
                }
            }
            
            if (empty($errors)) {
                $stmt = $pdo->prepare("
                    INSERT INTO exercises_default 
                    (exercise_type, kcal_hour, description, img_data, img_name)
                    VALUES (?, ?, ?, ?, ?)
                ");
                
                if ($stmt->execute([$exercise_type, $kcal_hour, $description, $img_data, $img_name])) {
                    $success = true;
                    $_POST = []; // Clear form
                    header("Location: exercises.php");
                    exit;
                } else {
                    $errors[] = "Failed to add exercise";
                }
            }
        }
    }
}

$page_title = "Exercise Database";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Exercise Database</h1>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addExerciseModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Exercise
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
        Exercise added successfully!
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Exercises</h6>
            <form class="d-flex">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search exercises..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($exercises as $exercise): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($exercise['img_data'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($exercise['img_data']) ?>" 
                                 class="card-img-top" alt="<?= htmlspecialchars($exercise['exercise_type']) ?>"
                                 style="height: 180px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                <i class="fas fa-running fa-4x text-secondary"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($exercise['exercise_type']) ?></h5>
                            <p class="card-text small text-muted">
                                <i class="fas fa-fire"></i> <?= $exercise['kcal_hour'] ?> kcal/hour
                            </p>
                            <?php if (!empty($exercise['description'])): ?>
                                <p class="card-text"><?= htmlspecialchars(truncateString($exercise['description'], 100)) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                        data-bs-target="#viewExerciseModal"
                                        data-id="<?= $exercise['exercise_id'] ?>"
                                        data-type="<?= htmlspecialchars($exercise['exercise_type']) ?>"
                                        data-kcal="<?= $exercise['kcal_hour'] ?>"
                                        data-desc="<?= htmlspecialchars($exercise['description']) ?>"
                                        data-img="<?= !empty($exercise['img_data']) ? 'data:image/jpeg;base64,' . base64_encode($exercise['img_data']) : '' ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="exercise-edit.php?id=<?= $exercise['exercise_id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="exercise-delete.php?id=<?= $exercise['exercise_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
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

<!-- Add Exercise Modal -->
<div class="modal fade" id="addExerciseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Exercise</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="exercise_type" class="form-label">Exercise Type *</label>
                        <input type="text" class="form-control" id="exercise_type" name="exercise_type" 
                               value="<?= htmlspecialchars($_POST['exercise_type'] ?? '') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="kcal_hour" class="form-label">Calories per Hour *</label>
                        <input type="number" class="form-control" id="kcal_hour" name="kcal_hour" 
                               value="<?= htmlspecialchars($_POST['kcal_hour'] ?? '300') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image" class="form-label">Exercise Image</label>
                        <input class="form-control" type="file" id="image" name="image">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_exercise" class="btn btn-primary">Add Exercise</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Exercise Modal -->
<div class="modal fade" id="viewExerciseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exercise Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="exerciseImage" src="" class="img-fluid rounded" style="max-height: 200px;">
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th>Exercise Type</th>
                        <td id="exerciseType"></td>
                    </tr>
                    <tr>
                        <th>Calories per Hour</th>
                        <td id="exerciseKcal"></td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td id="exerciseDesc"></td>
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
document.getElementById('viewExerciseModal').addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    document.getElementById('exerciseType').textContent = button.getAttribute('data-type');
    document.getElementById('exerciseKcal').textContent = button.getAttribute('data-kcal') + ' kcal/hour';
    document.getElementById('exerciseDesc').textContent = button.getAttribute('data-desc') || 'No description';
    
    const imgSrc = button.getAttribute('data-img');
    const imgElement = document.getElementById('exerciseImage');
    if (imgSrc) {
        imgElement.src = imgSrc;
        imgElement.style.display = 'block';
    } else {
        imgElement.style.display = 'none';
    }
});
</script>

<?php include 'includes/footer.php'; ?>