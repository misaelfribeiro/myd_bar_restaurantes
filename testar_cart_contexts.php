<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\AIContext;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTANDO VIEW CART E CHECKOUT ===\n\n";

$tests = [
    'mostra o carrinho' => 'view_cart_backend',
    'finalizar pedido' => 'checkout_backend',
    'ver meu pedido' => 'view_cart_backend',
    'concluir compra' => 'checkout_backend'
];

foreach ($tests as $message => $expectedContext) {
    echo "Mensagem: \"{$message}\"\n";
    echo "Esperado: {$expectedContext}\n";
    
    $contexts = AIContext::where('active', true)->get();
    $matched = null;
    
    foreach ($contexts as $context) {
        if ($context->matches($message)) {
            if ($context->key == $expectedContext || !$matched) {
                $matched = $context;
                if ($context->key == $expectedContext) {
                    break;
                }
            }
        }
    }
    
    if ($matched) {
        echo "✓ Match: {$matched->key} (threshold: {$matched->confidence_threshold})\n";
    } else {
        echo "✗ Nenhum match encontrado\n";
    }
    echo "\n";
}
