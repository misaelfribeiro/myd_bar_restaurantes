<?php

echo "=== CRIANDO CENÁRIO DE TESTE PARA API DE PAGAMENTOS ===\n";
echo "Este script criará um pedido finalizado não pago para testar a API unificada\n\n";

// Configurações básicas
$mesaId = 1;
$usuarioId = 1;
$total = 95.75;

// Usando curl para criar dados via SQL direto
$sqlFile = 'criar_pedido_api_teste.sql';

$sql = "INSERT INTO pedidos (mesa_id, usuario_id, total, status, observacoes, created_at, updated_at) 
VALUES ($mesaId, $usuarioId, $total, 'finalizado', 'Pedido de teste para API unificada de pagamentos', NOW(), NOW());";

file_put_contents($sqlFile, $sql);

echo "✅ Arquivo SQL criado: $sqlFile\n";
echo "📝 SQL gerado:\n$sql\n\n";

// Executar via mysql
$command = "mysql -h 127.0.0.1 -P 3306 -u root myd_bar_restaurantes < $sqlFile";
echo "🔧 Comando para executar:\n$command\n\n";

// Tentar executar automaticamente
$output = shell_exec($command . " 2>&1");
if ($output) {
    echo "📋 Saída do MySQL:\n$output\n";
} else {
    echo "✅ Comando executado com sucesso!\n";
}

// Limpar arquivo temporário
unlink($sqlFile);

echo "\n🧪 Pedido de teste criado! Agora testando a API...\n";

// Buscar o último pedido criado
$lastIdCommand = 'mysql -h 127.0.0.1 -P 3306 -u root -e "SELECT MAX(id) FROM pedidos" myd_bar_restaurantes';
$lastId = trim(shell_exec($lastIdCommand . " 2>/dev/null"));
if (is_numeric($lastId)) {
    echo "🆔 Último pedido criado: ID $lastId\n\n";
    
    // Testar a API
    echo "🧪 Testando API com pedido $lastId...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:8000/api/pagamentos-teste/info/pedido/$lastId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "📊 Status HTTP: $httpCode\n";
    if ($httpCode == 200) {
        echo "✅ SUCESSO! Pedido disponível para pagamento!\n";
        $decoded = json_decode($response, true);
        echo "📄 Resposta:\n" . json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    } else {
        echo "❌ Erro: $response\n";
    }
}

echo "\n✅ Script concluído!\n";

?>
