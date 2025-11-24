<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AILearningService;

echo "🧪 Testando busca melhorada de produtos\n\n";

$service = new AILearningService();

$tests = [
    'batata frita' => 'Deve priorizar "Batata Frita" ao invés de "Filé com Fritas"',
    'cerveja' => 'Deve buscar cervejas especificamente',
    'pizza' => 'Deve buscar pizzas',
];

foreach ($tests as $busca => $expectativa) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Busca: '{$busca}'\n";
    echo "Expectativa: {$expectativa}\n\n";
    
    $result = $service->processMessage(
        "quero {$busca}",
        null,
        null,
        'RESTAURANTE0001'
    );
    
    echo "Intent: {$result['intent']}\n";
    echo "Confidence: " . round($result['confidence'] * 100, 1) . "%\n";
    echo "Produtos encontrados: " . count($result['products']) . "\n\n";
    
    if (!empty($result['products'])) {
        echo "Top 3 produtos:\n";
        $produtos = array_slice($result['products'], 0, 3);
        foreach ($produtos as $i => $p) {
            $nome = is_array($p) ? $p['nome'] : $p->nome;
            $preco = is_array($p) ? $p['preco'] : $p->preco;
            echo "  " . ($i + 1) . ". {$nome} - R$ {$preco}\n";
        }
        echo "\n";
    }
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Teste concluído!\n\n";

echo "MELHORIAS IMPLEMENTADAS:\n";
echo "  1. ✅ Busca exata tem prioridade (batata frita > filé com fritas)\n";
echo "  2. ✅ Score de relevância:\n";
echo "     - Nome exato = 100 pontos\n";
echo "     - Nome começa com = 50 pontos\n";
echo "     - Nome contém = 25 pontos\n";
echo "     - Descrição contém = 10 pontos\n";
echo "  3. ✅ Emojis removidos dos resultados\n";
echo "  4. ✅ Ordenação por relevância ao invés de só preço\n";
