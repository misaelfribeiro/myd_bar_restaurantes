#!/usr/bin/env php
<?php

echo "=== TESTE DE MÚLTIPLOS PAGAMENTOS ===\n\n";

// Dados de teste
$multiplosPagamentos = [
    [
        'forma_pagamento' => 'dinheiro',
        'valor' => 15.00
    ],
    [
        'forma_pagamento' => 'cartao_credito', 
        'valor' => 10.00
    ]
];

$json = json_encode($multiplosPagamentos);
echo "JSON gerado:\n{$json}\n\n";

// Teste de decodificação
$decoded = json_decode($json, true);
echo "Dados decodificados:\n";
var_dump($decoded);

echo "\nValidação:\n";
$totalPagamentos = 0;
foreach ($decoded as $forma) {
    if (!isset($forma['forma_pagamento'], $forma['valor'])) {
        echo "❌ Dados incompletos!\n";
        exit(1);
    }
    
    if (!in_array($forma['forma_pagamento'], ['dinheiro', 'cartao_credito', 'cartao_debito', 'pix', 'vale_refeicao'])) {
        echo "❌ Forma de pagamento inválida: {$forma['forma_pagamento']}\n";
        exit(1);
    }
    
    $valor = floatval($forma['valor']);
    if ($valor <= 0) {
        echo "❌ Valor inválido!\n";
        exit(1);
    }
    
    $totalPagamentos += $valor;
    echo "✅ {$forma['forma_pagamento']}: R$ {$valor}\n";
}

echo "\nTotal dos pagamentos: R$ {$totalPagamentos}\n";
echo "Valor esperado do pedido: R$ 25.00\n";

if (abs($totalPagamentos - 25.00) < 0.01) {
    echo "✅ TOTAIS CONFEREM!\n";
} else {
    echo "❌ TOTAIS NÃO CONFEREM!\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";
