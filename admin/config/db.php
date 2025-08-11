<?php
$host = 'localhost';
$dbname = 'snapbound';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE templates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        is_customizable BOOLEAN DEFAULT FALSE,
        spacing DECIMAL(4,2) DEFAULT 0.25,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>