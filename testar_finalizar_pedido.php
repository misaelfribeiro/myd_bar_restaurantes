<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AILearningService;

$ai = new AILearningService();
$sessionToken = 'test-' . time();

echo "=== TESTE: FINALIZAR PEDIDO ===\n\n";

echo "Comando: 'finalizar pedido'\n";
$result = $ai->processMessage('finalizar pedido', $sessionToken, 2); // User ID 2

echo "\n🤖 Intent: {$result['intent']}\n";
echo "🤖 Action: {$result['action']}\n";
echo "🤖 Resposta: {$result['response']}\n";
echo "🔀 Navegação: " . ($result['navigate_to'] ?? 'nenhuma') . "\n";

if ($result['navigate_to'] === 'confirm_order') {
    echo "\n✅ Comando funcionando! Frontend deve chamar confirmOrder()\n";
} else {
    echo "\n❌ Navegação não retornou 'confirm_order'\n";
}
