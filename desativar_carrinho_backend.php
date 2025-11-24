<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DESATIVANDO CONTEXTOS DE CARRINHO BACKEND ===\n\n";

// Desativar contextos de carrinho backend
$contexts = [
    'add_product_to_cart',
    'view_cart_backend',
    'checkout_backend',
    'clear_cart_backend'
];

foreach ($contexts as $key) {
    $updated = DB::table('ai_contexts')
        ->where('key', $key)
        ->update(['active' => false]);
    
    if ($updated) {
        echo "✓ Desativado: {$key}\n";
    }
}

echo "\n✅ Contextos de carrinho backend desativados!\n";
echo "A IA agora apenas mostra produtos e o usuário adiciona ao carrinho do app manualmente.\n";
