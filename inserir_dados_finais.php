<?php

use App\Models\Mesa;
use App\Models\Categoria;
use App\Models\Produto;

echo "Inserindo dados...\n";

// Inserir mesas
for ($i = 1; $i <= 10; $i++) {
    Mesa::create([
        'numero' => $i,
        'capacidade' => rand(2, 6),
        'status' => 'disponivel',
        'disponivel' => true
    ]);
}
echo "✅ 10 mesas criadas\n";

// Inserir categorias
$categorias = [
    ['nome' => 'Bebidas', 'descricao' => 'Drinks, sucos e refrigerantes'],
    ['nome' => 'Lanches', 'descricao' => 'Hambúrguers, sanduíches e petiscos'],
    ['nome' => 'Pratos Principais', 'descricao' => 'Pratos executivos e refeições'],
    ['nome' => 'Sobremesas', 'descricao' => 'Doces e sobremesas variadas'],
    ['nome' => 'Entradas', 'descricao' => 'Aperitivos e entradas']
];

foreach ($categorias as $categoria) {
    $categoria['ativa'] = true;
    Categoria::create($categoria);
}
echo "✅ 5 categorias criadas\n";

// Inserir produtos
$produtos = [
    ['nome' => 'Hambúrguer Artesanal', 'preco' => 25.90, 'categoria' => 'Lanches', 'descricao' => 'Pão artesanal, carne 150g, queijo, alface e tomate'],
    ['nome' => 'Pizza Margherita', 'preco' => 32.50, 'categoria' => 'Pratos Principais', 'descricao' => 'Molho de tomate, mussarela e manjericão'],
    ['nome' => 'Coca-Cola 350ml', 'preco' => 5.50, 'categoria' => 'Bebidas', 'descricao' => 'Refrigerante gelado'],
    ['nome' => 'Suco de Laranja', 'preco' => 8.00, 'categoria' => 'Bebidas', 'descricao' => 'Suco natural da fruta'],
    ['nome' => 'Batata Frita', 'preco' => 12.00, 'categoria' => 'Entradas', 'descricao' => 'Porção de batata frita crocante'],
    ['nome' => 'Cerveja Heineken', 'preco' => 8.50, 'categoria' => 'Bebidas', 'descricao' => 'Cerveja long neck gelada'],
    ['nome' => 'Pudim de Leite', 'preco' => 9.90, 'categoria' => 'Sobremesas', 'descricao' => 'Pudim caseiro com calda'],
    ['nome' => 'Filé à Parmegiana', 'preco' => 45.00, 'categoria' => 'Pratos Principais', 'descricao' => 'Filé empanado com molho e queijo'],
    ['nome' => 'Salada Caesar', 'preco' => 18.50, 'categoria' => 'Entradas', 'descricao' => 'Alface, croutons, parmesão e molho caesar'],
    ['nome' => 'Água Mineral', 'preco' => 3.00, 'categoria' => 'Bebidas', 'descricao' => 'Água sem gás 500ml']
];

foreach ($produtos as $produto) {
    $produto['ativo'] = true;
    Produto::create($produto);
}
echo "✅ 10 produtos criados\n";

echo "✅ Dados inseridos com sucesso!\n";
