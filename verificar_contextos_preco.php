<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Contextos relacionados a 'barato' ou 'continuação':\n\n";

$contexts = \App\Models\AIContext::where('key', 'LIKE', '%continuation%')
    ->orWhere('key', 'LIKE', '%barato%')
    ->orWhere('key', 'LIKE', '%cheaper%')
    ->orWhere('pattern', 'LIKE', '%barato%')
    ->get(['key','pattern','requires_context']);

foreach($contexts as $c) {
    echo "{$c->key}:\n";
    echo "  Pattern: {$c->pattern}\n";
    echo "  Requires context: " . ($c->requires_context ? 'SIM' : 'NÃO') . "\n\n";
}

if ($contexts->isEmpty()) {
    echo "Nenhum contexto encontrado! Precisa criar.\n";
}
