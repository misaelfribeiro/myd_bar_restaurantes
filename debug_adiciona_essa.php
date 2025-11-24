<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 Analisando contexto que captura 'adiciona essa'\n\n";

// Testar qual contexto está pegando
$message = 'adiciona essa';
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

usort($matches, function($a, $b) {
    return $b['confidence'] <=> $a['confidence'];
});

echo "Contextos que fazem MATCH com '{$message}':\n\n";

foreach ($matches as $i => $match) {
    echo ($i + 1) . ". {$match['key']} (confidence: {$match['confidence']})\n";
    echo "   Action: {$match['action']}\n";
    echo "   Pattern: " . substr($match['pattern'], 0, 80) . "\n\n";
}

if (count($matches) > 0) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🎯 O contexto vencedor seria: {$matches[0]['key']}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    if ($matches[0]['key'] !== 'add_to_cart_confirm') {
        echo "⚠️  PROBLEMA: '{$matches[0]['key']}' está ganhando ao invés de 'add_to_cart_confirm'!\n\n";
        
        echo "Desativando '{$matches[0]['key']}'...\n";
        DB::table('ai_contexts')
            ->where('key', $matches[0]['key'])
            ->update(['active' => 0, 'updated_at' => now()]);
        
        echo "✓ {$matches[0]['key']} desativado\n";
    }
}
