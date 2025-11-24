<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AILearningService;

echo "🧪 Testando 'quero esse' via AILearningService\n\n";

$service = new AILearningService();

$result = $service->processMessage(
    'quero esse',
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
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

if ($result['action'] === 'addToCart') {
    echo "✅ SUCESSO! Action 'addToCart' foi acionada corretamente.\n";
} else {
    echo "❌ ERRO! Action esperada: 'addToCart', recebida: '{$result['action']}'\n";
}
