<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $db = new SQLite3('database/photobooth.db');
    
    // Create templates table if it doesn't exist
    $db->exec('CREATE TABLE IF NOT EXISTS templates (
        id TEXT PRIMARY KEY,
        name TEXT,
        image_data TEXT,
        timestamp INTEGER
    )');
    
    $results = $db->query('SELECT * FROM templates ORDER BY timestamp DESC');
    
    $templates = [];
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $templates[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'templates' => $templates
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>