<?php
header('Content-Type: application/json');

$uploadDir = '../templates/';

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // List all templates
        $templates = [];
        $files = glob($uploadDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        
        foreach ($files as $file) {
            $templates[] = [
                'id' => basename($file),
                'url' => '../templates/' . basename($file)
            ];
        }
        
        echo json_encode($templates);
        break;

    case 'DELETE':
        // Delete template
        $id = basename($_SERVER['PATH_INFO']);
        $file = $uploadDir . $id;
        
        if (file_exists($file) && unlink($file)) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'File not found'
            ]);
        }
        break;
}
?>
