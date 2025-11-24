<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\AIContext;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG CONTEXT MATCHING ===\n\n";

$message = "quero essa";
echo "Mensagem: \"{$message}\"\n\n";

$contexts = AIContext::where('active', true)->get();
echo "Contextos ativos: {$contexts->count()}\n\n";

echo "--- TESTANDO MATCHES ---\n\n";

foreach ($contexts as $context) {
    $matches = $context->matches($message);
    
    if ($matches) {
        echo "✓ {$context->key}\n";
        echo "  Pattern: {$context->pattern}\n";
        echo "  Action: {$context->action}\n";
        echo "  Threshold: {$context->confidence_threshold}\n\n";
    }
}
