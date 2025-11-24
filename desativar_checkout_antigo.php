<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DESATIVAR CONTEXTOS ANTIGOS DO CARRINHO ===\n\n";

// Desativar contextos que competem com os novos
$oldContexts = [
    'checkout',
    'view_order_status',
    'my_orders',
    'show_cart',
    'finish_order'
];

foreach ($oldContexts as $key) {
    $ctx = DB::table('ai_contexts')->where('key', $key)->first();
    
    if ($ctx && $ctx->active) {
        DB::table('ai_contexts')->where('key', $key)->update(['active' => false]);
        echo "✓ Desativado: {$key} (action: {$ctx->action})\n";
    }
}

echo "\n✅ Contextos desativados!\n";
