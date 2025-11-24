<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Produto;

echo "Atualizando produtos com códigos e tipo de preparo...\n\n";

// Definir códigos e tipos de preparo para cada produto
$produtos_config = [
    'Coca-Cola 350ml' => ['codigo' => 'BEB001', 'tipo_preparo' => 'nao_precisa'],
    'Suco de Laranja' => ['codigo' => 'BEB002', 'tipo_preparo' => 'preparo_rapido'],
    'Água Mineral 500ml' => ['codigo' => 'BEB003', 'tipo_preparo' => 'nao_precisa'],
    'Cerveja Heineken' => ['codigo' => 'BEB004', 'tipo_preparo' => 'nao_precisa'],
    'Hambúrguer Artesanal' => ['codigo' => 'PRA001', 'tipo_preparo' => 'preparo_cozinha'],
    'Pizza Margherita' => ['codigo' => 'PRA002', 'tipo_preparo' => 'preparo_cozinha'],
    'Filé à Parmegiana' => ['codigo' => 'PRA003', 'tipo_preparo' => 'preparo_cozinha'],
    'Lasanha Bolonhesa' => ['codigo' => 'PRA004', 'tipo_preparo' => 'preparo_cozinha'],
    'Batata Frita' => ['codigo' => 'PET001', 'tipo_preparo' => 'preparo_rapido'],
    'Salada Caesar' => ['codigo' => 'PET002', 'tipo_preparo' => 'preparo_rapido'],
    'Porção de Mandioca' => ['codigo' => 'PET003', 'tipo_preparo' => 'preparo_rapido'],
    'Pudim de Leite' => ['codigo' => 'SOB001', 'tipo_preparo' => 'nao_precisa'],
    'Brigadeiro Gourmet' => ['codigo' => 'SOB002', 'tipo_preparo' => 'nao_precisa'],
    'Torta de Chocolate' => ['codigo' => 'SOB003', 'tipo_preparo' => 'nao_precisa'],
    'Caipirinha' => ['codigo' => 'DRK001', 'tipo_preparo' => 'preparo_rapido'],
    'Mojito' => ['codigo' => 'DRK002', 'tipo_preparo' => 'preparo_rapido']
];

$contador = 0;

foreach ($produtos_config as $nome => $config) {
    $produto = Produto::where('nome', $nome)->first();
    
    if ($produto) {
        $produto->update([
            'codigo' => $config['codigo'],
            'tipo_preparo' => $config['tipo_preparo']
        ]);
        
        echo "✅ {$nome} - Código: {$config['codigo']} - Preparo: {$config['tipo_preparo']}\n";
        $contador++;
    } else {
        echo "❌ Produto '{$nome}' não encontrado\n";
    }
}

echo "\n🎉 {$contador} produtos atualizados com sucesso!\n";

// Mostrar resumo por tipo de preparo
echo "\n📊 RESUMO POR TIPO DE PREPARO:\n";
echo "┌─────────────────────┬─────────┐\n";
echo "│ Tipo                │ Qtd     │\n";
echo "├─────────────────────┼─────────┤\n";

$nao_precisa = Produto::where('tipo_preparo', 'nao_precisa')->count();
$rapido = Produto::where('tipo_preparo', 'preparo_rapido')->count();
$cozinha = Produto::where('tipo_preparo', 'preparo_cozinha')->count();

echo sprintf("│ %-19s │ %7d │\n", "Não precisa preparo", $nao_precisa);
echo sprintf("│ %-19s │ %7d │\n", "Preparo rápido", $rapido);
echo sprintf("│ %-19s │ %7d │\n", "Preparo cozinha", $cozinha);
echo "└─────────────────────┴─────────┘\n";

echo "\n📝 TIPOS DE PREPARO:\n";
echo "• nao_precisa: Produtos prontos (bebidas industrializadas, sobremesas prontas)\n";
echo "• preparo_rapido: Preparo simples e rápido (sucos, drinks, petiscos)\n";
echo "• preparo_cozinha: Requer preparo na cozinha (pratos principais)\n";
