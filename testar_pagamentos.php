<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$ai = new App\Services\AILearningService();
$clienteId = DB::table('clientes')->value('id');

echo "=== TESTE FORMAS DE PAGAMENTO ===\n\n";

$tests = [
    'pagar com pix' => 'pix',
    'pagar com cartão de crédito' => 'cartao_credito',
    'pagar com cartão de débito' => 'cartao_debito',
    'pagar com dinheiro' => 'dinheiro'
];

foreach($tests as $msg => $expected) {
    echo "Teste: '$msg'\n";
    $r = $ai->processMessage($msg, null, $clienteId);
    echo "Resposta: {$r['response']}\n";
    $method = $r['payment_selected']['method'] ?? 'null';
    echo "Método: $method\n";
    echo "✅ " . ($method === $expected ? "CORRETO" : "ERRO - esperado: $expected") . "\n\n";
}

// Teste de troco
echo "=== TESTE TROCO ===\n";
echo "1. Selecionar dinheiro\n";
$r1 = $ai->processMessage('pagar com dinheiro', null, $clienteId);
echo "Resposta: {$r1['response']}\n\n";

echo "2. Informar valor do troco\n";
$r2 = $ai->processMessage('troco para 50', null, $clienteId);
echo "Resposta: {$r2['response']}\n";
$change = $r2['change_amount'] ?? 'null';
echo "Valor: $change\n";
echo "✅ " . ($change == 50 ? "CORRETO" : "ERRO") . "\n";
