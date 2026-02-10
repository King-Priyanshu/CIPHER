<?php
try {
    $dsn = 'mysql:host=mysql;dbname=cipher;charset=utf8mb4';
    $user = 'root';
    $pass = 'secret';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected successfully to MySQL host 'mysql'.\n";
    
    // Try a simple query
    $stmt = $pdo->query("SELECT 1");
    $result = $stmt->fetch();
    echo "Query result: " . print_r($result, true) . "\n";
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    echo "Host: mysql\n";
    echo "User: root\n";
}
