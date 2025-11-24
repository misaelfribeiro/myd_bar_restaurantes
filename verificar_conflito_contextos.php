<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Verificando contextos que podem causar conflito com 'mostrar restaurantes'\n\n";

// Buscar contextos que contenham "mostrar" ou "restaurante"
$contexts = DB::table('ai_contexts')
    ->where(function($query) {
        $query->where('pattern', 'like', '%mostrar%')
              ->orWhere('pattern', 'like', '%restaurante%')
              ->orWhere('intent', 'like', '%restaurant%')
              ->orWhere('intent', 'list_restaurants')
              ->orWhere('intent', 'add_more_context');
    })
    ->orderBy('priority', 'desc')
    ->get(['id', 'intent', 'pattern', 'priority', 'category', 'action']);

foreach ($contexts as $context) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "ID: {$context->id}\n";
    echo "Intent: {$context->intent}\n";
    echo "Action: {$context->action}\n";
    echo "Priority: {$context->priority}\n";
    echo "Category: {$context->category}\n";
    echo "Pattern: {$context->pattern}\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Total: " . count($contexts) . " contextos\n";
