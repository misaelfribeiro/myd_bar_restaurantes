<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AIContext;
use Illuminate\Support\Facades\DB;

echo "🤖 Adicionando contextos avançados para a Carla...\n\n";

$novosContextos = [
    // SAUDAÇÕES AVANÇADAS
    [
        'category' => 'greeting',
        'key' => 'greeting_morning',
        'pattern' => '*(bom dia|bom* dia)*',
        'response_template' => 'Bom dia! ☀️ Sou a Carla! Como posso te ajudar hoje?',
        'action' => '',
        'confidence_threshold' => 0.8,
        'active' => true
    ],
    [
        'category' => 'greeting',
        'key' => 'greeting_afternoon',
        'pattern' => '*(boa tarde)*',
        'response_template' => 'Boa tarde! 🌤️ Sou a Carla! O que vai querer hoje?',
        'action' => '',
        'confidence_threshold' => 0.8,
        'active' => true
    ],
    [
        'category' => 'greeting',
        'key' => 'greeting_night',
        'pattern' => '*(boa noite)*',
        'response_template' => 'Boa noite! 🌙 Sou a Carla! Pronto para fazer seu pedido?',
        'action' => '',
        'confidence_threshold' => 0.8,
        'active' => true
    ],
    [
        'category' => 'greeting',
        'key' => 'greeting_como_vai',
        'pattern' => '*(como (vai|está|esta|vc|você))*|(tudo bem)*',
        'response_template' => 'Estou ótima! 😊 Obrigada por perguntar! E você, o que vai querer hoje?',
        'action' => '',
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    
    // DESPEDIDAS
    [
        'category' => 'greeting',
        'key' => 'goodbye',
        'pattern' => '*(tchau|adeus|até|falou|flw|vlw)*',
        'response_template' => 'Até logo! 👋 Foi um prazer te atender! Volte sempre! 😊',
        'action' => '',
        'confidence_threshold' => 0.8,
        'active' => true
    ],
    [
        'category' => 'greeting',
        'key' => 'goodbye_thanks',
        'pattern' => '*(obrigad|obrigada|valeu)*',
        'response_template' => 'Por nada! 🥰 Fico feliz em ajudar! Até a próxima! 👋',
        'action' => '',
        'confidence_threshold' => 0.8,
        'active' => true
    ],

    // BUSCA DE PRODUTOS ESPECÍFICOS
    [
        'category' => 'search',
        'key' => 'search_refrigerante',
        'pattern' => '*(refri|refrigerante|coca|pepsi|guaraná|fanta)*',
        'response_template' => 'Temos vários refrigerantes! 🥤 Coca-Cola, Guaraná, Fanta... Qual prefere?',
        'action' => 'searchProduct',
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    [
        'category' => 'search',
        'key' => 'search_suco',
        'pattern' => '*(suco|natural)*',
        'response_template' => 'Nossos sucos naturais são deliciosos! 🍹 Laranja, limão, morango... Qual quer?',
        'action' => 'searchProduct',
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    [
        'category' => 'search',
        'key' => 'search_lanche',
        'pattern' => '*(lanche|sanduiche|sanduíche|x-burger|xburguer)*',
        'response_template' => 'Nossos lanches são top! 🍔 X-Burger, X-Salada, X-Bacon... Qual vai querer?',
        'action' => 'searchProduct',
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    [
        'category' => 'search',
        'key' => 'search_batata',
        'pattern' => '*(batata|fritas)*',
        'response_template' => 'Batata frita sequinha e crocante! 🍟 Pequena, média ou grande?',
        'action' => 'searchProduct',
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    [
        'category' => 'search',
        'key' => 'search_pastel',
        'pattern' => '*(pastel|pastéis)*',
        'response_template' => 'Nossos pastéis são uma delícia! 🥟 Carne, queijo, frango... Qual sabor?',
        'action' => 'searchProduct',
        'confidence_threshold' => 0.7,
        'active' => true
    ],

    // CATEGORIAS
    [
        'category' => 'menu',
        'key' => 'show_desserts',
        'pattern' => '*(sobremesa|doce|açaí|sorvete|pudim|bolo)*',
        'response_template' => 'Nossas sobremesas são irresistíveis! 🍰 Açaí, sorvete, pudim... Qual vai querer?',
        'action' => 'filterByCategory',
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    [
        'category' => 'menu',
        'key' => 'show_appetizers',
        'pattern' => '*(entrada|petisco|porção|aperitivo)*',
        'response_template' => 'Temos petiscos deliciosos! 🍗 Porção de fritas, onion rings, nuggets... O que prefere?',
        'action' => 'filterByCategory',
        'confidence_threshold' => 0.7,
        'active' => true
    ],

    // PERGUNTAS SOBRE PREÇO
    [
        'category' => 'info',
        'key' => 'ask_price',
        'pattern' => '*(quanto (custa|é|vale)|preço|valor)*',
        'response_template' => 'Me diz qual produto você quer e eu te falo o preço! 💰 Ou posso mostrar o cardápio completo.',
        'action' => 'showMenu',
        'confidence_threshold' => 0.6,
        'active' => true
    ],

    // HORÁRIOS E FUNCIONAMENTO
    [
        'category' => 'info',
        'key' => 'ask_opening_hours',
        'pattern' => '*(horário|abre|fecha|funciona|aberto)*',
        'response_template' => 'Estamos abertos todos os dias! 🕐 Segunda a domingo, das 11h às 23h!',
        'action' => '',
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    [
        'category' => 'info',
        'key' => 'ask_delivery_time',
        'pattern' => '*(quanto tempo|demora|prazo|entrega)*',
        'response_template' => 'O tempo de entrega é de 30 a 45 minutos! 🚚⏱️ Depende da sua localização.',
        'action' => 'deliveryTime',
        'confidence_threshold' => 0.7,
        'active' => true
    ],

    // CARRINHO - AÇÕES ESPECÍFICAS
    [
        'category' => 'cart',
        'key' => 'empty_cart',
        'pattern' => '*(limpa|limpar|esvazia|esvaziar|remove tudo) (carrinho|pedido)*',
        'response_template' => 'Quer mesmo limpar o carrinho? 🗑️ Todos os itens serão removidos!',
        'action' => 'clearCart',
        'confidence_threshold' => 0.8,
        'active' => true
    ],
    [
        'category' => 'cart',
        'key' => 'add_more',
        'pattern' => '*(adiciona|coloca|põe|mais|quero mais)*',
        'response_template' => 'Claro! O que mais vai querer? 🛒 Me diz o produto!',
        'action' => 'showMenu',
        'confidence_threshold' => 0.6,
        'active' => true
    ],

    // PAGAMENTO - FORMAS
    [
        'category' => 'payment',
        'key' => 'pay_with_card',
        'pattern' => '*(cartão|card|crédito|débito)*',
        'response_template' => 'Aceitamos cartão de crédito e débito! 💳 Visa, Master, Elo...',
        'action' => 'paymentMethods',
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    [
        'category' => 'payment',
        'key' => 'pay_with_money',
        'pattern' => '*(dinheiro|cash|espécie)*',
        'response_template' => 'Aceitamos dinheiro sim! 💵 Precisa de troco?',
        'action' => 'paymentMethods',
        'confidence_threshold' => 0.7,
        'active' => true
    ],

    // AVALIAÇÃO E FEEDBACK
    [
        'category' => 'feedback',
        'key' => 'compliment',
        'pattern' => '*(muito bom|maravilhoso|excelente|ótimo|perfeito|top)*',
        'response_template' => 'Que legal! 🥰 Fico muito feliz que tenha gostado! Sua opinião é muito importante!',
        'action' => '',
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    [
        'category' => 'feedback',
        'key' => 'complaint',
        'pattern' => '*(ruim|péssimo|horrível|demorou|atrasou|problema)*',
        'response_template' => 'Sinto muito! 😔 Pode me contar o que aconteceu? Vou registrar para melhorarmos!',
        'action' => 'reportProblem',
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    [
        'category' => 'feedback',
        'key' => 'rate_service',
        'pattern' => '*(avaliar|avaliação|nota|estrela)*',
        'response_template' => 'Adoraria sua avaliação! ⭐ De 1 a 5 estrelas, qual nota você daria?',
        'action' => 'rateService',
        'confidence_threshold' => 0.7,
        'active' => true
    ],

    // PROMOÇÕES E CUPONS
    [
        'category' => 'promotion',
        'key' => 'ask_coupon',
        'pattern' => '*(cupom|desconto|promoção|oferta)*',
        'response_template' => 'Temos promoções incríveis! 🎉 Digite seu cupom ou veja as ofertas do dia!',
        'action' => 'showPromotions',
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    [
        'category' => 'promotion',
        'key' => 'ask_combo',
        'pattern' => '*(combo|kit|pacote|promoção)*',
        'response_template' => 'Nossos combos são super em conta! 🔥 Hambúrguer + Batata + Refri por um preço especial!',
        'action' => 'showCombos',
        'confidence_threshold' => 0.7,
        'active' => true
    ],

    // ENDEREÇO E LOCALIZAÇÃO
    [
        'category' => 'delivery',
        'key' => 'ask_delivery_area',
        'pattern' => '*(entrega|área|região|bairro|atende)*',
        'response_template' => 'Entregamos em vários bairros! 📍 Me diz seu endereço que verifico se atendemos sua região.',
        'action' => 'checkDeliveryArea',
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    [
        'category' => 'delivery',
        'key' => 'ask_delivery_fee',
        'pattern' => '*(taxa|frete|custo|cobr)*(entrega)*',
        'response_template' => 'A taxa de entrega varia de R$ 3 a R$ 8, dependendo do bairro! 🚚💰',
        'action' => 'checkDeliveryFee',
        'confidence_threshold' => 0.7,
        'active' => true
    ],

    // AJUDA E INFORMAÇÕES
    [
        'category' => 'help',
        'key' => 'ask_help',
        'pattern' => '*(ajuda|help|socorro|não entendi|como)*',
        'response_template' => 'Estou aqui pra ajudar! 🙋‍♀️ Você pode: ver o cardápio, fazer pedido, rastrear entrega, falar com suporte...',
        'action' => 'showHelp',
        'confidence_threshold' => 0.6,
        'active' => true
    ],
    [
        'category' => 'help',
        'key' => 'contact_whatsapp',
        'pattern' => '*(whatsapp|whats|zap|telefone|ligar|contato)*',
        'response_template' => 'Nosso WhatsApp é (11) 98765-4321! 📱 Estamos disponíveis para te ajudar!',
        'action' => 'contactSupport',
        'confidence_threshold' => 0.7,
        'active' => true
    ],

    // ALERGIAS E RESTRIÇÕES
    [
        'category' => 'info',
        'key' => 'ask_vegetarian',
        'pattern' => '*(vegetarian|vegano|vegan|sem carne)*',
        'response_template' => 'Temos opções vegetarianas sim! 🥗 Pizza de legumes, saladas, sucos naturais...',
        'action' => 'filterVegetarian',
        'confidence_threshold' => 0.7,
        'active' => true
    ],
    [
        'category' => 'info',
        'key' => 'ask_allergy',
        'pattern' => '*(alergia|alérgico|intolerante|glúten|lactose)*',
        'response_template' => 'Importante! 🚨 Me conta sua restrição que indico os produtos adequados pra você.',
        'action' => 'showAllergyInfo',
        'confidence_threshold' => 0.7,
        'active' => true
    ],
];

$added = 0;
$skipped = 0;

foreach ($novosContextos as $contexto) {
    $exists = AIContext::where('key', $contexto['key'])->exists();
    
    if ($exists) {
        echo "⏭️  '{$contexto['key']}' já existe\n";
        $skipped++;
        continue;
    }
    
    AIContext::create($contexto);
    echo "✅ Adicionado: {$contexto['key']} - {$contexto['response_template']}\n";
    $added++;
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ {$added} novos contextos adicionados!\n";
echo "⏭️  {$skipped} contextos já existiam\n";
echo "📊 Total de contextos: " . AIContext::count() . "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n🤖 Carla agora está muito mais inteligente!\n";
