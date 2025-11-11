<?php

// Direct database cleanup script
$host = '127.0.0.1';
$port = '3306';
$database = 'myd_bar_restaurantes';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🧹 LIMPEZA DIRETA DO BANCO DE DADOS\n";
    echo "===================================\n\n";
    
    // Get current state
    $stmt = $pdo->query("SELECT COUNT(*) FROM pedidos WHERE status = 'aberto'");
    $totalAbertos = $stmt->fetchColumn();
    echo "Pedidos abertos antes da limpeza: $totalAbertos\n";
    
    // Find mesas with multiple open orders
    $stmt = $pdo->query("
        SELECT mesa_id, COUNT(*) as count 
        FROM pedidos 
        WHERE status = 'aberto' 
        GROUP BY mesa_id 
        HAVING count > 1
    ");
    $mesasProblema = $stmt->fetchAll();
    
    echo "Mesas com múltiplos pedidos: " . count($mesasProblema) . "\n\n";
    
    foreach ($mesasProblema as $mesa) {
        echo "Mesa {$mesa['mesa_id']}: {$mesa['count']} pedidos\n";
        
        // Keep only the most recent order for each table
        $stmt = $pdo->prepare("
            SELECT id FROM pedidos 
            WHERE mesa_id = ? AND status = 'aberto' 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$mesa['mesa_id']]);
        $keepOrder = $stmt->fetchColumn();
        
        // Delete older orders
        $stmt = $pdo->prepare("
            DELETE FROM pedidos 
            WHERE mesa_id = ? AND status = 'aberto' AND id != ?
        ");
        $stmt->execute([$mesa['mesa_id'], $keepOrder]);
        $deleted = $stmt->rowCount();
        
        echo "  ✅ Removidos $deleted pedidos duplicados (mantido ID: $keepOrder)\n";
    }
    
    // Final state
    $stmt = $pdo->query("SELECT COUNT(*) FROM pedidos WHERE status = 'aberto'");
    $finalAbertos = $stmt->fetchColumn();
    echo "\n📊 RESULTADO:\n";
    echo "Pedidos abertos após limpeza: $finalAbertos\n";
    echo "Pedidos removidos: " . ($totalAbertos - $finalAbertos) . "\n";
    
    echo "\n✅ Limpeza concluída com sucesso!\n";
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>
