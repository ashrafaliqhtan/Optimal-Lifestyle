// admin/modules/content/content-manager.php
<?php
require_once '../../../includes/auth-check.php';
require_once '../../../config/database.php';

// Content type (articles, pages, blogs, etc.)
$contentType = $_GET['type'] ?? 'articles';
$allowedTypes = ['articles', 'pages', 'blogs', 'news', 'resources'];
if (!in_array($contentType, $allowedTypes)) {
    $contentType = 'articles';
}

// Get content items
$query = "SELECT * FROM Content WHERE content_type = ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$contentType]);
$contentItems = $stmt->fetchAll();

// Get content statistics
$stats = $pdo->query("
    SELECT 
        content_type,
        COUNT(*) as total,
        SUM(views) as total_views,
        SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
        SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as drafts
    FROM Content 
    GROUP BY content_type
")->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);

include '../../../includes/header.php';
?>

<div class="container-fluid">
    <!-- Content Type Tabs -->
    <ul class="nav nav-tabs mb-4" id="contentTabs" role="tablist">
        <?php foreach ($allowedTypes as $type): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $contentType === $type ? 'active' : '' ?>" 
                    id="<?= $type ?>-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#<?= $type ?>" 
                    type="button" 
                    role="tab" 
                    aria-controls="<?= $type ?>" 
                    aria-selected="<?= $contentType === $type ? 'true' : 'false' ?>">
                <?= ucfirst($type) ?>
                <span class="badge bg-secondary ms-1"><?= $stats[$type]['total'] ?? 0 ?></span>
            </button>
        </li>
        <?php endforeach; ?>
    </ul>

    <!-- Content Tab Panes -->
    <div class="tab-content" id="contentTabsContent">
        <?php foreach ($allowedTypes as $type): ?>
        <div class="tab-pane fade <?= $contentType === $type ? 'show active' : '' ?>" 
             id="<?= $type ?>" 
             role="tabpanel" 
             aria-labelledby="<?= $type ?>-tab">
            
            <!-- Content Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-uppercase small">Total <?= $type ?></div>
                                    <div class="h4"><?= $stats[$type]['total'] ?? 0 ?></div>
                                </div>
                                <i class="fas fa-file-alt fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Other stat cards... -->
            </div>

            <!-- Content Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><?= ucfirst($type) ?> Management</h6>
                    <div>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i> Options
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="content-export.php?type=<?= $type ?>"><i class="fas fa-file-export me-1"></i> Export</a></li>
                                <li><a class="dropdown-item" href="content-import.php?type=<?= $type ?>"><i class="fas fa-file-import me-1"></i> Import</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-tags me-1"></i> Manage Categories</a></li>
                            </ul>
                        </div>
                        <a href="content-add.php?type=<?= $type ?>" class="btn btn-sm btn-primary ms-2">
                            <i class="fas fa-plus me-1"></i> Add New
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="<?= $type ?>Table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="40px"><input type="checkbox" class="form-check-input select-all"></th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Categories</th>
                                    <th>Status</th>
                                    <th>Views</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contentItems as $item): ?>
                                <tr>
                                    <td><input type="checkbox" class="form-check-input content-checkbox" value="<?= $item['content_id'] ?>"></td>
                                    <td>
                                        <a href="content-edit.php?id=<?= $item['content_id'] ?>" class="text-dark">
                                            <?= htmlspecialchars($item['title']) ?>
                                            <?php if ($item['featured']): ?>
                                                <span class="badge bg-warning ms-1">Featured</span>
                                            <?php endif; ?>
                                        </a>
                                    </td>
                                    <td><?= getAuthorName($item['author_id']) ?></td>
                                    <td><?= getCategoryBadges($item['content_id']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= getStatusBadge($item['status']) ?>">
                                            <?= ucfirst($item['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($item['views']) ?></td>
                                    <td><?= formatDate($item['created_at']) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="../<?= $type ?>/<?= $item['slug'] ?>" target="_blank" class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="content-edit.php?id=<?= $item['content_id'] ?>" class="btn btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-<?= $item['featured'] ? 'warning' : 'secondary' ?> toggle-featured" 
                                                    data-id="<?= $item['content_id'] ?>" 
                                                    title="<?= $item['featured'] ? 'Unfeature' : 'Feature' ?>">
                                                <i class="fas fa-star"></i>
                                            </button>
                                            <button class="btn btn-danger delete-content" data-id="<?= $item['content_id'] ?>" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Content Editor Modal -->
<div class="modal fade" id="contentEditorModal" tabindex="-1" aria-hidden="true">
    <!-- Modal content for quick editing -->
</div>

<!-- Bulk Actions Modal -->
<div class="modal fade" id="contentBulkModal" tabindex="-1" aria-hidden="true">
    <!-- Modal content for bulk actions -->
</div>

<script>
$(document).ready(function() {
    // Initialize DataTables for each content type
    <?php foreach ($allowedTypes as $type): ?>
    $('#<?= $type ?>Table').DataTable({
        dom: '<"top"lfB>rt<"bottom"ip>',
        buttons: [
            {
                extend: 'collection',
                text: '<i class="fas fa-bars"></i> Bulk Actions',
                buttons: [
                    {
                        text: '<i class="fas fa-check-circle"></i> Publish Selected',
                        action: function() {
                            bulkAction('publish');
                        }
                    },
                    {
                        text: '<i class="fas fa-times-circle"></i> Unpublish Selected',
                        action: function() {
                            bulkAction('unpublish');
                        }
                    },
                    {
                        text: '<i class="fas fa-trash"></i> Delete Selected',
                        action: function() {
                            bulkAction('delete');
                        }
                    }
                ]
            }
        ]
    });
    <?php endforeach; ?>

    // Toggle featured status
    $('.toggle-featured').click(function() {
        const contentId = $(this).data('id');
        const isFeatured = $(this).hasClass('btn-warning');
        
        $.post('content-feature.php', {
            id: contentId,
            featured: isFeatured ? 0 : 1
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json');
    });

    // Quick edit in modal
    $('.quick-edit').click(function() {
        const contentId = $(this).data('id');
        $('#contentEditorModal').modal('show');
        // Load content via AJAX...
    });
});
</script>

<?php include '../../../includes/footer.php'; ?>