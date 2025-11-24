<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== TESTE CARLA COM PRODUTOS ===\n\n";

$tenantCode = 'RESTAURANTE0001';
$message = 'quero cerveja';

echo "Tenant: {$tenantCode}\n";
echo "Mensagem: {$message}\n\n";

// Simular chamada da API
$aiService = app(\App\Services\AILearningService::class);

try {
    $result = $aiService->processMessage($message, null, null, $tenantCode);
    
    echo "Resposta: {$result['response']}\n";
    echo "Intent: {$result['intent']}\n";
    echo "Confiança: " . ($result['confidence'] * 100) . "%\n";
    echo "Ação: {$result['action']}\n";
    
    if (!empty($result['products'])) {
        echo "\nProdutos encontrados: " . count($result['products']) . "\n";
        foreach ($result['products'] as $produto) {
            if (is_array($produto)) {
                echo "  - {$produto['nome']} - R$ {$produto['preco']}\n";
            } else {
                echo "  - {$produto->nome} - R$ {$produto->preco}\n";
            }
        }
    } else {
        echo "\nNenhum produto encontrado.\n";
    }
    
    echo "\n=== TESTE: a mais barata ===\n";
    $result2 = $aiService->processMessage('a mais barata', $result['session_token'], null, $tenantCode);
    echo "Resposta: {$result2['response']}\n";
    echo "Intent: {$result2['intent']}\n";
    echo "Ação: {$result2['action']}\n";
    
    if (!empty($result2['products'])) {
        echo "Produtos: " . count($result2['products']) . "\n";
        foreach ($result2['products'] as $produto) {
            if (is_array($produto)) {
                echo "  - {$produto['nome']} - R$ {$produto['preco']}\n";
            } else {
                echo "  - {$produto->nome} - R$ {$produto->preco}\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
