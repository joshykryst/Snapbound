<?php
header('Content-Type: application/json');

// Create templates directory if it doesn't exist
$uploadDir = '../templates/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

try {
    if (!isset($_FILES['template'])) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['template'];
    $fileName = time() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;

    // Verify image dimensions
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        throw new Exception('Invalid image file');
    }

    if ($imageInfo[0] !== 600 || $imageInfo[1] !== 1800) {
        throw new Exception('Image must be exactly 600x1800 pixels');
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo json_encode([
            'success' => true,
            'file' => $fileName,
            'url' => '../templates/' . $fileName
        ]);
    } else {
        throw new Exception('Failed to save file');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
