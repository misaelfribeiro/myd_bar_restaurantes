<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Verificando estrutura da tabela ai_contexts\n\n";

// Verificar colunas da tabela
$columns = DB::select("SHOW COLUMNS FROM ai_contexts");

echo "Colunas disponíveis:\n";
foreach ($columns as $column) {
    echo "  - {$column->Field} ({$column->Type})\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Buscar contextos relacionados
echo "Contextos que podem conflitar:\n\n";
$contexts = DB::table('ai_contexts')
    ->where(function($query) {
        $query->where('pattern', 'like', '%mostrar%')
              ->orWhere('pattern', 'like', '%restaurante%')
              ->orWhere('key', 'like', '%restaurant%')
              ->orWhere('key', 'add_more_context');
    })
    ->orderBy('confidence_threshold', 'desc')
    ->get();

foreach ($contexts as $context) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "ID: {$context->id}\n";
    echo "Key: {$context->key}\n";
    echo "Action: {$context->action}\n";
    echo "Confidence Threshold: {$context->confidence_threshold}\n";
    echo "Category: {$context->category}\n";
    echo "Pattern: {$context->pattern}\n";
    echo "Response: " . substr($context->response_template, 0, 60) . "...\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Total: " . count($contexts) . " contextos\n";
