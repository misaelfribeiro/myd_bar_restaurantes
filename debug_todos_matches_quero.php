<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Verificando TODOS os contextos que capturam 'quero esse'\n\n";

$message = 'quero esse';
$contexts = DB::table('ai_contexts')
    ->where('active', 1)
    ->get();

$matches = [];

foreach ($contexts as $context) {
    $pattern = $context->pattern;
    $pattern = str_replace('*', '.*', $pattern);
    
    if (@preg_match('/' . $pattern . '/i', $message) === 1) {
        $matches[] = [
            'key' => $context->key,
            'confidence' => $context->confidence_threshold,
            'action' => $context->action,
            'pattern' => $context->pattern
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

if (count($matches) > 0) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🎯 Contexto vencedor: {$matches[0]['key']}\n";
    echo "   Action esperada: addToCart\n";
    echo "   Action real: {$matches[0]['action']}\n";
    
    if ($matches[0]['key'] === 'add_to_cart_confirm') {
        echo "\n✅ CORRETO! add_to_cart_confirm está vencendo.\n";
    } else {
        echo "\n⚠️ PROBLEMA! '{$matches[0]['key']}' está vencendo ao invés de add_to_cart_confirm.\n";
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Total de matches: " . count($matches) . "\n";
