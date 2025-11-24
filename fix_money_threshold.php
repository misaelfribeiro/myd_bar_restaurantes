<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== AJUSTANDO THRESHOLDS ===\n\n";

DB::table('ai_contexts')
    ->where('key', 'select_payment_money')
    ->update(['confidence_threshold' => 0.65]);
echo "✅ select_payment_money: 0.65\n";

DB::table('ai_contexts')
    ->where('key', 'inform_change_amount')
    ->update(['confidence_threshold' => 0.65]);
echo "✅ inform_change_amount: 0.65\n";

// Testar novamente
use App\Services\AILearningService;
$ai = new AILearningService();
$clienteId = DB::table('clientes')->value('id');

echo "\n=== TESTE ===\n";
$r = $ai->processMessage('pagar com dinheiro', null, $clienteId);
echo "Resposta: {$r['response']}\n";
echo "Payment: " . json_encode($r['payment_selected'] ?? 'null') . "\n";
