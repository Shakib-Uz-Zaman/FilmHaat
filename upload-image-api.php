<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST requests allowed']);
    exit;
}

$file = $_FILES['file'] ?? $_FILES['image'] ?? null;
$imageType = $_POST['type'] ?? $_POST['image_type'] ?? null;

if (!$file || !$imageType) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'File upload error']);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if ($mimeType !== 'image/webp') {
    echo json_encode(['success' => false, 'message' => 'Only WebP format is accepted']);
    exit;
}

$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($fileExtension !== 'webp') {
    echo json_encode(['success' => false, 'message' => 'Only .webp file is accepted']);
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
    $aspectRatio = $width / $height;
    $expectedRatio = 1 / 1;
    $tolerance = 0.01;
    
    if (abs($aspectRatio - $expectedRatio) > $tolerance) {
        echo json_encode(['success' => false, 'message' => 'Logo must be 1:1 aspect ratio']);
        exit;
    }
    
    $targetFile = 'attached_image/logo-image.webp';
} elseif ($imageType === 'background') {
    $aspectRatio = $width / $height;
    $expectedRatio = 16 / 9;
    $tolerance = 0.01;
    
    if (abs($aspectRatio - $expectedRatio) > $tolerance) {
        echo json_encode(['success' => false, 'message' => 'Background image must be 16:9 aspect ratio']);
        exit;
    }
    
    $targetFile = 'attached_image/background-image.webp';
} elseif ($imageType === 'popup') {
    if (!is_dir('attached_image/popup')) {
        mkdir('attached_image/popup', 0755, true);
    }
    
    $targetFile = 'attached_image/popup/popup-image.webp';
} elseif ($imageType === 'pwa_logo') {
    $aspectRatio = $width / $height;
    $expectedRatio = 1 / 1;
    $tolerance = 0.01;
    
    if (abs($aspectRatio - $expectedRatio) > $tolerance) {
        echo json_encode(['success' => false, 'message' => 'PWA Logo must be 1:1 aspect ratio']);
        exit;
    }
    
    $targetFile = 'attached_image/pwa-logo.webp';
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
