<?php
header('Content-Type: application/json');
require_once '../config/db.php';

function handleUpload() {
    $target_dir = "../uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($_FILES["template"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    if (move_uploaded_file($_FILES["template"]["tmp_name"], $target_file)) {
        return $target_file;
    }
    return false;
}

switch($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if(isset($_FILES['template'])) {
            $image_path = handleUpload();
            if($image_path) {
                $stmt = $pdo->prepare("INSERT INTO templates (name, image_path, is_customizable, spacing) VALUES (?, ?, ?, ?)");
                $stmt->execute([$data['name'], $image_path, $data['is_customizable'], $data['spacing']]);
                echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            }
        }
        break;
        
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM templates ORDER BY created_at DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if($id) {
            $stmt = $pdo->prepare("DELETE FROM templates WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        }
        break;
}
?>