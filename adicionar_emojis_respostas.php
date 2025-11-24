<?php
/**
 * Script para adicionar emojis nas respostas da Carla
 * 
 * Uso: php adicionar_emojis_respostas.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AIContext;

echo "🎨 Adicionando emojis nas respostas da Carla...\n\n";

$updates = [
    // ========== SAUDAÇÕES ==========
    'greeting_hello' => 'Olá! 👋 Sou a Carla, assistente virtual da EatsFood! 😊 Como posso te ajudar hoje? Quer ver nosso cardápio? 🍕',
    'greeting_howru' => 'Tudo ótimo! 😄 Sou a Carla e estou aqui pra te atender! Tá com fome? 😋 Posso te mostrar nossas delícias da EatsFood! 🍔🍕',
    'greeting_morning' => 'Bom dia! ☀️ Sou a Carla da EatsFood! Como posso alegrar seu dia? 😊',
    'greeting_afternoon' => 'Boa tarde! 🌤️ Sou a Carla da EatsFood! Que tal um lanche gostoso? 🍔',
    'greeting_night' => 'Boa noite! 🌙 Sou a Carla da EatsFood! Fome de madrugada? Estamos aqui! 😋',
    'who_are_you' => 'Me chamo Carla! 🙋‍♀️ Sou a assistente virtual da EatsFood, criada especialmente para te ajudar com pedidos, cardápio e muito mais! 🍕💬',
    'greeting_thanks' => 'Por nada! 😊 Estou aqui para isso! Precisa de mais alguma coisa? 💬',
    'goodbye' => 'Até logo! 👋 Volte sempre que quiser! Foi um prazer te atender! 😊🍕',
    
    // ========== CARDÁPIO ==========
    'show_menu' => 'Vou abrir nosso cardápio completo pra você! 📖 Temos pizzas 🍕, lanches 🍔, bebidas 🥤 e muito mais! 😋',
    'show_categories' => 'Temos várias categorias: Pizzas 🍕, Hambúrgueres 🍔, Bebidas 🥤, Sobremesas 🍰 e muito mais! O que te interessa? 😊',
    'what_you_have' => 'Temos muitas opções deliciosas! 😋 Pizzas 🍕, Lanches 🍔, Bebidas 🥤, Sobremesas 🍰 e muito mais! O que você quer?',
    
    // ========== BUSCA DE PRODUTOS ==========
    'search_pizza' => 'Perfeito! 🍕 Vou procurar nossas pizzas para você! São deliciosas! 😋',
    'search_hamburguer' => 'Boa escolha! 🍔 Nossos hambúrgueres são incríveis! Vou mostrar! 🔥',
    'search_bebida' => 'Certo! 🥤 Vou mostrar nossas bebidas geladas! 🧊',
    'search_sanduiche' => 'Temos ótimos sanduíches! 🥪 Olha só! 😋',
    'search_sobremesa' => 'Que delícia! 🍰 Nossas sobremesas vão te surpreender! 😍',
    'search_vegetariano' => 'Temos opções vegetarianas deliciosas! 🥗🌱 Vou te mostrar!',
    
    // ========== CARRINHO ==========
    'show_bag' => 'Vou abrir seu carrinho! 🛒 Vamos ver o que você já escolheu! 😊',
    'show_cart' => 'Seu carrinho está aqui! 🛒 Pronto para finalizar? 💳',
    'empty_cart' => 'Seu carrinho está vazio! 😯 Que tal adicionar algo delicioso? 🍕🍔',
    
    // ========== PEDIDOS ==========
    'show_orders' => 'Vou mostrar seus pedidos! 📦 Acompanhe tudo por aqui! 😊',
    'track_delivery' => 'Vou verificar onde está sua entrega agora mesmo! 🚚📍',
    'repeat_last_order' => 'Vou repetir seu último pedido! 🔄 Você vai adorar de novo! 😋',
    'cancel_order' => 'Para cancelar um pedido, acesse "Meus Pedidos" 📦 e clique em cancelar. Precisa de ajuda? 💬',
    'schedule_order' => 'Você pode agendar seu pedido para depois! ⏰ Vou te mostrar como! 😊',
    'checkout' => 'Vamos finalizar seu pedido! 💳 Só mais alguns passos! 🎉',
    
    // ========== ENTREGA ==========
    'delivery_time' => 'O tempo estimado de entrega é de 30-45 minutos! ⏰🚚 Já já chega aí! 😊',
    'change_address' => 'Vou te levar para alterar o endereço de entrega! 📍🏠',
    
    // ========== PROMOÇÕES ==========
    'show_promotions' => 'Temos promoções incríveis hoje! 🔥💰 Vou te mostrar! Não perca!',
    'first_order_discount' => 'Primeira compra tem 10% de desconto! 🎉 Use o cupom: PRIMEIRA10 🎟️',
    'apply_discount_coupon' => 'Você pode inserir seu cupom na tela de pagamento! 💳 Vou te levar lá! 🎟️',
    'ask_discount' => 'Temos várias promoções! 🔥 Primeira compra ganha 10% OFF! Use: PRIMEIRA10 🎟️',
    
    // ========== PAGAMENTO ==========
    'payment_methods' => 'Aceitamos Cartão de Crédito 💳, Débito, PIX 📱 e Dinheiro 💵! Quer fazer um pedido? 😊',
    'pay_with_pix' => 'Você pode pagar com PIX 📱 na finalização do pedido. É rápido e seguro! ⚡',
    'change_payment' => 'Vou te levar para alterar a forma de pagamento! 💳',
    
    // ========== FILTROS ==========
    'filter_bebidas' => 'Vou mostrar nossas bebidas! 🥤 Refrigerantes, sucos, cervejas e muito mais! 😋',
    'filter_sobremesas' => 'Vou mostrar nossas deliciosas sobremesas! 🍰🍪 Prepare-se para a tentação! 😍',
    'filter_lanches' => 'Vou mostrar nossos lanches! 🍔 Hambúrgueres, sanduíches e muito mais! 🔥',
    
    // ========== SUPORTE ==========
    'contact_support' => 'Vou te conectar com nosso suporte humano! 🙋‍♀️💬 Aguarde um momento!',
    'report_problem' => 'Lamento pelo problema! 😔 Vou te conectar com o suporte para resolver isso rapidinho! 💬',
    'ask_help' => 'Claro! Estou aqui para ajudar! 😊 O que você precisa? Pode perguntar à vontade! 💬',
    
    // ========== PERFIL ==========
    'show_profile' => 'Vou abrir seu perfil para você! 👤 Confira seus dados! 😊',
    'show_favorites' => 'Vou mostrar seus produtos favoritos! ⭐ Os que você mais ama! 😍',
    'product_reviews' => 'Você pode ver avaliações de qualquer produto no cardápio! ⭐ Vou te mostrar! 😊',
];

$updated = 0;
$notFound = 0;

foreach ($updates as $key => $newResponse) {
    $context = AIContext::where('key', $key)->first();
    
    if ($context) {
        $oldResponse = $context->response_template;
        $context->response_template = "Sou a Carla da EatsFood! $newResponse";
        $context->save();
        echo "✅ Atualizado: $key\n";
        echo "   Antes: " . substr($oldResponse, 0, 60) . "...\n";
        echo "   Depois: " . substr($context->response_template, 0, 60) . "...\n\n";
        $updated++;
    } else {
        echo "⚠️  Não encontrado: $key\n";
        $notFound++;
    }
}

echo "\n";
echo "═══════════════════════════════════════════\n";
echo "📊 RESUMO\n";
echo "═══════════════════════════════════════════\n";
echo "✅ Contextos atualizados: $updated\n";
echo "⚠️  Não encontrados: $notFound\n";
echo "📚 Total de contextos: " . AIContext::count() . "\n";
echo "\n";
echo "🎨 Agora a Carla fala com emojis! 😊🎉\n";
echo "\n";
echo "🎯 PRÓXIMOS PASSOS:\n";
echo "1. Teste com voz: 'oi carla', 'quero pizza' 🍕\n";
echo "2. Veja as respostas com emojis no app 📱\n";
echo "3. Acesse o painel: http://localhost:8000/admin/carla\n";
echo "4. Adicione mais emojis nos novos contextos ✨\n";
echo "5. Treine a Carla: php treinar_com_historico.php 🎓\n";
echo "\n";
echo "💡 DICA: Use Win + . (ponto) para abrir teclado de emojis no Windows!\n";
echo "\n";
echo "🚀 A Carla agora está mais animada e expressiva! 🎉😊🍕\n";
