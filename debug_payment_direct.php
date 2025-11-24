<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\AILearningService;

// Ativar logs
DB::enableQueryLog();

$ai = new AILearningService();
$clienteId = DB::table('clientes')->value('id');

echo "=== DEBUG: 'pagar com cartão de crédito' ===\n\n";
$result = $ai->processMessage('pagar com cartão de crédito', null, $clienteId);

echo "Response: {$result['response']}\n";
echo "Action: " . ($result['action'] ?? 'null') . "\n";
echo "Payment Selected: " . json_encode($result['payment_selected'] ?? 'null') . "\n";
echo "Intent: " . ($result['intent'] ?? 'null') . "\n";
echo "Confidence: " . ($result['confidence'] ?? 'null') . "\n\n";

echo "=== DEBUG: 'pagar com dinheiro' ===\n\n";
$result2 = $ai->processMessage('pagar com dinheiro', null, $clienteId);

echo "Response: {$result2['response']}\n";
echo "Action: " . ($result2['action'] ?? 'null') . "\n";
echo "Payment Selected: " . json_encode($result2['payment_selected'] ?? 'null') . "\n";
echo "Intent: " . ($result2['intent'] ?? 'null') . "\n";
echo "Confidence: " . ($result2['confidence'] ?? 'null') . "\n";
