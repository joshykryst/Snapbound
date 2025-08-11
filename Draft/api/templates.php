<?php
<?php
header('Content-Type: application/json');

$db = new SQLite3('../database/photobooth.db');

// Create templates table if it doesn't exist
$db->exec('CREATE TABLE IF NOT EXISTS templates (
    id TEXT PRIMARY KEY,
    name TEXT,
    url TEXT,
    timestamp INTEGER
)');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $results = $db->query('SELECT * FROM templates ORDER BY timestamp DESC');
        $templates = [];
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $templates[] = $row;
        }
        echo json_encode($templates);
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare('INSERT INTO templates (id, name, url, timestamp) VALUES (:id, :name, :url, :timestamp)');
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':url', $data['url']);
        $stmt->bindValue(':timestamp', $data['timestamp']);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(['message' => 'Template saved successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save template']);
        }
        break;
}
?>