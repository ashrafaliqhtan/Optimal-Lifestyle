<?php
// upload.php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

header('Content-Type: application/json');
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// إعدادات التحمل
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$maxFileSize = 5 * 1024 * 1024; // 5MB
$uploadDir = 'uploads/articles/';

// إنشاء مجلد التحميل إذا لم يكن موجوداً
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$response = [];

try {
    if (!isset($_FILES['images'])) {
        throw new Exception('No files uploaded');
    }

    $uploadedFiles = [];
    
    // معالجة كل الملفات المرفوعة
    foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
        $fileName = $_FILES['images']['name'][$index];
        $fileSize = $_FILES['images']['size'][$index];
        $fileError = $_FILES['images']['error'][$index];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // التحقق من الأخطاء
        if ($fileError !== UPLOAD_ERR_OK) {
            throw new Exception('Upload error: ' . $fileError);
        }

        // التحقق من حجم الملف
        if ($fileSize > $maxFileSize) {
            throw new Exception("File $fileName is too large (max 5MB)");
        }

        // التحقق من نوع الملف
        if (!in_array($fileExt, $allowedExtensions)) {
            throw new Exception("File $fileName has invalid extension");
        }

        // إنشاء اسم فريد للملف
        $newFileName = uniqid('img_', true) . '.' . $fileExt;
        $destination = $uploadDir . $newFileName;

        // نقل الملف إلى المجلد المخصص
        if (move_uploaded_file($tmpName, $destination)) {
            $uploadedFiles[] = [
                'originalName' => $fileName,
                'storedName' => $newFileName,
                'url' => $destination,
                'size' => $fileSize
            ];
        } else {
            throw new Exception("Failed to move uploaded file $fileName");
        }
    }

    $response = [
        'success' => true,
        'files' => $uploadedFiles
    ];

} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
?>