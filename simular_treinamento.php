<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AILearningService;

echo "🧪 Simulador de Treinamento da Carla\n";
echo "====================================\n\n";

$aiService = new AILearningService();

// Simulação de conversas
$conversas = [
    // Saudações
    ['oi', 'greeting_hello'],
    ['olá carla', 'greeting_hello'],
    ['bom dia', 'greeting_hello'],
    ['tudo bem?', 'greeting_howru'],
    ['obrigado', 'greeting_thanks'],
    ['valeu', 'greeting_thanks'],
    
    // Busca de produtos
    ['quero uma pizza', 'search_pizza'],
    ['tem hamburguer?', 'search_hamburguer'],
    ['quero uma bebida', 'search_bebida'],
    ['tem sorvete?', 'search_sobremesa'],
    ['opções vegetarianas', 'search_vegetariano'],
    
    // Cardápio
    ['mostra o cardápio', 'show_menu'],
    ['o que vocês tem?', 'show_menu'],
    ['quero ver o menu', 'show_menu'],
    
    // Carrinho
    ['meu carrinho', 'show_cart'],
    ['ver sacola', 'show_cart'],
    
    // Pedidos
    ['meus pedidos', 'show_orders'],
    ['status do pedido', 'order_status'],
    ['onde está minha entrega', 'delivery_status'],
    
    // Info
    ['horário de funcionamento', 'opening_hours'],
    ['como entrar em contato', 'contact'],
    ['tem desconto?', 'ask_discount'],
];

echo "📊 Processando " . count($conversas) . " conversas simuladas...\n\n";

$acertos = 0;
$erros = 0;

foreach ($conversas as $i => $conversa) {
    [$mensagem, $intentEsperada] = $conversa;
    
    try {
        $resultado = $aiService->processMessage($mensagem, null, null);
        
        $acertou = ($resultado['intent'] === $intentEsperada);
        $icone = $acertou ? '✅' : '❌';
        $acertos += $acertou ? 1 : 0;
        $erros += $acertou ? 0 : 1;
        
        $num = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
        $conf = round($resultado['confidence'] * 100);
        
        echo "$num. $icone \"$mensagem\"\n";
        echo "    Esperava: $intentEsperada | Detectou: {$resultado['intent']} ($conf%)\n";
        
        if (!$acertou) {
            echo "    ⚠️ Resposta: " . substr($resultado['response'], 0, 50) . "...\n";
        }
        
        echo "\n";
        
    } catch (\Exception $e) {
        echo "$i. ❌ ERRO: {$e->getMessage()}\n\n";
        $erros++;
    }
}

$taxa = round(($acertos / count($conversas)) * 100, 1);
$nivel = $taxa >= 90 ? 'EXCELENTE' : ($taxa >= 75 ? 'BOA' : ($taxa >= 60 ? 'REGULAR' : 'PRECISA MELHORAR'));

echo "====================================\n";
echo "📈 RESULTADO:\n";
echo "   ✅ Acertos: $acertos\n";
echo "   ❌ Erros: $erros\n";
echo "   📊 Taxa de acerto: $taxa%\n";
echo "   🎯 Nível: $nivel\n\n";

if ($taxa < 90) {
    echo "💡 DICA: Execute 'php treinar_com_historico.php' para melhorar!\n";
}
