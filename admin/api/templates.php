<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/db.php';

function handleUpload() {
    $target_dir = "../uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file = $_FILES["template"];
    $fileName = time() . '_' . basename($file["name"]);
    $target_file = $target_dir . $fileName;
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return '/admin/uploads/' . $fileName;
    }
    return false;
}

try {
    switch($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            if (!isset($_FILES['template'])) {
                throw new Exception('No file uploaded');
            }
            
            $image_path = handleUpload();
            if (!$image_path) {
                throw new Exception('Failed to save file');
            }
            
            $stmt = $pdo->prepare("INSERT INTO templates (name, image_path, is_customizable, spacing) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_POST['name'],
                $image_path,
                $_POST['is_customizable'] === 'true' ? 1 : 0,
                floatval($_POST['spacing'])
            ]);
            
            echo json_encode([
                'success' => true,
                'id' => $pdo->lastInsertId(),
                'message' => 'Template saved successfully'
            ]);
            break;
            
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM templates ORDER BY created_at DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;
            
        case 'DELETE':
            if (!isset($_GET['id'])) {
                throw new Exception('No template ID provided');
            }
            
            $stmt = $pdo->prepare("SELECT image_path FROM templates WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $template = $stmt->fetch();
            
            if ($template) {
                $file_path = $_SERVER['DOCUMENT_ROOT'] . $template['image_path'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            
            $stmt = $pdo->prepare("DELETE FROM templates WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Template deleted successfully'
            ]);
            break;
            
        default:
            throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>