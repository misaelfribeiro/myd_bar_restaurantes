<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🛒 Criando contextos para fluxo completo via backend\n\n";

$contextos = [
    // 1. Adicionar produto ao carrinho (via backend)
    [
        'category' => 'cart',
        'key' => 'add_product_to_cart',
        'pattern' => '*(adiciona|adicionar|coloca|colocar|quero|pegar|pega|pede|pedir) *(esse|essa|este|esta|isso|este aqui|essa aqui|esta aqui|ai|aí|isso ai)*',
        'response_template' => 'Produto adicionado ao carrinho! Quer adicionar mais alguma coisa ou finalizar o pedido?',
        'action' => 'addToCartBackend',
        'confidence_threshold' => 0.90,
        'active' => 1,
        'requires_context' => 0,
        'usage_count' => 0,
        'success_rate' => 0.80
    ],
    
    // 2. Ver carrinho
    [
        'category' => 'cart',
        'key' => 'view_cart_backend',
        'pattern' => '*(ver|mostra|olha|check|verifica|como esta) *(carrinho|sacola|pedido|meu pedido)*',
        'response_template' => 'Vou te mostrar o que tem no seu carrinho.',
        'action' => 'viewCartBackend',
        'confidence_threshold' => 0.85,
        'active' => 1,
        'requires_context' => 0,
        'usage_count' => 0,
        'success_rate' => 0.80
    ],
    
    // 3. Finalizar pedido
    [
        'category' => 'cart',
        'key' => 'checkout_backend',
        'pattern' => '*(finaliz|conclu|confir|fazer|fechar|enviar) *(pedido|compra)*',
        'response_template' => 'Pedido finalizado com sucesso! Em breve você receberá a confirmação.',
        'action' => 'checkoutBackend',
        'confidence_threshold' => 0.85,
        'active' => 1,
        'requires_context' => 0,
        'usage_count' => 0,
        'success_rate' => 0.80
    ],
    
    // 4. Limpar carrinho
    [
        'category' => 'cart',
        'key' => 'clear_cart_backend',
        'pattern' => '*(limpa|limpar|esvazia|esvaziar|cancela|cancelar|remove tudo) *(carrinho|sacola|pedido)*',
        'response_template' => 'Carrinho limpo! Quer começar um novo pedido?',
        'action' => 'clearCartBackend',
        'confidence_threshold' => 0.80,
        'active' => 1,
        'requires_context' => 0,
        'usage_count' => 0,
        'success_rate' => 0.80
    ],
];

foreach ($contextos as $contexto) {
    $existing = DB::table('ai_contexts')->where('key', $contexto['key'])->first();
    
    if ($existing) {
        DB::table('ai_contexts')
            ->where('key', $contexto['key'])
            ->update([
                'pattern' => $contexto['pattern'],
                'action' => $contexto['action'],
                'response_template' => $contexto['response_template'],
                'confidence_threshold' => $contexto['confidence_threshold'],
                'active' => $contexto['active'],
                'updated_at' => now()
            ]);
        echo "✓ Atualizado: {$contexto['key']}\n";
    } else {
        DB::table('ai_contexts')->insert(array_merge($contexto, [
            'created_at' => now(),
            'updated_at' => now()
        ]));
        echo "✓ Criado: {$contexto['key']}\n";
    }
}

// Desativar os contextos antigos do frontend
echo "\nDesativando contextos do frontend...\n";
DB::table('ai_contexts')
    ->whereIn('key', ['add_to_cart_confirm', 'view_cart', 'checkout_order'])
    ->update(['active' => 0, 'updated_at' => now()]);

echo "✓ Contextos do frontend desativados\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📝 FLUXO COMPLETO VIA BACKEND:\n\n";
echo "1️⃣ Buscar produto:\n";
echo "   'quero cerveja' → API retorna produtos\n\n";
echo "2️⃣ Adicionar ao carrinho:\n";
echo "   'adiciona essa' → Action: addToCartBackend\n";
echo "   Backend adiciona produto no carrinho (sessão)\n\n";
echo "3️⃣ Ver carrinho:\n";
echo "   'mostra o carrinho' → Action: viewCartBackend\n";
echo "   Backend retorna itens do carrinho\n\n";
echo "4️⃣ Finalizar pedido:\n";
echo "   'finalizar pedido' → Action: checkoutBackend\n";
echo "   Backend cria pedido no banco\n\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Contextos criados! Agora vou implementar as actions no backend.\n";
