<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AIContext;

class AIKnowledgeSeeder extends Seeder
{
    /**
     * Seed the application's database with AI knowledge.
     *
     * @return void
     */
    public function run()
    {
        $contexts = [
            // ========== SAUDAÇÕES ==========
            [
                'category' => 'greeting',
                'key' => 'greeting_hello',
                'pattern' => '(oi|olá|ola|hey|ei|bom dia|boa tarde|boa noite)',
                'response_template' => 'Olá! Eu sou a Carla, assistente virtual da EatsFood! Como posso te ajudar hoje? Quer ver nosso cardápio?',
                'action' => null,
                'confidence_threshold' => 0.7,
            ],
            [
                'category' => 'greeting',
                'key' => 'greeting_howru',
                'pattern' => '(tudo bem|como vai|tudo certo)',
                'response_template' => 'Tudo ótimo! Sou a Carla e estou aqui pra te atender! Tá com fome? Posso te mostrar nossas delícias da EatsFood!',
                'action' => null,
                'confidence_threshold' => 0.7,
            ],
            [
                'category' => 'greeting',
                'key' => 'who_are_you',
                'pattern' => '*(quem é você|qual seu nome|como se chama|quem és)*',
                'response_template' => 'Me chamo Carla! Sou a assistente virtual da EatsFood, criada especialmente para te ajudar com pedidos, cardápio e muito mais!',
                'action' => null,
                'confidence_threshold' => 0.8,
            ],

            // ========== CARDÁPIO ==========
            [
                'category' => 'menu',
                'key' => 'show_menu',
                'pattern' => '*(cardápio|cardapio|menu|produtos|o que tem|opções|opcoes)*',
                'response_template' => 'Vou abrir nosso cardápio completo pra você! Temos pizzas, lanches, bebidas e muito mais!',
                'action' => 'showMenu',
                'confidence_threshold' => 0.6,
            ],
            [
                'category' => 'menu',
                'key' => 'show_categories',
                'pattern' => '*(categorias|tipos|o que voce tem|que tipo)*',
                'response_template' => 'Temos várias categorias: Pizzas, Hambúrgueres, Bebidas, Sobremesas e muito mais! O que te interessa?',
                'action' => 'showCategories',
                'confidence_threshold' => 0.6,
            ],

            // ========== BUSCA DE PRODUTOS ==========
            [
                'category' => 'search',
                'key' => 'search_pizza',
                'pattern' => '*(pizza)*',
                'response_template' => 'Perfeito! Vou procurar nossas pizzas para você!',
                'action' => 'searchProduct',
                'parameters' => ['query' => 'pizza'],
                'confidence_threshold' => 0.6,
            ],
            [
                'category' => 'search',
                'key' => 'search_hamburguer',
                'pattern' => '*(hamburguer|hambúrguer|burger|lanche)*',
                'response_template' => 'Boa escolha! Nossos hambúrgueres são incríveis! Vou mostrar!',
                'action' => 'searchProduct',
                'parameters' => ['query' => 'hamburguer'],
                'confidence_threshold' => 0.6,
            ],
            [
                'category' => 'search',
                'key' => 'search_bebida',
                'pattern' => '*(bebida|suco|refrigerante|coca|guarana|agua|água|cerveja)*',
                'response_template' => 'Certo! Vou mostrar nossas bebidas!',
                'action' => 'searchProduct',
                'parameters' => ['query' => 'bebida'],
                'confidence_threshold' => 0.6,
            ],
            [
                'category' => 'search',
                'key' => 'search_sanduiche',
                'pattern' => '*(sanduíche|sanduiche|sandwich)*',
                'response_template' => 'Temos ótimos sanduíches! Olha só!',
                'action' => 'searchProduct',
                'parameters' => ['query' => 'sanduiche'],
                'confidence_threshold' => 0.6,
            ],

            // ========== CARRINHO ==========
            [
                'category' => 'cart',
                'key' => 'show_cart',
                'pattern' => '*(carrinho|sacola|cesta|meu pedido)*',
                'response_template' => 'Vou mostrar seu carrinho!',
                'action' => 'showCart',
                'confidence_threshold' => 0.7,
            ],
            [
                'category' => 'cart',
                'key' => 'clear_cart',
                'pattern' => '*(limpar carrinho|esvaziar carrinho|remover tudo)*',
                'response_template' => 'Ok, vou limpar seu carrinho!',
                'action' => 'clearCart',
                'confidence_threshold' => 0.8,
            ],

            // ========== PEDIDOS ==========
            [
                'category' => 'orders',
                'key' => 'show_orders',
                'pattern' => '*(meus pedidos|pedidos|histórico|historico)*',
                'response_template' => 'Vou consultar seus pedidos!',
                'action' => 'showOrders',
                'confidence_threshold' => 0.7,
            ],
            [
                'category' => 'orders',
                'key' => 'order_status',
                'pattern' => '*(status|onde está|cadê|acompanhar|rastrear)*pedido*',
                'response_template' => 'Vou verificar o status do seu pedido!',
                'action' => 'showOrderStatus',
                'confidence_threshold' => 0.7,
            ],

            // ========== ENTREGA ==========
            [
                'category' => 'delivery',
                'key' => 'delivery_status',
                'pattern' => '*(entrega|entregador|delivery|onde está meu pedido)*',
                'response_template' => 'Vou verificar sua entrega!',
                'action' => 'showDeliveryStatus',
                'confidence_threshold' => 0.7,
            ],

            // ========== PREÇOS ==========
            [
                'category' => 'price',
                'key' => 'ask_price',
                'pattern' => '*(quanto custa|qual o preço|qual valor|preço)*',
                'response_template' => 'Para ver os preços, é só olhar o cardápio! Posso abrir pra você?',
                'action' => 'showMenu',
                'confidence_threshold' => 0.6,
            ],

            // ========== PAGAMENTO ==========
            [
                'category' => 'payment',
                'key' => 'payment_methods',
                'pattern' => '*(como pagar|forma de pagamento|pagamento|cartão|dinheiro|pix)*',
                'response_template' => 'Aceitamos Cartão, Dinheiro e PIX! Você escolhe na hora de finalizar o pedido.',
                'action' => null,
                'confidence_threshold' => 0.7,
            ],

            // ========== AJUDA ==========
            [
                'category' => 'help',
                'key' => 'help',
                'pattern' => '*(ajuda|help|não entendi|como funciona)*',
                'response_template' => 'Sou a Carla, da EatsFood! Posso te ajudar de várias formas: "quero pizza", "mostra o cardápio", "meus pedidos", ou "status da entrega". O que você gostaria?',
                'action' => null,
                'confidence_threshold' => 0.6,
            ],

            // ========== FINALIZAR PEDIDO ==========
            [
                'category' => 'checkout',
                'key' => 'checkout',
                'pattern' => '*(finalizar|concluir|fechar|confirmar pedido|quero pedir)*',
                'response_template' => 'Vou te levar para finalizar seu pedido!',
                'action' => 'checkout',
                'confidence_threshold' => 0.7,
            ],

            // ========== PROMOÇÕES ==========
            [
                'category' => 'promotion',
                'key' => 'show_promotions',
                'pattern' => '*(promoção|promoções|promocao|promocoes|oferta|desconto)*',
                'response_template' => 'Vou mostrar nossas promoções!',
                'action' => 'showPromotions',
                'confidence_threshold' => 0.7,
            ],

            // ========== COMBOS ==========
            [
                'category' => 'combo',
                'key' => 'show_combos',
                'pattern' => '*(combo|combinação|kit)*',
                'response_template' => 'Temos combos incríveis! Vou mostrar!',
                'action' => 'showCombos',
                'confidence_threshold' => 0.7,
            ],

            // ========== DESTAQUES ==========
            [
                'category' => 'highlight',
                'key' => 'show_highlights',
                'pattern' => '*(destaque|mais vendido|popular|recomendação|recomendacao|favorito)*',
                'response_template' => 'Vou te mostrar nossos destaques e mais vendidos!',
                'action' => 'showHighlights',
                'confidence_threshold' => 0.6,
            ],

            // ========== ADICIONAR AO CARRINHO ==========
            [
                'category' => 'cart',
                'key' => 'add_to_cart_generic',
                'pattern' => '*(adicionar|quero|colocar|pedir)*',
                'response_template' => 'Beleza! O que você quer adicionar no carrinho? Pode me dizer o nome do produto.',
                'action' => null,
                'confidence_threshold' => 0.5,
            ],

            // ========== HORÁRIO DE FUNCIONAMENTO ==========
            [
                'category' => 'info',
                'key' => 'opening_hours',
                'pattern' => '*(horário|funcionamento|abre|fecha|aberto)*',
                'response_template' => 'A EatsFood está aberta de segunda a domingo das 18h às 23h! Pode pedir à vontade!',
                'action' => null,
                'confidence_threshold' => 0.7,
            ],

            // ========== ENDEREÇO ==========
            [
                'category' => 'info',
                'key' => 'address',
                'pattern' => '*(endereço|endereco|onde fica|localização|localizacao)*',
                'response_template' => 'Você pode consultar nosso endereço no app! Fazemos delivery para toda a cidade.',
                'action' => null,
                'confidence_threshold' => 0.7,
            ],
        ];

        // Insere os contextos
        foreach ($contexts as $context) {
            AIContext::updateOrCreate(
                ['key' => $context['key']],
                $context
            );
        }

        $this->command->info('✅ ' . count($contexts) . ' contextos de IA cadastrados!');
    }
}
