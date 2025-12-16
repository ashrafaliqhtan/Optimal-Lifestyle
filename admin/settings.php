<?php
require_once '../includes/auth-check.php';
require_once '../config/database.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Update site settings
        $stmt = $pdo->prepare("UPDATE SiteSettings SET setting_value = ? WHERE setting_name = ?");
        
        foreach ($_POST['settings'] as $name => $value) {
            $stmt->execute([$value, $name]);
        }
        
        // Handle logo upload
        if (!empty($_FILES['site_logo']['name'])) {
            $target_dir = "../assets/images/";
            $target_file = $target_dir . basename($_FILES["site_logo"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            // Check if image file is a actual image
            $check = getimagesize($_FILES["site_logo"]["tmp_name"]);
            if($check === false) {
                throw new Exception("File is not an image.");
            }
            
            // Check file size
            if ($_FILES["site_logo"]["size"] > 500000) {
                throw new Exception("Sorry, your file is too large.");
            }
            
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                throw new Exception("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
            }
            
            // Upload file
            if (move_uploaded_file($_FILES["site_logo"]["tmp_name"], $target_file)) {
                $stmt = $pdo->prepare("UPDATE SiteSettings SET setting_value = ? WHERE setting_name = 'site_logo'");
                $stmt->execute([basename($_FILES["site_logo"]["name"])]);
            } else {
                throw new Exception("Sorry, there was an error uploading your file.");
            }
        }
        
        // Commit transaction
        $pdo->commit();
        $success_message = "Settings updated successfully!";
        
        // Log activity
        $activity_stmt = $pdo->prepare("INSERT INTO AdminActivityLog (admin_id, admin_name, activity_type, activity_details) VALUES (?, ?, ?, ?)");
        $activity_stmt->execute([
            $_SESSION['admin_id'],
            $_SESSION['admin_name'],
            'Settings Update',
            'Updated system settings'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "Error updating settings: " . $e->getMessage();
    }
}

// Get current settings
$settings_stmt = $pdo->query("SELECT * FROM SiteSettings");
$settings = $settings_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

include '../includes/header.php';
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">System Settings</h6>
    </div>
    
    <div class="card-body">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="mb-3">General Settings</h5>
                    
                    <div class="mb-3">
                        <label for="site_name" class="form-label">Site Name</label>
                        <input type="text" class="form-control" id="site_name" name="settings[site_name]" value="<?= htmlspecialchars($settings['site_name']) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="site_email" class="form-label">Admin Email</label>
                        <input type="email" class="form-control" id="site_email" name="settings[site_email]" value="<?= htmlspecialchars($settings['site_email']) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="site_description" class="form-label">Site Description</label>
                        <textarea class="form-control" id="site_description" name="settings[site_description]" rows="3"><?= htmlspecialchars($settings['site_description']) ?></textarea>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5 class="mb-3">Logo & Appearance</h5>
                    
                    <div class="mb-3">
                        <label for="site_logo" class="form-label">Site Logo</label>
                        <input type="file" class="form-control" id="site_logo" name="site_logo" accept="image/*">
                        <?php if (!empty($settings['site_logo'])): ?>
                            <div class="mt-2">
                                <img src="../assets/images/<?= htmlspecialchars($settings['site_logo']) ?>" alt="Current Logo" style="max-height: 100px;" class="img-thumbnail">
                                <p class="small text-muted mt-1">Current logo: <?= htmlspecialchars($settings['site_logo']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="theme_color" class="form-label">Theme Color</label>
                        <input type="color" class="form-control form-control-color" id="theme_color" name="settings[theme_color]" value="<?= htmlspecialchars($settings['theme_color']) ?>">
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="mb-3">User Registration</h5>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="allow_registration" name="settings[allow_registration]" value="1" <?= $settings['allow_registration'] == '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="allow_registration">Allow new user registration</label>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="email_verification" name="settings[email_verification]" value="1" <?= $settings['email_verification'] == '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="email_verification">Require email verification</label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5 class="mb-3">Maintenance Mode</h5>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="maintenance_mode" name="settings[maintenance_mode]" value="1" <?= $settings['maintenance_mode'] == '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="maintenance_mode">Enable maintenance mode</label>
                    </div>
                    
                    <div class="mb-3">
                        <label for="maintenance_message" class="form-label">Maintenance Message</label>
                        <textarea class="form-control" id="maintenance_message" name="settings[maintenance_message]" rows="3"><?= htmlspecialchars($settings['maintenance_message']) ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>