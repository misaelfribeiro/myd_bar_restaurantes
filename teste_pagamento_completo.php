<?php

echo "=== TESTANDO PROCESSAMENTO DE PAGAMENTO ===\n";

// Teste 1: Pagamento único
echo "\n1. Testando pagamento único (cartão crédito)...\n";

$data = [
    'forma_pagamento' => 'cartao_credito',
    'valor' => 50.00,
    'observacoes' => 'Teste pagamento único via API'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/pagamentos-teste/pedido/19');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $httpCode\n";
$decoded = json_decode($response, true);
if ($decoded) {
    echo "Resposta:\n" . json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "Resposta: $response\n";
}

// Teste 2: Múltiplos pagamentos para completar o restante
echo "\n2. Testando múltiplos pagamentos para o restante...\n";

$multiplosPagamentos = [
    [
        'forma_pagamento' => 'dinheiro',
        'valor' => 25.00,
        'valor_recebido' => 30.00
    ],
    [
        'forma_pagamento' => 'pix',
        'valor' => 14.50
    ]
];

$data2 = [
    'multiplos_pagamentos' => json_encode($multiplosPagamentos)
];

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, 'http://localhost:8000/api/pagamentos-teste/pedido/19');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($data2));
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response2 = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

echo "Status: $httpCode2\n";
$decoded2 = json_decode($response2, true);
if ($decoded2) {
    echo "Resposta:\n" . json_encode($decoded2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "Resposta: $response2\n";
}

// Teste 3: Verificar estado final do pedido
echo "\n3. Verificando estado final do pedido...\n";
$ch3 = curl_init();
curl_setopt($ch3, CURLOPT_URL, 'http://localhost:8000/api/pagamentos-teste/info/pedido/19');
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_HTTPHEADER, ['Accept: application/json']);

$response3 = curl_exec($ch3);
$httpCode3 = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
curl_close($ch3);

echo "Status: $httpCode3\n";
$decoded3 = json_decode($response3, true);
if ($decoded3) {
    echo "Resposta:\n" . json_encode($decoded3, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "Resposta: $response3\n";
}

echo "\n✅ TESTES CONCLUÍDOS!\n";

?>
