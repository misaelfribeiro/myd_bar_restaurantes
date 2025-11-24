<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=myd_bar_restaurantes', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== USUÁRIOS E ROLES ===\n";
    
    $stmt = $pdo->query("SELECT id, nome, email, role FROM usuarios ORDER BY role, nome");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($usuarios as $user) {
        echo "ID: {$user['id']} | Nome: {$user['nome']} | Email: {$user['email']} | Role: {$user['role']}\n";
    }
    
    echo "\n=== DADOS PARA LOGIN ===\n";
    $admin = null;
    foreach ($usuarios as $user) {
        if (in_array($user['role'], ['admin', 'gerente'])) {
            echo "✅ {$user['role']}: {$user['email']} (senha: 123456)\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>