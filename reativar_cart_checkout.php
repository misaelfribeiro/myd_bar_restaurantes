<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== REATIVANDO CONTEXTOS DE CARRINHO E CHECKOUT ===\n\n";

// Reativar view_cart_backend como viewAppCart
DB::table('ai_contexts')
    ->where('key', 'view_cart_backend')
    ->update([
        'active' => true,
        'action' => 'viewAppCart',
        'response_template' => '🛒 Mostrando seu carrinho!',
        'confidence_threshold' => 0.75
    ]);

echo "✓ view_cart_backend reativado como viewAppCart\n";
echo "  Pattern: *(ver|mostra|olha|check|verifica|como esta)*(carrinho|sacola|pedido|meu pedido)*\n\n";

// Reativar checkout_backend como checkoutApp
DB::table('ai_contexts')
    ->where('key', 'checkout_backend')
    ->update([
        'active' => true,
        'action' => 'checkoutApp',
        'response_template' => '✅ Pronto! Vou te levar para finalizar seu pedido.',
        'confidence_threshold' => 0.75
    ]);

echo "✓ checkout_backend reativado como checkoutApp\n";
echo "  Pattern: *(finaliz|conclu|confir|fazer|fechar|enviar)*(pedido|compra)*\n\n";

echo "✅ Contextos reativados!\n";
echo "- 'mostra o carrinho' → Abre tela de carrinho do app\n";
echo "- 'finalizar pedido' → Abre tela de checkout do app\n";
