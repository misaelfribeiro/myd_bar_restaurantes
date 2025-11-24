<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AIContext;

echo "📚 Adicionando Novos Conhecimentos para a Carla...\n\n";

// Novos contextos para treinar
$novosContextos = [
    [
        'category' => 'greeting',
        'key' => 'greeting_thanks',
        'pattern' => '(obrigado|obrigada|valeu|thanks|vlw)',
        'response_template' => 'Por nada! Estou aqui sempre que precisar! 😊',
        'action' => null,
        'confidence_threshold' => 0.7,
    ],
    [
        'category' => 'search',
        'key' => 'search_sobremesa',
        'pattern' => '*(sobremesa|doce|sorvete|pudim|mousse)*',
        'response_template' => 'Hmm, nossas sobremesas são deliciosas! Vou mostrar!',
        'action' => 'searchProduct',
        'parameters' => ['query' => 'sobremesa'],
        'confidence_threshold' => 0.6,
    ],
    [
        'category' => 'search',
        'key' => 'search_vegetariano',
        'pattern' => '*(vegetariano|vegano|sem carne|vegetariana)*',
        'response_template' => 'Temos ótimas opções vegetarianas! Olha só!',
        'action' => 'searchProduct',
        'parameters' => ['query' => 'vegetariano'],
        'confidence_threshold' => 0.7,
    ],
    [
        'category' => 'promotion',
        'key' => 'ask_discount',
        'pattern' => '*(desconto|cupom|promoção|oferta)*',
        'response_template' => 'Vou mostrar nossas promoções e descontos especiais!',
        'action' => 'showPromotions',
        'confidence_threshold' => 0.7,
    ],
    [
        'category' => 'info',
        'key' => 'contact',
        'pattern' => '*(telefone|contato|whatsapp|falar)*',
        'response_template' => 'Entre em contato conosco pelo WhatsApp ou telefone disponível no app!',
        'action' => null,
        'confidence_threshold' => 0.7,
    ],
    [
        'category' => 'menu',
        'key' => 'what_you_have',
        'pattern' => '*(o que tem|o que vocês tem|que tem|tem o que)*',
        'response_template' => 'Temos muitas opções deliciosas! Deixa eu mostrar nosso cardápio completo!',
        'action' => 'showMenu',
        'confidence_threshold' => 0.65,
    ],
    [
        'category' => 'cart',
        'key' => 'show_bag',
        'pattern' => '*(sacola|bolsa|bag)*',
        'response_template' => 'Vou mostrar sua sacola de compras!',
        'action' => 'showCart',
        'confidence_threshold' => 0.7,
    ],
];

foreach ($novosContextos as $contexto) {
    $ctx = AIContext::updateOrCreate(
        ['key' => $contexto['key']],
        $contexto
    );
    
    echo "✅ {$ctx->key}: {$ctx->pattern}\n";
}

echo "\n🎉 " . count($novosContextos) . " novos contextos adicionados!\n";
echo "📊 Total de contextos: " . AIContext::count() . "\n";
