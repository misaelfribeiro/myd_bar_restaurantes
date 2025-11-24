<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Adicionando contextos de seleção de restaurante...\n\n";

$contextos = [
    [
        'key' => 'select_restaurant',
        'category' => 'navigation',
        'pattern' => '*(seleciona|escolhe|abre|entra|vai para|mostra|quero ver) * (restaurante|loja|estabelecimento)*',
        'response' => 'Vou abrir o restaurante para você! 🏪',
        'response_template' => 'Vou abrir o restaurante ${restaurant_name} para você! 🏪',
        'confidence_threshold' => 0.6,
        'active' => true,
        'action' => 'selectRestaurant',
        'requires_context' => false
    ],
    [
        'key' => 'list_restaurants',
        'category' => 'navigation',
        'pattern' => '*(quais|que|mostra|lista|tem|existe) * (restaurante|loja|estabelecimento)*',
        'response' => 'Vou mostrar os restaurantes disponíveis para você! 🍽️',
        'response_template' => 'Vou mostrar os restaurantes disponíveis para você! 🍽️',
        'confidence_threshold' => 0.7,
        'active' => true,
        'action' => 'showRestaurants',
        'requires_context' => false
    ],
    [
        'key' => 'select_restaurant_direct',
        'category' => 'navigation',
        'pattern' => '*(restaurante|loja) * (teste|claudia|dona claudia)*',
        'response' => 'Abrindo restaurante! 🏪',
        'response_template' => 'Abrindo ${restaurant_name}! 🏪',
        'confidence_threshold' => 0.7,
        'active' => true,
        'action' => 'selectRestaurant',
        'requires_context' => false
    ]
];

foreach ($contextos as $ctx) {
    $existing = \App\Models\AIContext::where('key', $ctx['key'])->first();
    
    if ($existing) {
        $existing->update($ctx);
        echo "✓ Atualizado: {$ctx['key']}\n";
    } else {
        \App\Models\AIContext::create($ctx);
        echo "✓ Criado: {$ctx['key']}\n";
    }
}

echo "\n✅ Contextos de restaurante adicionados!\n";
echo "\nTotal de contextos ativos: " . \App\Models\AIContext::where('active', true)->count() . "\n";
