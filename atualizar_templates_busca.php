<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ATUALIZANDO TEMPLATES DE RESPOSTA ===\n\n";

// Atualizar contextos de busca para orientar o usuário
$updates = [
    'search_bebida' => 'Vou buscar bebidas para você! 🥤 Depois é só clicar no botão "Adicionar ao Carrinho" do produto que você gostar.',
    'search_comida' => 'Vou procurar comidas deliciosas! 🍔 Depois clique no botão "Adicionar ao Carrinho" para escolher.',
    'search_lanche' => 'Vou buscar lanches para você! 🥪 Clique em "Adicionar ao Carrinho" no produto desejado.',
    'search_sobremesa' => 'Vou buscar sobremesas! 🍰 Depois clique no botão "Adicionar ao Carrinho".',
];

foreach ($updates as $key => $template) {
    $ctx = DB::table('ai_contexts')->where('key', $key)->first();
    
    if ($ctx) {
        DB::table('ai_contexts')
            ->where('key', $key)
            ->update(['response_template' => $template]);
        
        echo "✓ Atualizado: {$key}\n";
    }
}

// Atualizar resposta genérica de produtos encontrados
echo "\n✅ Templates atualizados!\n";
echo "A IA agora orienta o usuário a clicar no botão para adicionar ao carrinho.\n";
