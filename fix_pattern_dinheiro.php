<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== AJUSTANDO PATTERN DE DINHEIRO ===\n\n";

// Pattern mais específico
$newPattern = '*(pag|quero pag)*(dinheiro|espécie|especie|cash|vivo)*';

DB::table('ai_contexts')
    ->where('key', 'select_payment_money')
    ->update([
        'pattern' => $newPattern,
        'confidence_threshold' => 0.70
    ]);

echo "✅ Pattern atualizado: $newPattern\n";
echo "✅ Threshold: 0.70\n\n";

// Testar
use App\Services\AILearningService;
$ai = new AILearningService();
$clienteId = DB::table('clientes')->value('id');

$tests = [
    'pagar com dinheiro',
    'quero pagar com dinheiro',
    'pagar dinheiro',
    'pagamento em dinheiro'
];

foreach($tests as $msg) {
    echo "Teste: '$msg'\n";
    $r = $ai->processMessage($msg, null, $clienteId);
    echo "Response: {$r['response']}\n";
    echo "Payment: " . ($r['payment_selected']['method'] ?? 'null') . "\n\n";
}
