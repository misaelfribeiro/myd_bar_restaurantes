<?php
/**
 * Script para adicionar novas ações/contextos à Carla
 * 
 * Uso: php adicionar_acoes_novas.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AIContext;

echo "🤖 Adicionando novas ações para a Carla...\n\n";

// ============================================
// AÇÕES DE DELIVERY E RASTREAMENTO
// ============================================

$novosContextos = [
    // 1. Rastrear Entrega
    [
        'category' => 'delivery',
        'key' => 'track_delivery',
        'pattern' => '*(rastrear|rastreio|onde está|cadê)*(entrega|pedido|meu pedido)*',
        'response_template' => 'Sou a Carla da EatsFood! Vou verificar onde está sua entrega agora mesmo! 🚚',
        'action' => 'trackDelivery',
        'parameters' => json_encode([]),
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    
    // 2. Tempo de Entrega
    [
        'category' => 'delivery',
        'key' => 'delivery_time',
        'pattern' => '*(quanto tempo|demora|prazo)*(entrega|chegar|chega)*',
        'response_template' => 'Sou a Carla da EatsFood! O tempo estimado de entrega é de 30-45 minutos. Quer fazer um pedido?',
        'action' => 'showMenu',
        'parameters' => json_encode([]),
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    
    // 3. Alterar Endereço
    [
        'category' => 'delivery',
        'key' => 'change_address',
        'pattern' => '*(mudar|alterar|trocar)*(endereço|local|lugar)*',
        'response_template' => 'Sou a Carla da EatsFood! Vou te levar para alterar o endereço de entrega.',
        'action' => 'changeAddress',
        'parameters' => json_encode([]),
        'confidence_threshold' => 0.7,
        'active' => true
    ],

    // ============================================
    // AÇÕES DE PROMOÇÕES E DESCONTOS
    // ============================================
    
    // 4. Ver Promoções
    [
        'category' => 'menu',
        'key' => 'show_promotions',
        'pattern' => '*(promoção|promoções|oferta|ofertas|desconto|barato|em conta)*(hoje|ativa|disponível)*',
        'response_template' => 'Sou a Carla da EatsFood! Temos promoções incríveis hoje! Vou te mostrar. 🔥',
        'action' => 'showPromotions',
        'parameters' => json_encode([]),
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    
    // 5. Aplicar Cupom
    [
        'category' => 'cart',
        'key' => 'apply_discount_coupon',
        'pattern' => '*(cupom|código|desconto)*(aplicar|usar|tenho)*',
        'response_template' => 'Sou a Carla da EatsFood! Você pode inserir seu cupom na tela de pagamento. Vou te levar lá!',
        'action' => 'applyDiscount',
        'parameters' => json_encode([]),
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    
    // 6. Primeira Compra
    [
        'category' => 'info',
        'key' => 'first_order_discount',
        'pattern' => '*(primeira|primeiro)*(compra|pedido)*(desconto)*',
        'response_template' => 'Sou a Carla da EatsFood! Primeira compra tem 10% de desconto! Use o cupom: PRIMEIRA10',
        'action' => 'showMenu',
        'parameters' => json_encode(['coupon' => 'PRIMEIRA10']),
        'confidence_threshold' => 0.7,
        'active' => true
    ],

    // ============================================
    // AÇÕES DE FILTROS E BUSCA
    // ============================================
    
    // 7. Filtrar Bebidas
    [
        'category' => 'search',
        'key' => 'filter_bebidas',
        'pattern' => '*(bebida|bebidas|refrigerante|suco|drink|cerveja)*',
        'response_template' => 'Sou a Carla da EatsFood! Vou mostrar nossas bebidas! 🥤',
        'action' => 'filterByCategory',
        'parameters' => json_encode(['category' => 'bebidas']),
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    
    // 8. Filtrar Sobremesas
    [
        'category' => 'search',
        'key' => 'filter_sobremesas',
        'pattern' => '*(sobremesa|sobremesas|doce|doces|açaí|sorvete)*',
        'response_template' => 'Sou a Carla da EatsFood! Vou mostrar nossas deliciosas sobremesas! 🍰',
        'action' => 'filterByCategory',
        'parameters' => json_encode(['category' => 'sobremesas']),
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    
    // 9. Filtrar Lanches
    [
        'category' => 'search',
        'key' => 'filter_lanches',
        'pattern' => '*(lanche|lanches|hambúrguer|hamburguer|burger|sanduíche)*',
        'response_template' => 'Sou a Carla da EatsFood! Vou mostrar nossos lanches! 🍔',
        'action' => 'filterByCategory',
        'parameters' => json_encode(['category' => 'lanches']),
        'confidence_threshold' => 0.7,
        'active' => true
    ],

    // ============================================
    // AÇÕES DE PEDIDOS
    // ============================================
    
    // 10. Repetir Último Pedido
    [
        'category' => 'orders',
        'key' => 'repeat_last_order',
        'pattern' => '*(repetir|de novo|novamente|mesmo)*(pedido|último|anterior)*',
        'response_template' => 'Sou a Carla da EatsFood! Vou repetir seu último pedido! 🔄',
        'action' => 'repeatOrder',
        'parameters' => json_encode([]),
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    
    // 11. Cancelar Pedido
    [
        'category' => 'orders',
        'key' => 'cancel_order',
        'pattern' => '*(cancelar|desistir)*(pedido)*',
        'response_template' => 'Sou a Carla da EatsFood! Para cancelar um pedido, acesse "Meus Pedidos" e clique em cancelar.',
        'action' => 'showOrders',
        'parameters' => json_encode([]),
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    
    // 12. Agendar Pedido
    [
        'category' => 'orders',
        'key' => 'schedule_order',
        'pattern' => '*(agendar|programar|marcar)*(pedido|entrega)*(depois|mais tarde)*',
        'response_template' => 'Sou a Carla da EatsFood! Você pode agendar seu pedido para depois! Vou te mostrar como.',
        'action' => 'scheduleOrder',
        'parameters' => json_encode([]),
        'confidence_threshold' => 0.7,
        'active' => true
    ],

    // ============================================
    // AÇÕES DE PAGAMENTO
    // ============================================
    
    // 13. Formas de Pagamento
    [
        'category' => 'info',
        'key' => 'payment_methods',
        'pattern' => '*(forma|formas|meio|meios)*(pagamento|pagar)*(aceita|aceitam)*',
        'response_template' => 'Sou a Carla da EatsFood! Aceitamos Cartão de Crédito, Débito, PIX e Dinheiro. Quer fazer um pedido?',
        'action' => 'showMenu',
        'parameters' => json_encode([]),
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    
    // 14. Pagar com PIX
    [
        'category' => 'payment',
        'key' => 'pay_with_pix',
        'pattern' => '*(pagar|pagamento)*(pix)*',
        'response_template' => 'Sou a Carla da EatsFood! Você pode pagar com PIX na finalização do pedido. É rápido e seguro!',
        'action' => 'checkout',
        'parameters' => json_encode(['payment_method' => 'pix']),
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    
    // 15. Mudar Forma de Pagamento
    [
        'category' => 'payment',
        'key' => 'change_payment',
        'pattern' => '*(mudar|alterar|trocar)*(pagamento|forma de pagar)*',
        'response_template' => 'Sou a Carla da EatsFood! Vou te levar para alterar a forma de pagamento.',
        'action' => 'changePayment',
        'parameters' => json_encode([]),
        'confidence_threshold' => 0.7,
        'active' => true
    ],

    // ============================================
    // AÇÕES DE SUPORTE
    // ============================================
    
    // 16. Contatar Suporte
    [
        'category' => 'help',
        'key' => 'contact_support',
        'pattern' => '*(ajuda|suporte|atendimento|falar com)*(alguém|pessoa|atendente|humano)*',
        'response_template' => 'Sou a Carla da EatsFood! Vou te conectar com nosso suporte humano! 🙋',
        'action' => 'contactSupport',
        'parameters' => json_encode([]),
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    
    // 17. Reportar Problema
    [
        'category' => 'help',
        'key' => 'report_problem',
        'pattern' => '*(problema|bug|erro|não funciona)*(app|sistema|pedido)*',
        'response_template' => 'Sou a Carla da EatsFood! Lamento pelo problema! Vou te conectar com o suporte para resolver isso.',
        'action' => 'contactSupport',
        'parameters' => json_encode(['type' => 'problem']),
        'confidence_threshold' => 0.7,
        'active' => true
    ],

    // ============================================
    // AÇÕES DE PERFIL E CONTA
    // ============================================
    
    // 18. Ver Perfil
    [
        'category' => 'account',
        'key' => 'show_profile',
        'pattern' => '*(meu|minha)*(perfil|conta|dados|cadastro)*',
        'response_template' => 'Sou a Carla da EatsFood! Vou abrir seu perfil para você.',
        'action' => 'showProfile',
        'parameters' => json_encode([]),
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    
    // 19. Ver Favoritos
    [
        'category' => 'menu',
        'key' => 'show_favorites',
        'pattern' => '*(favorito|favoritos|preferido|preferidos)*',
        'response_template' => 'Sou a Carla da EatsFood! Vou mostrar seus produtos favoritos! ⭐',
        'action' => 'showFavorites',
        'parameters' => json_encode([]),
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    
    // 20. Avaliar Produto
    [
        'category' => 'info',
        'key' => 'product_reviews',
        'pattern' => '*(avaliação|avaliações|review|comentário)*(produto)*',
        'response_template' => 'Sou a Carla da EatsFood! Você pode ver avaliações de qualquer produto no cardápio!',
        'action' => 'showMenu',
        'parameters' => json_encode([]),
        'confidence_threshold' => 0.7,
        'active' => true
    ],
];

// Inserir contextos
$inseridos = 0;
$erros = 0;

foreach ($novosContextos as $contexto) {
    try {
        // Verifica se já existe
        $existe = AIContext::where('key', $contexto['key'])->first();
        
        if ($existe) {
            echo "⚠️  Contexto '{$contexto['key']}' já existe. Pulando...\n";
            continue;
        }
        
        AIContext::create($contexto);
        echo "✅ Adicionado: {$contexto['key']} (Ação: {$contexto['action']})\n";
        $inseridos++;
        
    } catch (\Exception $e) {
        echo "❌ Erro ao adicionar {$contexto['key']}: " . $e->getMessage() . "\n";
        $erros++;
    }
}

echo "\n";
echo "═══════════════════════════════════════════\n";
echo "📊 RESUMO\n";
echo "═══════════════════════════════════════════\n";
echo "✅ Contextos adicionados: $inseridos\n";
echo "❌ Erros: $erros\n";
echo "📚 Total de contextos agora: " . AIContext::count() . "\n";
echo "\n";
echo "🎯 PRÓXIMOS PASSOS:\n";
echo "1. Acesse o painel: http://localhost:8000/admin/carla\n";
echo "2. Verifique os novos contextos na tabela\n";
echo "3. Teste com voz: 'rastrear minha entrega', 'mostrar promoções'\n";
echo "4. Treine a Carla: php treinar_com_historico.php\n";
echo "\n";
echo "🚀 Pronto! A Carla agora sabe executar " . AIContext::count() . " ações diferentes!\n";
