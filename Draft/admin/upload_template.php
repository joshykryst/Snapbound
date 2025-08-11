<?php
<?php
$db = new SQLite3('../database/photobooth.db');

// Create templates table if not exists
$db->exec('CREATE TABLE IF NOT EXISTS templates (
    id TEXT PRIMARY KEY,
    name TEXT,
    image_data TEXT,
    timestamp INTEGER
)');

// Get POST data
$templateData = $_POST['templateData'];
$name = $_POST['name'];
$id = uniqid();

$stmt = $db->prepare('INSERT INTO templates (id, name, image_data, timestamp) VALUES (:id, :name, :image_data, :timestamp)');
$stmt->bindValue(':id', $id);
$stmt->bindValue(':name', $name);
$stmt->bindValue(':image_data', $templateData);
$stmt->bindValue(':timestamp', time());

if($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to save template']);
}
?>