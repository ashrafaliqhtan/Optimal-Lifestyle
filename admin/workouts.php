<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

$search = $_GET['search'] ?? '';
$exercise = $_GET['exercise'] ?? '';
$user = $_GET['user'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query
$query = "SELECT e.*, u.name as user_name, ed.exercise_type
          FROM exercises e
          JOIN usersmanage u ON e.user_id = u.id
          JOIN exercises_default ed ON e.exercise_id = ed.exercise_id
          WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (ed.exercise_type LIKE ? OR e.place LIKE ? OR e.people LIKE ?)";
    $params = array_fill(0, 3, "%$search%");
}

if (!empty($exercise)) {
    $query .= " AND e.exercise_id = ?";
    $params[] = $exercise;
}

if (!empty($user)) {
    $query .= " AND e.user_id = ?";
    $params[] = $user;
}

if (!empty($date_from)) {
    $query .= " AND e.date_done >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $query .= " AND e.date_done <= ?";
    $params[] = $date_to;
}

$query .= " ORDER BY e.date_done DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$workouts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$countQuery = str_replace('e.*, u.name as user_name, ed.exercise_type', 'COUNT(*) as total', explode('LIMIT', $query)[0]);
$totalStmt = $pdo->prepare($countQuery);
$totalStmt->execute(array_slice($params, 0, -2));
$totalWorkouts = $totalStmt->fetchColumn();
$totalPages = ceil($totalWorkouts / $limit);

// Get filters data
$exercises = $pdo->query("SELECT * FROM exercises_default ORDER BY exercise_type")->fetchAll(PDO::FETCH_ASSOC);
$users = $pdo->query("SELECT id, name FROM usersmanage ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Workout Management";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Workout Management</h1>
        <a href="workout-add.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Workout
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Workouts</h6>
            <button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#filtersCollapse" aria-expanded="false" aria-controls="filtersCollapse">
                <i class="fas fa-filter"></i> Filters
            </button>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="collapse mb-4" id="filtersCollapse">
                <form class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="<?= htmlspecialchars($search) ?>" placeholder="Search workouts...">
                    </div>
                    <div class="col-md-2">
                        <label for="exercise" class="form-label">Exercise</label>
                        <select class="form-control" id="exercise" name="exercise">
                            <option value="">All Exercises</option>
                            <?php foreach ($exercises as $ex): ?>
                            <option value="<?= $ex['exercise_id'] ?>" <?= $exercise == $ex['exercise_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ex['exercise_type']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="user" class="form-label">User</label>
                        <select class="form-control" id="user" name="user">
                            <option value="">All Users</option>
                            <?php foreach ($users as $usr): ?>
                            <option value="<?= $usr['id'] ?>" <?= $user == $usr['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($usr['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="<?= htmlspecialchars($date_from) ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="<?= htmlspecialchars($date_to) ?>">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </div>
                </form>
            </div>
            
            <!-- Workouts Table -->
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Exercise</th>
                            <th>Date</th>
                            <th>Duration</th>
                            <th>Calories</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($workouts as $workout): ?>
                        <tr>
                            <td><?= htmlspecialchars($workout['user_name']) ?></td>
                            <td><?= htmlspecialchars($workout['exercise_type']) ?></td>
                            <td><?= date('M j, Y', strtotime($workout['date_done'])) ?></td>
                            <td><?= $workout['total_time'] ?></td>
                            <td><?= $workout['total_kcal'] ?></td>
                            <td>
                                <span class="badge <?= getWorkoutStatusBadge($workout['status']) ?>">
                                    <?= ucfirst($workout['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="workout-view.php?id=<?= $workout['id'] ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="workout-edit.php?id=<?= $workout['id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="workout-delete.php?id=<?= $workout['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
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
                        <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&exercise=<?= $exercise ?>&user=<?= $user ?>&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>">Previous</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&exercise=<?= $exercise ?>&user=<?= $user ?>&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&exercise=<?= $exercise ?>&user=<?= $user ?>&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>