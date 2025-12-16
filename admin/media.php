<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$limit = 24;
$offset = ($page - 1) * $limit;

// Build query
$query = "SELECT * FROM media WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR alt_text LIKE ?)";
    $params = array_fill(0, 2, "%$search%");
}

if (!empty($type)) {
    $query .= " AND type = ?";
    $params[] = $type;
}

$query .= " ORDER BY uploaded_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$media = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$countQuery = str_replace('*', 'COUNT(*) as total', explode('LIMIT', $query)[0]);
$totalStmt = $pdo->prepare($countQuery);
$totalStmt->execute(array_slice($params, 0, -2));
$totalMedia = $totalStmt->fetchColumn();
$totalPages = ceil($totalMedia / $limit);

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media_file'])) {
    $uploadDir = '../uploads/media/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'application/pdf'];
    $maxSize = 10 * 1024 * 1024; // 10MB
    
    $file = $_FILES['media_file'];
    $fileName = basename($file['name']);
    $fileType = $file['type'];
    $fileSize = $file['size'];
    $fileTmp = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    $newFileName = uniqid() . '.' . $fileExt;
    $uploadPath = $uploadDir . $newFileName;
    
    // Validation
    if (!in_array($fileType, $allowedTypes)) {
        $errors[] = "File type not allowed";
    } elseif ($fileSize > $maxSize) {
        $errors[] = "File is too large (max 10MB)";
    } elseif (move_uploaded_file($fileTmp, $uploadPath)) {
        // Determine media type
        $mediaType = strpos($fileType, 'image/') === 0 ? 'image' : 
                    (strpos($fileType, 'video/') === 0 ? 'video' : 'document');
        
        // Insert into database
        $stmt = $pdo->prepare("
            INSERT INTO media (name, path, type, alt_text, uploaded_by, uploaded_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        if ($stmt->execute([$fileName, $newFileName, $mediaType, $_POST['alt_text'], $_SESSION['user_id']])) {
            $success = true;
            // Refresh media list
            header("Location: media.php");
            exit;
        } else {
            $errors[] = "Failed to save media to database";
            // Delete uploaded file
            unlink($uploadPath);
        }
    } else {
        $errors[] = "Failed to upload file";
    }
}

$page_title = "Media Library";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Media Library</h1>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-upload fa-sm text-white-50"></i> Upload Media
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
        Media uploaded successfully!
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Media</h6>
            <form class="d-flex">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search media..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <select class="form-control" name="type">
                        <option value="">All Types</option>
                        <option value="image" <?= $type == 'image' ? 'selected' : '' ?>>Images</option>
                        <option value="video" <?= $type == 'video' ? 'selected' : '' ?>>Videos</option>
                        <option value="document" <?= $type == 'document' ? 'selected' : '' ?>>Documents</option>
                    </select>
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <?php if (empty($media)): ?>
                <p class="text-center text-muted">No media found</p>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($media as $item): ?>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <?php if ($item['type'] == 'image'): ?>
                                <img src="../uploads/media/<?= htmlspecialchars($item['path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['alt_text']) ?>">
                            <?php elseif ($item['type'] == 'video'): ?>
                                <div class="card-img-top bg-dark text-white d-flex align-items-center justify-content-center" style="height: 120px;">
                                    <i class="fas fa-video fa-3x"></i>
                                </div>
                            <?php else: ?>
                                <div class="card-img-top bg-light text-dark d-flex align-items-center justify-content-center" style="height: 120px;">
                                    <i class="fas fa-file fa-3x"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h6 class="card-title"><?= htmlspecialchars($item['name']) ?></h6>
                                <p class="card-text small text-muted">
                                    <?= date('M j, Y', strtotime($item['uploaded_at'])) ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white">
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                            data-bs-target="#mediaInfoModal"
                                            data-id="<?= $item['id'] ?>"
                                            data-name="<?= htmlspecialchars($item['name']) ?>"
                                            data-type="<?= $item['type'] ?>"
                                            data-path="<?= htmlspecialchars($item['path']) ?>"
                                            data-alt="<?= htmlspecialchars($item['alt_text']) ?>"
                                            data-date="<?= date('M j, Y H:i', strtotime($item['uploaded_at'])) ?>">
                                        <i class="fas fa-info"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" 
                                            data-bs-target="#deleteMediaModal"
                                            data-id="<?= $item['id'] ?>"
                                            data-name="<?= htmlspecialchars($item['name']) ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
                            <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&type=<?= $type ?>">Previous</a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&type=<?= $type ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&type=<?= $type ?>">Next</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="media_file" class="form-label">Select File *</label>
                        <input class="form-control" type="file" id="media_file" name="media_file" required>
                        <small class="text-muted">Allowed: JPG, PNG, GIF, MP4, PDF (max 10MB)</small>
                    </div>
                    <div class="form-group">
                        <label for="alt_text" class="form-label">Alt Text/Description</label>
                        <input type="text" class="form-control" id="alt_text" name="alt_text">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Media Info Modal -->
<div class="modal fade" id="mediaInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Media Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 text-center">
                    <div id="mediaPreviewContainer">
                        <!-- Preview will be inserted here by JavaScript -->
                    </div>
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <td id="infoName"></td>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <td id="infoType"></td>
                    </tr>
                    <tr>
                        <th>Uploaded</th>
                        <td id="infoDate"></td>
                    </tr>
                    <tr>
                        <th>Alt Text</th>
                        <td id="infoAlt"></td>
                    </tr>
                </table>
                <div class="form-group">
                    <label for="embedCode" class="form-label">Embed Code</label>
                    <input type="text" class="form-control" id="embedCode" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="copyEmbedCode()">
                    <i class="fas fa-copy"></i> Copy Embed Code
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Media Modal -->
<div class="modal fade" id="deleteMediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="media-delete.php">
                <input type="hidden" name="id" id="deleteMediaId">
                <div class="modal-body">
                    <p>Are you sure you want to delete "<span id="deleteMediaName"></span>"?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Handle info modal
document.getElementById('mediaInfoModal').addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const name = button.getAttribute('data-name');
    const type = button.getAttribute('data-type');
    const path = button.getAttribute('data-path');
    const alt = button.getAttribute('data-alt');
    const date = button.getAttribute('data-date');
    
    // Set text info
    document.getElementById('infoName').textContent = name;
    document.getElementById('infoType').textContent = type.charAt(0).toUpperCase() + type.slice(1);
    document.getElementById('infoDate').textContent = date;
    document.getElementById('infoAlt').textContent = alt || 'None';
    
    // Set preview
    const previewContainer = document.getElementById('mediaPreviewContainer');
    previewContainer.innerHTML = '';
    
    if (type === 'image') {
        const img = document.createElement('img');
        img.src = '../uploads/media/' + path;
        img.className = 'img-fluid';
        img.alt = alt;
        previewContainer.appendChild(img);
    } else if (type === 'video') {
        const icon = document.createElement('i');
        icon.className = 'fas fa-video fa-5x text-secondary';
        previewContainer.appendChild(icon);
    } else {
        const icon = document.createElement('i');
        icon.className = 'fas fa-file fa-5x text-secondary';
        previewContainer.appendChild(icon);
    }
    
    // Set embed code
    const embedCode = type === 'image' ? 
        `&lt;img src="/uploads/media/${path}" alt="${alt || ''}" class="img-fluid"&gt;` :
        `&lt;a href="/uploads/media/${path}"&gt;${name}&lt;/a&gt;`;
    document.getElementById('embedCode').value = embedCode;
});

// Handle delete modal
document.getElementById('deleteMediaModal').addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    document.getElementById('deleteMediaId').value = button.getAttribute('data-id');
    document.getElementById('deleteMediaName').textContent = button.getAttribute('data-name');
});

// Copy embed code to clipboard
function copyEmbedCode() {
    const embedCode = document.getElementById('embedCode');
    embedCode.select();
    document.execCommand('copy');
    
    // Show feedback
    const originalText = embedCode.value;
    embedCode.value = 'Copied to clipboard!';
    setTimeout(() => {
        embedCode.value = originalText;
    }, 2000);
}
</script>

<?php include 'includes/footer.php'; ?>