<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== REATIVANDO ADD_PRODUCT_TO_CART COM NOVA ACTION ===\n\n";

// Reativar e atualizar contexto para adicionar ao carrinho do app
DB::table('ai_contexts')
    ->where('key', 'add_product_to_cart')
    ->update([
        'active' => true,
        'action' => 'addToAppCart',  // Nova ação
        'response_template' => '✅ {produto} adicionado ao carrinho! Continue escolhendo ou vá ao carrinho para finalizar.',
        'confidence_threshold' => 0.75
    ]);

echo "✓ Contexto add_product_to_cart reativado\n";
echo "  Action: addToAppCart\n";
echo "  Pattern: *(adiciona|adicionar|coloca|colocar|quero|pegar|pega|pede|pedir)*(esse|essa|este|esta|isso|este aqui|essa aqui|esta aqui|ai|aí|isso ai)*\n\n";

echo "✅ Agora 'quero esse' vai adicionar automaticamente ao carrinho do app!\n";
