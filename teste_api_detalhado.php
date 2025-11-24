<?php
// TESTE COMPLETO DA API UNIFICADA - Versão Detalhada
// IMPORTANTE: Executar via browser acessando http://localhost/myd_bar_restaurantes/teste_api_detalhado.php

echo "<h1>🔬 TESTE DETALHADO DA API UNIFICADA</h1>";
echo "<p>Data/Hora: " . date('Y-m-d H:i:s') . "</p>";

$baseUrl = 'http://localhost/myd_bar_restaurantes/public/api';

function testarEndpoint($url, $data = null, $metodo = 'GET') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if ($metodo === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => $response,
        'error' => $error,
        'data' => $response ? json_decode($response, true) : null
    ];
}

// ==================== TESTE 1: STATUS DA API ====================
echo "<div style='background: #e7f3ff; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
echo "<h2>🔍 1. Verificação do Status da API</h2>";
$result = testarEndpoint("$baseUrl/pagamentos-status");
echo "<p><strong>URL:</strong> <code>/api/pagamentos-status</code></p>";
echo "<p><strong>Método:</strong> GET</p>";
echo "<p><strong>Status HTTP:</strong> <span style='font-weight:bold; color:" . ($result['http_code'] == 200 ? 'green' : 'red') . ";'>{$result['http_code']}</span></p>";
if ($result['data']) {
    echo "<p><strong>Resposta:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 4px;'>" . json_encode($result['data'], JSON_PRETTY_PRINT) . "</pre>";
}
if ($result['error']) echo "<p style='color: red;'><strong>Erro cURL:</strong> {$result['error']}</p>";
echo "</div>";

// ==================== TESTE 2: CRIAR DADOS DE TESTE ====================
echo "<div style='background: #fff3cd; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
echo "<h2>🛠️ 2. Preparação de Dados de Teste</h2>";

// Vamos usar a API para verificar dados ao invés de tentar usar modelos diretamente
echo "<p>⚡ Verificando dados através da API...</p>";

// Testar se conseguimos acessar o endpoint de teste da API
$testeMesa = testarEndpoint("$baseUrl/teste-models");
if ($testeMesa['http_code'] == 200 && $testeMesa['data'] && isset($testeMesa['data']['success']) && $testeMesa['data']['success']) {
    echo "<p style='color: green;'>✅ <strong>Conexão com banco OK via API!</strong></p>";
    echo "<p>Mesas disponíveis: " . count($testeMesa['data']['mesas']) . "</p>";
    
    if (count($testeMesa['data']['mesas']) > 0) {
        $mesa = $testeMesa['data']['mesas'][0];
        $mesaId = $mesa['id'];
        echo "<ul>";
        echo "<li>Mesa de teste: #{$mesa['id']} (Número {$mesa['numero']})</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠️ Nenhuma mesa encontrada. Usando dados padrão para teste.</p>";
        $mesaId = 1;
    }
    
    // Definir pedido de teste baseado nos dados encontrados
    if (isset($testeMesa['data']['pedidos']) && count($testeMesa['data']['pedidos']) > 0) {
        $pedidoTeste = (object)$testeMesa['data']['pedidos'][0];
    } else {
        $pedidoTeste = (object)['id' => 19, 'total' => '50.00']; // Pedido conhecido que criamos antes
    }
} else {
    echo "<p style='color: orange;'>⚠️ API não disponível ou sem dados. Usando dados padrão para teste.</p>";
    echo "<p><strong>Status da resposta:</strong> " . ($testeMesa['http_code'] ?: 'Sem resposta') . "</p>";
    if ($testeMesa['error']) {
        echo "<p style='color: red;'><strong>Erro:</strong> {$testeMesa['error']}</p>";
    }
    $mesaId = 1;
    $pedidoTeste = (object)['id' => 19, 'total' => '50.00'];
}

echo "</div>";

