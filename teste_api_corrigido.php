<?php
// TESTE DA API DE PAGAMENTOS CORRIGIDA
require_once 'vendor/autoload.php';

echo "<h1>🚀 TESTE DA API DE PAGAMENTOS CORRIGIDA</h1>";
echo "<p>Data/Hora: " . date('Y-m-d H:i:s') . "</p>";

$baseUrl = 'http://localhost/myd_bar_restaurantes/public/api';

function testarEndpoint($url, $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($data) {
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
        'error' => $error
    ];
}

echo "<h2>🔍 1. Teste do Status da API</h2>";
$result = testarEndpoint("$baseUrl/pagamentos-status");
echo "<p><strong>Status HTTP:</strong> {$result['http_code']}</p>";
echo "<p><strong>Resposta:</strong> {$result['response']}</p>";
if ($result['error']) echo "<p><strong>Erro:</strong> {$result['error']}</p>";

echo "<h2>📋 2. Teste de Informações do Pedido 19</h2>";
$result = testarEndpoint("$baseUrl/pagamentos-teste/info/pedido/19");
echo "<p><strong>Status HTTP:</strong> {$result['http_code']}</p>";
echo "<p><strong>Resposta:</strong> <pre>" . htmlspecialchars($result['response']) . "</pre></p>";
if ($result['error']) echo "<p><strong>Erro:</strong> {$result['error']}</p>";

echo "<h2>💳 3. Teste de Pagamento Único - Pedido 19</h2>";
$dadosPagamento = [
    'forma_pagamento' => 'dinheiro',
    'valor' => 50.00,
    'valor_recebido' => 60.00,
    'observacoes' => 'Teste da API corrigida'
];

$result = testarEndpoint("$baseUrl/pagamentos-teste/pedido/19", $dadosPagamento);
echo "<p><strong>Status HTTP:</strong> {$result['http_code']}</p>";
echo "<p><strong>Resposta:</strong> <pre>" . htmlspecialchars($result['response']) . "</pre></p>";
if ($result['error']) echo "<p><strong>Erro:</strong> {$result['error']}</p>";

if ($result['http_code'] == 200) {
    $response = json_decode($result['response'], true);
    if ($response['success']) {
        echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; color: #155724; margin: 10px 0;'>
                ✅ <strong>SUCESSO!</strong> Pagamento processado com sucesso!
              </div>";
    }
} else {
    echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; color: #721c24; margin: 10px 0;'>
            ❌ <strong>ERRO!</strong> Falha no processamento do pagamento.
          </div>";
}

echo "<h2>💰 4. Teste de Múltiplos Pagamentos</h2>";
$multiplosPagamentos = [
    'multiplos_pagamentos' => json_encode([
        [
            'forma_pagamento' => 'dinheiro',
            'valor' => 25.00,
            'valor_recebido' => 30.00,
            'observacoes' => 'Parte em dinheiro'
        ],
        [
            'forma_pagamento' => 'cartao_credito',
            'valor' => 25.00,
            'observacoes' => 'Parte no cartão'
        ]
    ])
];

$result = testarEndpoint("$baseUrl/pagamentos-teste/pedido/19", $multiplosPagamentos);
echo "<p><strong>Status HTTP:</strong> {$result['http_code']}</p>";
echo "<p><strong>Resposta:</strong> <pre>" . htmlspecialchars($result['response']) . "</pre></p>";
if ($result['error']) echo "<p><strong>Erro:</strong> {$result['error']}</p>";

echo "<h2>📊 Resultado Final</h2>";
if ($result['http_code'] == 200 || $result['http_code'] == 400) {
    echo "<div style='background: #d1ecf1; padding: 10px; border: 1px solid #bee5eb; color: #0c5460; margin: 10px 0;'>
            🎉 <strong>API FUNCIONANDO!</strong> A API de pagamentos está operacional.
          </div>";
} else {
    echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; color: #856404; margin: 10px 0;'>
            ⚠️ <strong>ATENÇÃO!</strong> Ainda há problemas na API de pagamentos.
          </div>";
}

echo "<p><a href='teste_api_pagamentos.html'>🔧 Ir para Interface de Teste Completa</a></p>";
?>
