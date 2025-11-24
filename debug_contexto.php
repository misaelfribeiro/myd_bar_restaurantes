<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== DEBUG CONTEXTO ===\n\n";

$context = \App\Models\AIContext::where('key', 'search_continuation_specific')->first();

echo "Key: {$context->key}\n";
echo "Pattern: {$context->pattern}\n";
echo "Action: {$context->action}\n";
echo "Requires context: " . ($context->requires_context ? 'SIM' : 'NÃO') . "\n\n";

// Testar match
$message = 'a mais barata';
$matches = $context->matches($message);
echo "Mensagem '{$message}' match? " . ($matches ? 'SIM' : 'NÃO') . "\n";

if ($matches) {
    $params = $context->extractParameters($message);
    echo "Parâmetros extraídos: " . json_encode($params) . "\n";
}
