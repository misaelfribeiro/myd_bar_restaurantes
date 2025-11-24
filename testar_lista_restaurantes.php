<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AILearningService;

echo "🧪 Testando processamento: 'lista restaurantes'\n\n";

$service = new AILearningService();

$result = $service->processMessage(
    'lista restaurantes',
    null,
    null,
    'RESTAURANTE0001'
);

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Resultado:\n";
echo "  Response: {$result['response']}\n";
echo "  Intent: {$result['intent']}\n";
echo "  Action: {$result['action']}\n";
echo "  Confidence: " . round($result['confidence'] * 100, 1) . "%\n";
echo "  Products: " . count($result['products']) . " produtos\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

if (!empty($result['products'])) {
    echo "\n⚠️ PROBLEMA: Está retornando produtos quando deveria apenas listar restaurantes!\n";
} else {
    echo "\n✅ Correto: Não está retornando produtos.\n";
}