// ==================== TESTE 3: INFO PEDIDO ====================
if ($pedidoTeste) {
    echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
    echo "<h2>📋 3. Informações do Pedido</h2>";
    $result = testarEndpoint("$baseUrl/pagamentos-teste/info/pedido/{$pedidoTeste->id}");
    echo "<p><strong>URL:</strong> <code>/api/pagamentos-teste/info/pedido/{$pedidoTeste->id}</code></p>";
    echo "<p><strong>Status HTTP:</strong> <span style='font-weight:bold; color:" . ($result['http_code'] == 200 ? 'green' : 'red') . ";'>{$result['http_code']}</span></p>";
    if ($result['data']) {
        echo "<p><strong>Estrutura da Resposta:</strong></p>";
        if ($result['data']['success']) {
            $pedidoInfo = $result['data']['data']['pedido'];
            echo "<ul>";
            echo "<li>Pedido ID: {$pedidoInfo['id']}</li>";
            echo "<li>Mesa: {$pedidoInfo['mesa']}</li>";
            echo "<li>Total: R$ {$pedidoInfo['total']}</li>";
            echo "<li>Status: {$pedidoInfo['status']}</li>";
            echo "</ul>";
        }
        echo "<details><summary>Ver resposta completa</summary>";
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 4px; max-height: 300px; overflow-y: auto;'>" . json_encode($result['data'], JSON_PRETTY_PRINT) . "</pre>";
        echo "</details>";
    }
    echo "</div>";    // ==================== TESTE 4: INFO MESA ====================
    echo "<div style='background: #d1ecf1; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
    echo "<h2>🏪 4. Informações da Mesa</h2>";
    $result = testarEndpoint("$baseUrl/pagamentos-teste/info/mesa/{$mesaId}");
    echo "<p><strong>URL:</strong> <code>/api/pagamentos-teste/info/mesa/{$mesaId}</code></p>";
    echo "<p><strong>Status HTTP:</strong> <span style='font-weight:bold; color:" . ($result['http_code'] == 200 ? 'green' : 'red') . ";'>{$result['http_code']}</span></p>";
    if ($result['data']) {
        if ($result['data']['success']) {
            $mesaInfo = $result['data']['data']['mesa'];
            echo "<ul>";
            echo "<li>Mesa ID: {$mesaInfo['id']}</li>";
            echo "<li>Número: {$mesaInfo['numero']}</li>";
            echo "<li>Total Geral: R$ {$mesaInfo['total_geral']}</li>";
            echo "<li>Pedidos: " . count($result['data']['data']['pedidos']) . "</li>";
            echo "</ul>";
        }
        echo "<details><summary>Ver resposta completa</summary>";
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 4px; max-height: 300px; overflow-y: auto;'>" . json_encode($result['data'], JSON_PRETTY_PRINT) . "</pre>";
        echo "</details>";
    }
    echo "</div>";

    // ==================== TESTE 5: PAGAMENTO ÚNICO ====================
    echo "<div style='background: #f8d7da; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
    echo "<h2>💳 5. Teste de Pagamento Único</h2>";
    
    $dadosPagamento = [
        'forma_pagamento' => 'dinheiro',
        'valor' => floatval($pedidoTeste->total),
        'valor_recebido' => floatval($pedidoTeste->total) + 10,
        'observacoes' => 'Teste API - Pagamento único em dinheiro'
    ];
    
    echo "<p><strong>URL:</strong> <code>/api/pagamentos-teste/pedido/{$pedidoTeste->id}</code></p>";
    echo "<p><strong>Método:</strong> POST</p>";
    echo "<p><strong>Dados enviados:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 4px;'>" . json_encode($dadosPagamento, JSON_PRETTY_PRINT) . "</pre>";
    
    $result = testarEndpoint("$baseUrl/pagamentos-teste/pedido/{$pedidoTeste->id}", $dadosPagamento, 'POST');
    echo "<p><strong>Status HTTP:</strong> <span style='font-weight:bold; color:" . ($result['http_code'] == 200 ? 'green' : 'red') . ";'>{$result['http_code']}</span></p>";
    
    if ($result['data']) {
        if ($result['data']['success']) {
            echo "<p style='color: green;'>✅ <strong>Pagamento processado com sucesso!</strong></p>";
            $pagamentoData = $result['data']['data'];
            echo "<ul>";
            echo "<li>Total processado: R$ {$pagamentoData['total_processado']}</li>";
            echo "<li>Pedido totalmente pago: " . ($pagamentoData['pedido_totalmente_pago'] ? 'Sim' : 'Não') . "</li>";
            echo "<li>Saldo restante: R$ {$pagamentoData['saldo_restante']}</li>";
            echo "<li>Quantidade de pagamentos: " . count($pagamentoData['pagamentos']) . "</li>";
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>❌ <strong>Erro:</strong> {$result['data']['message']}</p>";
        }
        echo "<details><summary>Ver resposta completa</summary>";
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 4px; max-height: 300px; overflow-y: auto;'>" . json_encode($result['data'], JSON_PRETTY_PRINT) . "</pre>";
        echo "</details>";
    }
    echo "</div>";
}

// ==================== RESULTADO FINAL ====================
echo "<div style='background: #6c757d; color: white; padding: 20px; margin: 20px 0; border-radius: 8px; text-align: center;'>";
echo "<h2>📊 RESULTADO FINAL DOS TESTES</h2>";
echo "<p>🕒 Teste concluído em: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>🔧 <a href='teste_api_pagamentos.html' style='color: #ffc107;'>Interface de Teste Completa</a></p>";
echo "<p>📄 <a href='/myd_bar_restaurantes/public/garcom/mesas' style='color: #28a745;'>Voltar ao Modo Garçom</a></p>";
echo "</div>";
?>

<style>
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 20px; background: #f5f5f5; }
h1 { color: #2c3e50; text-align: center; }
h2 { color: #34495e; margin-top: 0; }
code { background: #e9ecef; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }
pre { font-size: 12px; }
details { margin-top: 10px; }
summary { cursor: pointer; padding: 8px; background: #e9ecef; border-radius: 4px; }
</style>
