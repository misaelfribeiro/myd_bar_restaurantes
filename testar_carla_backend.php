<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE DA IA CARLA - BACKEND ===\n\n";

// Simular requisição HTTP
function testarEndpoint($method, $url, $data = []) {
    echo "📡 Testando: $method $url\n";
    echo "   Dados: " . json_encode($data) . "\n";
    
    $ch = curl_init();
    
    $fullUrl = 'http://localhost' . $url;
    
    if ($method === 'GET' && !empty($data)) {
        $fullUrl .= '?' . http_build_query($data);
    }
    
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "   Status: $httpCode\n";
    
    if ($response) {
        $json = json_decode($response, true);
        if ($json) {
            echo "   Resposta: " . json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        } else {
            echo "   Resposta (não-JSON): " . substr($response, 0, 200) . "...\n";
        }
    }
    
    echo "\n";
    return ['code' => $httpCode, 'response' => $response];
}

// Teste 1: Processar mensagem simples
echo "=== TESTE 1: Mensagem Simples ===\n";
$result1 = testarEndpoint('POST', '/api/ai/process', [
    'message' => 'Olá Carla',
    'session_token' => 'teste_' . time(),
    'user_id' => null
]);

// Teste 2: Buscar produtos
echo "=== TESTE 2: Buscar Produtos ===\n";
$result2 = testarEndpoint('POST', '/api/ai/process', [
    'message' => 'quero uma pizza',
    'session_token' => 'teste_' . time(),
    'user_id' => null
]);

// Teste 3: Perguntar preço
echo "=== TESTE 3: Perguntar Preço ===\n";
$result3 = testarEndpoint('POST', '/api/ai/process', [
    'message' => 'quanto custa a pizza margherita?',
    'session_token' => 'teste_' . time(),
    'user_id' => null
]);

// Teste 4: Adicionar ao carrinho
echo "=== TESTE 4: Adicionar ao Carrinho ===\n";
$result4 = testarEndpoint('POST', '/api/ai/process', [
    'message' => 'adicione uma pizza ao carrinho',
    'session_token' => 'teste_' . time(),
    'user_id' => null
]);

// Teste 5: Ver cardápio
echo "=== TESTE 5: Ver Cardápio ===\n";
$result5 = testarEndpoint('POST', '/api/ai/process', [
    'message' => 'mostre o cardápio',
    'session_token' => 'teste_' . time(),
    'user_id' => null
]);

// Teste 6: Status de pedido
echo "=== TESTE 6: Status de Pedido ===\n";
$result6 = testarEndpoint('POST', '/api/ai/process', [
    'message' => 'onde está meu pedido?',
    'session_token' => 'teste_' . time(),
    'user_id' => null
]);

// Teste 7: Teste com sessão persistente
echo "=== TESTE 7: Conversação Contínua (mesma sessão) ===\n";
$sessionToken = 'teste_conversacao_' . time();

testarEndpoint('POST', '/api/ai/process', [
    'message' => 'olá',
    'session_token' => $sessionToken,
]);

sleep(1);

testarEndpoint('POST', '/api/ai/process', [
    'message' => 'quero uma pizza',
    'session_token' => $sessionToken,
]);

sleep(1);

testarEndpoint('POST', '/api/ai/process', [
    'message' => 'qual o preço?',
    'session_token' => $sessionToken,
]);

// Resumo
echo "\n=== RESUMO DOS TESTES ===\n";
echo "✅ Teste 1 - Mensagem Simples: " . ($result1['code'] == 200 ? 'OK' : 'FALHOU') . "\n";
echo "✅ Teste 2 - Buscar Produtos: " . ($result2['code'] == 200 ? 'OK' : 'FALHOU') . "\n";
echo "✅ Teste 3 - Perguntar Preço: " . ($result3['code'] == 200 ? 'OK' : 'FALHOU') . "\n";
echo "✅ Teste 4 - Adicionar Carrinho: " . ($result4['code'] == 200 ? 'OK' : 'FALHOU') . "\n";
echo "✅ Teste 5 - Ver Cardápio: " . ($result5['code'] == 200 ? 'OK' : 'FALHOU') . "\n";
echo "✅ Teste 6 - Status Pedido: " . ($result6['code'] == 200 ? 'OK' : 'FALHOU') . "\n";

echo "\n=== FIM DOS TESTES ===\n";
