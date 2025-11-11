<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing PHP execution...\n";
echo "PHP Version: " . phpversion() . "\n";

// Test database connection
$host = '127.0.0.1';
$port = '3306';
$database = 'myd_bar_restaurantes';
$username = 'root';
$password = '';

try {
    echo "Attempting database connection...\n";
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    echo "✅ Database connected successfully!\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM pedidos WHERE status = 'aberto'");
    $count = $stmt->fetchColumn();
    echo "Pedidos abertos: $count\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>
