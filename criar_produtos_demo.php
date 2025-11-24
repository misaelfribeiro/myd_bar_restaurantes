<?php

/**
 * Script para criar produtos de demonstração
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Categoria;
use App\Models\Produto;

echo "==========================================\n";
echo "CRIANDO PRODUTOS DE DEMONSTRAÇÃO\n";
echo "==========================================\n\n";

// Criar categorias
$categorias = [
    ['nome' => 'Bebidas', 'descricao' => 'Bebidas diversas'],
    ['nome' => 'Lanches', 'descricao' => 'Lanches e petiscos'],
    ['nome' => 'Pratos Principais', 'descricao' => 'Refeições completas'],
    ['nome' => 'Sobremesas', 'descricao' => 'Doces e sobremesas'],
    ['nome' => 'Cervejas', 'descricao' => 'Cervejas artesanais e tradicionais'],
];

foreach ($categorias as $catData) {
    $cat = Categoria::firstOrCreate(
        ['nome' => $catData['nome']], 
        $catData
    );
    echo "✓ Categoria: {$cat->nome}\n";
}

echo "\n";

// Criar produtos
$produtos = [
    // Bebidas
    ['nome' => 'Água Mineral', 'descricao' => 'Água mineral sem gás 500ml', 'preco' => 3.00, 'categoria' => 'Bebidas', 'disponivel' => 1],
    ['nome' => 'Refrigerante Lata', 'descricao' => 'Coca-Cola, Guaraná, Fanta 350ml', 'preco' => 5.00, 'categoria' => 'Bebidas', 'disponivel' => 1],
    ['nome' => 'Suco Natural', 'descricao' => 'Laranja, limão ou morango 500ml', 'preco' => 8.00, 'categoria' => 'Bebidas', 'disponivel' => 1],
    ['nome' => 'Café Expresso', 'descricao' => 'Café expresso tradicional', 'preco' => 4.00, 'categoria' => 'Bebidas', 'disponivel' => 1],
    
    // Cervejas
    ['nome' => 'Cerveja Pilsen', 'descricao' => 'Cerveja pilsen gelada 350ml', 'preco' => 6.00, 'categoria' => 'Cervejas', 'disponivel' => 1],
    ['nome' => 'Cerveja IPA', 'descricao' => 'Indian Pale Ale 500ml', 'preco' => 12.00, 'categoria' => 'Cervejas', 'disponivel' => 1],
    ['nome' => 'Cerveja Weiss', 'descricao' => 'Cerveja de trigo 500ml', 'preco' => 10.00, 'categoria' => 'Cervejas', 'disponivel' => 1],
    
    // Lanches
    ['nome' => 'Hambúrguer Artesanal', 'descricao' => 'Pão brioche, 180g de carne, queijo, alface e tomate', 'preco' => 25.00, 'categoria' => 'Lanches', 'disponivel' => 1],
    ['nome' => 'Batata Frita', 'descricao' => 'Porção de batata frita crocante 400g', 'preco' => 15.00, 'categoria' => 'Lanches', 'disponivel' => 1],
    ['nome' => 'Pastel Misto', 'descricao' => 'Pastel de queijo e presunto', 'preco' => 8.00, 'categoria' => 'Lanches', 'disponivel' => 1],
    ['nome' => 'Coxinha de Frango', 'descricao' => 'Porção com 6 unidades', 'preco' => 12.00, 'categoria' => 'Lanches', 'disponivel' => 1],
    
    // Pratos Principais
    ['nome' => 'Filé à Parmegiana', 'descricao' => 'Filé bovino empanado com molho e queijo, acompanha arroz, batata e salada', 'preco' => 45.00, 'categoria' => 'Pratos Principais', 'disponivel' => 1],
    ['nome' => 'Feijoada Completa', 'descricao' => 'Feijoada tradicional com acompanhamentos', 'preco' => 38.00, 'categoria' => 'Pratos Principais', 'disponivel' => 1],
    ['nome' => 'Picanha na Chapa', 'descricao' => '300g de picanha grelhada com arroz, farofa e vinagrete', 'preco' => 55.00, 'categoria' => 'Pratos Principais', 'disponivel' => 1],
    
    // Sobremesas
    ['nome' => 'Pudim de Leite', 'descricao' => 'Pudim caseiro com calda de caramelo', 'preco' => 10.00, 'categoria' => 'Sobremesas', 'disponivel' => 1],
    ['nome' => 'Petit Gateau', 'descricao' => 'Bolo de chocolate com sorvete', 'preco' => 18.00, 'categoria' => 'Sobremesas', 'disponivel' => 1],
    ['nome' => 'Sorvete', 'descricao' => 'Duas bolas de sorvete sabor a escolher', 'preco' => 12.00, 'categoria' => 'Sobremesas', 'disponivel' => 1],
];

foreach ($produtos as $prodData) {
    $categoria = Categoria::where('nome', $prodData['categoria'])->first();
    
    if ($categoria) {
        $produto = Produto::firstOrCreate(
            ['nome' => $prodData['nome']],
            [
                'descricao' => $prodData['descricao'],
                'preco' => $prodData['preco'],
                'categoria_id' => $categoria->id,
                'disponivel' => $prodData['disponivel']
            ]
        );
        
        echo "✓ Produto: {$produto->nome} - R$ " . number_format($produto->preco, 2, ',', '.') . "\n";
    }
}

echo "\n==========================================\n";
echo "RESUMO:\n";
echo "==========================================\n";
echo "Categorias: " . Categoria::count() . "\n";
echo "Produtos: " . Produto::count() . "\n";
echo "Produtos disponíveis: " . Produto::where('disponivel', 1)->count() . "\n";
echo "\n✅ CONCLUÍDO!\n";
echo "==========================================\n";
