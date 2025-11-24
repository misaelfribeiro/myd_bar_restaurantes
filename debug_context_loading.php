<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AIContext;

echo "🔍 Verificando se AIContext está carregando add_to_cart_confirm\n\n";

// Simular o que findMatchingContexts faz
$message = 'quero esse';
$contexts = AIContext::where('active', true)->get();

echo "Total de contextos ativos: " . $contexts->count() . "\n\n";

$matches = [];

foreach ($contexts as $context) {
    if ($context->matches($message)) {
        echo "✅ MATCH: {$context->key}\n";
        echo "   Pattern: {$context->pattern}\n";
        echo "   Action: {$context->action}\n";
        echo "   Confidence threshold: {$context->confidence_threshold}\n\n";
        
        $matches[] = $context;
    }
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Total de matches: " . count($matches) . "\n";

if (count($matches) > 0) {
    echo "\n✅ add_to_cart_confirm ESTÁ fazendo match!\n";
    echo "O problema deve estar no cálculo de confidence no AILearningService.\n";
} else {
    echo "\n❌ Nenhum contexto fez match!\n";
}
