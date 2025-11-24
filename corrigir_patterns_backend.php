<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CORRIGINDO PATTERNS DOS CONTEXTOS BACKEND ===\n\n";

$updates = [
    'checkout_backend' => '*(finaliz|conclu|confir|fazer|fechar|enviar)*(pedido|compra)*',
    'view_cart_backend' => '*(ver|mostra|olha|check|verifica|como esta)*(carrinho|sacola|pedido|meu pedido)*',
];

foreach ($updates as $key => $pattern) {
    DB::table('ai_contexts')
        ->where('key', $key)
        ->update(['pattern' => $pattern]);
    
    echo "✓ {$key}: pattern atualizado\n";
    echo "  {$pattern}\n\n";
}

echo "✅ Patterns corrigidos!\n";
