<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Verificando pattern do add_to_cart_confirm\n\n";

$context = DB::table('ai_contexts')
    ->where('key', 'add_to_cart_confirm')
    ->first();

if ($context) {
    echo "Status: " . ($context->active ? 'ATIVO' : 'INATIVO') . "\n";
    echo "Pattern: {$context->pattern}\n";
    echo "Confidence: {$context->confidence_threshold}\n\n";
    
    // Testar o pattern
    $testMessages = [
        'adiciona essa',
        'adiciona esse',
        'quero esse',
        'coloca essa',
        'pega esse'
    ];
    
    echo "Testando pattern:\n";
    $pattern = str_replace('*', '.*', $context->pattern);
    
    foreach ($testMessages as $msg) {
        $matches = @preg_match('/' . $pattern . '/i', $msg);
        $icon = $matches ? '✅' : '❌';
        echo "  {$icon} '{$msg}'\n";
        
        if (!$matches) {
            echo "      Regex: /{$pattern}/i\n";
        }
    }
} else {
    echo "❌ Contexto add_to_cart_confirm não encontrado!\n";
}
