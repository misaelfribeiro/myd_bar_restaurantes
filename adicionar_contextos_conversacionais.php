<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AIContext;

echo "🧠 Adicionando contextos conversacionais (memória)...\n\n";

$contextosConversacionais = [
    // CONTINUAÇÃO DE BUSCA
    [
        'category' => 'search',
        'key' => 'search_continuation_specific',
        'pattern' => '*(o mais barato|a mais barata|mais em conta|mais caro|melhor|top)*',
        'response_template' => 'Entendi! Vou procurar ${product_type} ${criteria} para você! 🔍',
        'action' => 'searchWithFilter',
        'confidence_threshold' => 0.6,
        'active' => true,
        'requires_context' => true
    ],
    [
        'category' => 'search',
        'key' => 'search_continuation_yes',
        'pattern' => '*(sim|isso|exato|certo|uhum|é)*',
        'response_template' => 'Perfeito! Vou buscar isso para você! ✅',
        'action' => 'confirmSearch',
        'confidence_threshold' => 0.5,
        'active' => true,
        'requires_context' => true
    ],
    
    // CONTINUAÇÃO DE QUANTIDADE
    [
        'category' => 'cart',
        'key' => 'quantity_response',
        'pattern' => '*(1|2|3|4|5|um|dois|tres|quatro|cinco|uma|duas)*',
        'response_template' => 'Anotado! ${quantity} ${item}(s). Algo mais? 📝',
        'action' => 'addWithQuantity',
        'confidence_threshold' => 0.7,
        'active' => true,
        'requires_context' => true
    ],
    
    // NEGAÇÃO / CANCELAMENTO
    [
        'category' => 'general',
        'key' => 'cancel_no',
        'pattern' => '*(não|nao|nem|nunca|jamais|de jeito nenhum)*',
        'response_template' => 'Tudo bem! 👌 O que posso fazer por você então?',
        'action' => 'cancelCurrent',
        'confidence_threshold' => 0.7,
        'active' => true,
        'requires_context' => true
    ],
    
    // CONFIRMAÇÃO
    [
        'category' => 'general',
        'key' => 'confirm_yes',
        'pattern' => '*(sim|yes|ok|pode|tudo bem|beleza|vai|confirma)*',
        'response_template' => 'Confirmado! ✅ Continuando...',
        'action' => 'confirmAction',
        'confidence_threshold' => 0.6,
        'active' => true,
        'requires_context' => true
    ],
    
    // CONTINUAÇÃO DE PEDIDO
    [
        'category' => 'cart',
        'key' => 'add_more_context',
        'pattern' => '*(e|também|mais|outro|outra)*',
        'response_template' => 'Claro! O que mais vai querer? 🛒',
        'action' => 'continueAdding',
        'confidence_threshold' => 0.5,
        'active' => true,
        'requires_context' => true
    ],
    
    // REFERÊNCIA AO ANTERIOR
    [
        'category' => 'general',
        'key' => 'reference_that',
        'pattern' => '*(esse|essa|isso|este|esta|aquele|aquela)*',
        'response_template' => 'Você quer ${last_item}? Confirma? ✅',
        'action' => 'referenceLastItem',
        'confidence_threshold' => 0.6,
        'active' => true,
        'requires_context' => true
    ],
    
    // TAMANHOS
    [
        'category' => 'info',
        'key' => 'size_response',
        'pattern' => '*(pequen|médio|media|grande|gigante|família|familia|broto)*',
        'response_template' => 'Perfeito! ${size} anotado! 📏',
        'action' => 'setSize',
        'confidence_threshold' => 0.7,
        'active' => true,
        'requires_context' => true
    ],
    
    // PREFERÊNCIAS
    [
        'category' => 'search',
        'key' => 'preference_gelada',
        'pattern' => '*(gelad|gela|bem gelad)*',
        'response_template' => 'Pode deixar! Bem gelada! 🧊',
        'action' => 'addPreference',
        'confidence_threshold' => 0.7,
        'active' => true,
        'requires_context' => true
    ],
    
    // MODIFICAÇÕES
    [
        'category' => 'cart',
        'key' => 'remove_ingredient',
        'pattern' => '*(sem|tira|retira|remove) (cebola|tomate|queijo|bacon|molho)*',
        'response_template' => 'Ok! Sem ${ingredient}! 🚫',
        'action' => 'removeIngredient',
        'confidence_threshold' => 0.7,
        'active' => true,
        'requires_context' => true
    ],
    [
        'category' => 'cart',
        'key' => 'add_extra',
        'pattern' => '*(com mais|extra|adiciona|coloca mais) (queijo|bacon|molho|cebola)*',
        'response_template' => 'Adicionado ${ingredient} extra! 🔥',
        'action' => 'addExtra',
        'confidence_threshold' => 0.7,
        'active' => true,
        'requires_context' => true
    ],
    
    // FINALIZAÇÃO
    [
        'category' => 'cart',
        'key' => 'finish_order',
        'pattern' => '*(só isso|é só|é tudo|pronto|finaliza|termina|fecha)*',
        'response_template' => 'Ótimo! Vou finalizar seu pedido! 🎯 Confirma?',
        'action' => 'prepareCheckout',
        'confidence_threshold' => 0.7,
        'active' => true,
        'requires_context' => true
    ],
    
    // MUDANÇA DE IDEIA
    [
        'category' => 'general',
        'key' => 'change_mind',
        'pattern' => '*(na verdade|melhor|mudei|desculpa|espera)*',
        'response_template' => 'Sem problema! O que você prefere então? 🔄',
        'action' => 'changeMind',
        'confidence_threshold' => 0.6,
        'active' => true,
        'requires_context' => true
    ],
];

$added = 0;
$skipped = 0;

foreach ($contextosConversacionais as $contexto) {
    $exists = AIContext::where('key', $contexto['key'])->exists();
    
    if ($exists) {
        echo "⏭️  '{$contexto['key']}' já existe\n";
        $skipped++;
        continue;
    }
    
    AIContext::create($contexto);
    echo "✅ Adicionado: {$contexto['key']}\n";
    $added++;
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ {$added} contextos conversacionais adicionados!\n";
echo "⏭️  {$skipped} contextos já existiam\n";
echo "📊 Total de contextos: " . AIContext::count() . "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n🧠 Carla agora tem memória conversacional!\n\n";

echo "📝 Exemplos de conversas que ela entende agora:\n";
echo "   Você: oi\n";
echo "   Carla: Olá! 👋\n";
echo "   Você: quero bebidas\n";
echo "   Carla: Temos várias bebidas! 🥤\n";
echo "   Você: procura cerveja\n";
echo "   Carla: Qual cerveja prefere?\n";
echo "   Você: a mais barata\n";
echo "   Carla: Vou procurar cerveja mais barata! 🔍\n";
