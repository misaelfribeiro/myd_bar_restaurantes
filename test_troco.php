<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== AJUSTANDO CONTEXTO DE TROCO ===\n\n";

// Atualizar pattern para ser mais simples
DB::table('ai_contexts')
    ->where('key', 'inform_change_amount')
    ->update([
        'pattern' => '*(troco|preciso)*(\\d+|cinquenta|cem|vinte)*',
        'confidence_threshold' => 0.60
    ]);

echo "✅ Pattern atualizado\n\n";

// Testar
use App\Services\AILearningService;
$ai = new AILearningService();
$clienteId = DB::table('clientes')->value('id');

$tests = [
    'troco para 50',
    'troco pra 100',
    'preciso de troco para 50',
    '50 reais'
];

foreach($tests as $msg) {
    echo "Teste: '$msg'\n";
    $r = $ai->processMessage($msg, null, $clienteId);
    echo "Response: {$r['response']}\n";
    echo "Change: " . ($r['change_amount'] ?? 'null') . "\n\n";
}
