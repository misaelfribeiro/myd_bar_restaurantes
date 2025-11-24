<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Atualizando contexto search_continuation_specific...\n";

$context = \App\Models\AIContext::where('key', 'search_continuation_specific')->first();

if ($context) {
    $context->requires_context = true;
    $context->action = 'searchProduct';
    $context->save();
    
    echo "✓ Contexto atualizado!\n";
    echo "  Requires context: SIM\n";
    echo "  Action: {$context->action}\n";
} else {
    echo "✗ Contexto não encontrado!\n";
}
