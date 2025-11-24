<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Verificando TODOS os contextos que fazem match com 'lista restaurantes'\n\n";

$message = 'lista restaurantes';
$contexts = DB::table('ai_contexts')
    ->where('active', 1)
    ->get();

$matches = [];

foreach ($contexts as $context) {
    // Simular método matches()
    $pattern = $context->pattern;
    $pattern = str_replace('*', '.*', $pattern);
    
    if (@preg_match('/' . $pattern . '/i', $message) === 1) {
        $matches[] = [
            'key' => $context->key,
            'pattern' => $context->pattern,
            'confidence' => $context->confidence_threshold,
            'action' => $context->action
        ];
    }
}

// Ordenar por confidence
usort($matches, function($a, $b) {
    return $b['confidence'] <=> $a['confidence'];
});

echo "Contextos que fazem MATCH com '{$message}':\n\n";

foreach ($matches as $i => $match) {
    echo ($i + 1) . ". {$match['key']} (confidence: {$match['confidence']})\n";
    echo "   Action: {$match['action']}\n";
    echo "   Pattern: {$match['pattern']}\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Total: " . count($matches) . " matches\n";
