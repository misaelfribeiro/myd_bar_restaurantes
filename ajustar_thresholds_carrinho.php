<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== AJUSTANDO CONFIDENCE THRESHOLDS ===\n\n";

// Reduzir threshold dos contextos de carrinho
$contexts = [
    'add_product_to_cart' => 0.75,
    'view_cart_backend' => 0.75,
    'checkout_backend' => 0.75,
    'clear_cart_backend' => 0.70
];

foreach ($contexts as $key => $threshold) {
    $updated = DB::table('ai_contexts')
        ->where('key', $key)
        ->update(['confidence_threshold' => $threshold]);
    
    if ($updated) {
        echo "✓ {$key}: threshold ajustado para {$threshold}\n";
    }
}

echo "\n✅ Thresholds ajustados!\n";
