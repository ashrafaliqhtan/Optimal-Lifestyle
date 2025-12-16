<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

$page_title = "Meal Plans";
include 'includes/header.php';
include 'includes/sidebar.php';

// Get all meal plans
$meal_plans = [];
try {
    $stmt = $pdo->prepare("
        SELECT mp.*, u.name as creator_name, 
               COUNT(mpd.id) as meal_count,
               (SELECT COUNT(*) FROM user_meal_plans WHERE meal_plan_id = mp.id) as user_count
        FROM meal_plans mp
        LEFT JOIN usersmanage u ON mp.created_by = u.id
        LEFT JOIN meal_plan_days mpd ON mp.id = mpd.meal_plan_id
        GROUP BY mp.id
        ORDER BY mp.created_at DESC
    ");
    $stmt->execute();
    $meal_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching meal plans: " . $e->getMessage();
}

// Get popular foods for suggestions
$popular_foods = [];
try {
    $stmt = $pdo->prepare("
        SELECT food_name, COUNT(*) as usage_count
        FROM CalorieCalculator
        GROUP BY food_name
        ORDER BY usage_count DESC
        LIMIT 10
    ");
    $stmt->execute();
    $popular_foods = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching popular foods: " . $e->getMessage());
}
?>

<div id="content">
    <?php include 'includes/topbar.php'; ?>

    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Meal Plans Management</h1>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#newMealPlanModal">
                <i class="fas fa-plus fa-sm text-white-50"></i> New Meal Plan
            </button>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">All Meal Plans</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                        <a class="dropdown-item" href="#" id="exportMealPlans"><i class="fas fa-file-export mr-2"></i>Export</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" id="printMealPlans"><i class="fas fa-print mr-2"></i>Print</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="mealPlansTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Meals</th>
                                <th>Users</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($meal_plans as $plan): ?>
                            <tr>
                                <td><?= htmlspecialchars($plan['name']) ?></td>
                                <td><?= htmlspecialchars(truncateString($plan['description'], 50)) ?></td>
                                <td class="text-center"><?= $plan['meal_count'] ?></td>
                                <td class="text-center"><?= $plan['user_count'] ?></td>
                                <td><?= htmlspecialchars($plan['creator_name']) ?></td>
                                <td><?= formatDate($plan['created_at']) ?></td>
                                <td>
                                    <a href="meal-plan.php?id=<?= $plan['id'] ?>" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="meal-plan-edit.php?id=<?= $plan['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-meal-plan" data-id="<?= $plan['id'] ?>" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Meal Plan Modal -->
<div class="modal fade" id="newMealPlanModal" tabindex="-1" role="dialog" aria-labelledby="newMealPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newMealPlanModalLabel">Create New Meal Plan</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createMealPlanForm" action="includes/create-meal-plan.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="planName">Plan Name</label>
                        <input type="text" class="form-control" id="planName" name="planName" required>
                    </div>
                    <div class="form-group">
                        <label for="planDescription">Description</label>
                        <textarea class="form-control" id="planDescription" name="planDescription" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Days Configuration</label>
                        <div id="daysContainer">
                            <!-- Days will be added here dynamically -->
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary mt-2" id="addDayBtn">
                            <i class="fas fa-plus"></i> Add Day
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Food Search Modal -->
<div class="modal fade" id="foodSearchModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Food</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" class="form-control" id="foodSearchInput" placeholder="Search food...">
                </div>
                <div id="foodSearchResults">
                    <div class="list-group">
                        <?php foreach ($popular_foods as $food): ?>
                        <a href="#" class="list-group-item list-group-item-action food-item" 
                           data-name="<?= htmlspecialchars($food['food_name']) ?>"
                           data-calories="<?= $food['calorie_amount'] ?? 0 ?>">
                            <?= htmlspecialchars($food['food_name']) ?>
                            <span class="float-right text-muted"><?= $food['usage_count'] ?> uses</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#mealPlansTable').DataTable({
        responsive: true,
        columnDefs: [
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: -1 }
        ]
    });

    // Day counter
    let dayCounter = 1;

    // Add day button click handler
    $('#addDayBtn').click(function() {
        const dayId = 'day_' + dayCounter++;
        const dayHtml = `
            <div class="card mb-3 day-card" id="${dayId}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0">Day ${dayCounter - 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-day" data-day="${dayId}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Day Name (optional)</label>
                        <input type="text" class="form-control" name="dayNames[]" placeholder="e.g. 'Rest Day', 'High Protein Day'">
                    </div>
                    <div class="meals-container">
                        <h6>Meals</h6>
                        <div class="meals-list">
                            <!-- Meals will be added here -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary add-meal-btn">
                            <i class="fas fa-plus"></i> Add Meal
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#daysContainer').append(dayHtml);
    });

    // Add meal button click handler (delegated)
    $('#daysContainer').on('click', '.add-meal-btn', function() {
        const mealsList = $(this).siblings('.meals-list');
        const mealHtml = `
            <div class="card mb-2 meal-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Meal Name</label>
                                <div class="input-group">
                                    <input type="text" class="form-control meal-name" name="mealNames[]" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary food-search-btn" type="button">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Time</label>
                                <select class="form-control meal-time" name="mealTimes[]">
                                    <option value="breakfast">Breakfast</option>
                                    <option value="lunch">Lunch</option>
                                    <option value="dinner">Dinner</option>
                                    <option value="snack">Snack</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Calories</label>
                                <input type="number" class="form-control meal-calories" name="mealCalories[]" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-danger remove-meal">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Notes (optional)</label>
                        <textarea class="form-control meal-notes" name="mealNotes[]" rows="2"></textarea>
                    </div>
                </div>
            </div>
        `;
        mealsList.append(mealHtml);
    });

    // Remove day button click handler
    $('#daysContainer').on('click', '.remove-day', function() {
        const dayId = $(this).data('day');
        $('#' + dayId).remove();
        // Re-number days
        $('.day-card').each(function(index) {
            $(this).find('h6').text('Day ' + (index + 1));
        });
    });

    // Remove meal button click handler
    $('#daysContainer').on('click', '.remove-meal', function() {
        $(this).closest('.meal-card').remove();
    });

    // Food search button click handler
    $('#daysContainer').on('click', '.food-search-btn', function() {
        const mealCard = $(this).closest('.meal-card');
        currentMealCard = mealCard;
        $('#foodSearchModal').modal('show');
    });

    // Food item click handler
    $('#foodSearchResults').on('click', '.food-item', function(e) {
        e.preventDefault();
        const foodName = $(this).data('name');
        const calories = $(this).data('calories');
        
        if (currentMealCard) {
            currentMealCard.find('.meal-name').val(foodName);
            currentMealCard.find('.meal-calories').val(calories);
        }
        
        $('#foodSearchModal').modal('hide');
    });

    // Food search input handler
    $('#foodSearchInput').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        if (searchTerm.length > 2) {
            // In a real app, you would make an AJAX call here
            $('.food-item').each(function() {
                const foodName = $(this).data('name').toLowerCase();
                if (foodName.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $('.food-item').show();
        }
    });

    // Delete meal plan handler
    $('.delete-meal-plan').click(function() {
        const planId = $(this).data('id');
        if (confirm('Are you sure you want to delete this meal plan? This action cannot be undone.')) {
            window.location.href = 'includes/delete-meal-plan.php?id=' + planId;
        }
    });

    // Export meal plans handler
    $('#exportMealPlans').click(function(e) {
        e.preventDefault();
        window.location.href = 'includes/export-meal-plans.php';
    });

    // Print meal plans handler
    $('#printMealPlans').click(function(e) {
        e.preventDefault();
        window.print();
    });
});
</script>