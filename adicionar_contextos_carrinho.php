<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🛒 Criando contextos para fluxo de pedido\n\n";

// 1. Adicionar ao carrinho (quando produto já foi mostrado)
$addToCart = [
    'category' => 'cart',
    'key' => 'add_to_cart_confirm',
    'pattern' => '.*(adiciona|adicionar|coloca|colocar|quero|pedir|pede|esse|este|essa).*',
    'response_template' => 'Produto adicionado ao carrinho! 🛒',
    'action' => 'addToCart',
    'confidence_threshold' => 0.60,
    'active' => 1,
    'requires_context' => 0,
    'usage_count' => 0,
    'success_rate' => 0.80
];

// 2. Finalizar pedido / Fazer checkout
$checkout = [
    'category' => 'cart',
    'key' => 'checkout_order',
    'pattern' => '.*(finaliz|conclu|confir|fazer pedido|fechar|pagar|pagamento).*',
    'response_template' => 'Vou finalizar seu pedido! 🎉',
    'action' => 'checkout',
    'confidence_threshold' => 0.80,
    'active' => 1,
    'requires_context' => 0,
    'usage_count' => 0,
    'success_rate' => 0.80
];

// 3. Ver carrinho
$viewCart = [
    'category' => 'cart',
    'key' => 'view_cart',
    'pattern' => '.*(ver|mostra|olha|check).*(carrinho|sacola|pedido).*',
    'response_template' => 'Vou mostrar seu carrinho! 🛒',
    'action' => 'showCart',
    'confidence_threshold' => 0.85,
    'active' => 1,
    'requires_context' => 0,
    'usage_count' => 0,
    'success_rate' => 0.80
];

$contexts = [$addToCart, $checkout, $viewCart];

foreach ($contexts as $context) {
    // Verificar se já existe
    $existing = DB::table('ai_contexts')->where('key', $context['key'])->first();
    
    if ($existing) {
        DB::table('ai_contexts')
            ->where('key', $context['key'])
            ->update([
                'pattern' => $context['pattern'],
                'action' => $context['action'],
                'confidence_threshold' => $context['confidence_threshold'],
                'active' => $context['active'],
                'updated_at' => now()
            ]);
        echo "✓ Atualizado: {$context['key']}\n";
    } else {
        DB::table('ai_contexts')->insert(array_merge($context, [
            'created_at' => now(),
            'updated_at' => now()
        ]));
        echo "✓ Criado: {$context['key']}\n";
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Fluxo completo disponível:\n\n";

echo "1️⃣ Selecionar restaurante:\n";
echo "   'Carla, mostra os restaurantes'\n";
echo "   'seleciona restaurante teste'\n\n";

echo "2️⃣ Buscar produto:\n";
echo "   'quero bebida'\n";
echo "   'mostra a mais barata'\n\n";

echo "3️⃣ Adicionar ao carrinho:\n";
echo "   'adiciona esse' ou 'quero esse'\n";
echo "   Action: addToCart\n\n";

echo "4️⃣ Ver carrinho:\n";
echo "   'mostra o carrinho'\n";
echo "   Action: showCart\n\n";

echo "5️⃣ Finalizar pedido:\n";
echo "   'finalizar pedido' ou 'fazer checkout'\n";
echo "   Action: checkout\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Contextos criados! Agora vou verificar as actions no frontend.\n";
