<?php
$host = 'localhost';
$dbname = 'snapbound';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode([
        'success' => false,
        'error' => "Connection failed: " . $e->getMessage()
    ]));
}
?>