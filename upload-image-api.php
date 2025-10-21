<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST requests allowed']);
    exit;
}

if (!isset($_FILES['image']) || !isset($_POST['image_type'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$imageType = $_POST['image_type'];
$file = $_FILES['image'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'File upload error']);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if ($mimeType !== 'image/webp') {
    echo json_encode(['success' => false, 'message' => 'শুধুমাত্র WebP format গ্রহণযোগ্য']);
    exit;
}

$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($fileExtension !== 'webp') {
    echo json_encode(['success' => false, 'message' => 'শুধুমাত্র .webp file গ্রহণযোগ্য']);
    exit;
}

$imageInfo = getimagesize($file['tmp_name']);
if ($imageInfo === false) {
    echo json_encode(['success' => false, 'message' => 'Invalid image file']);
    exit;
}

$width = $imageInfo[0];
$height = $imageInfo[1];
$fileSize = $file['size'];

if ($imageType === 'logo') {
    if ($width !== 150 || $height !== 150) {
        echo json_encode(['success' => false, 'message' => 'Logo size অবশ্যই 150x150 pixels হতে হবে']);
        exit;
    }
    
    $maxSize = 5 * 1024;
    if ($fileSize > $maxSize) {
        echo json_encode(['success' => false, 'message' => 'Logo file size সর্বোচ্চ 5KB হতে পারবে']);
        exit;
    }
    
    $targetFile = 'attached_image/logo-image.webp';
} elseif ($imageType === 'background') {
    $aspectRatio = $width / $height;
    $expectedRatio = 16 / 9;
    $tolerance = 0.01;
    
    if (abs($aspectRatio - $expectedRatio) > $tolerance) {
        echo json_encode(['success' => false, 'message' => 'Background image অবশ্যই 16:9 aspect ratio হতে হবে']);
        exit;
    }
    
    $maxSize = 550 * 1024;
    if ($fileSize > $maxSize) {
        echo json_encode(['success' => false, 'message' => 'Background file size সর্বোচ্চ 550KB হতে পারবে']);
        exit;
    }
    
    $targetFile = 'attached_image/background-image.webp';
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid image type']);
    exit;
}

if (!is_dir('attached_image')) {
    mkdir('attached_image', 0755, true);
}

if (file_exists($targetFile)) {
    $backupFile = str_replace('.webp', '.backup.webp', $targetFile);
    copy($targetFile, $backupFile);
}

if (move_uploaded_file($file['tmp_name'], $targetFile)) {
    echo json_encode([
        'success' => true,
        'message' => 'Image uploaded successfully',
        'path' => $targetFile,
        'size' => $fileSize,
        'dimensions' => "{$width}x{$height}"
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save image']);
}
?>
