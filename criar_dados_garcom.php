<?php

// Carregamento do Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

// Inicialização do kernel console
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Categoria;
use App\Models\Produto;
use App\Models\Mesa;
use App\Models\Usuario;

echo "🍽️ Criando dados de teste para o Modo Garçom...\n\n";

// Criar categorias
$categorias = [
    ['nome' => 'Hambúrgueres', 'descricao' => 'Deliciosos hambúrgueres artesanais'],
    ['nome' => 'Bebidas', 'descricao' => 'Bebidas geladas e quentes'],
    ['nome' => 'Sobremesas', 'descricao' => 'Doces irresistíveis'],
    ['nome' => 'Pratos Principais', 'descricao' => 'Pratos completos e saborosos']
];

foreach ($categorias as $cat) {
    $categoria = Categoria::firstOrCreate(
        ['nome' => $cat['nome']],
        ['descricao' => $cat['descricao']]
    );
    echo "✅ Categoria: {$categoria->nome}\n";
}

// Criar produtos
$produtos = [    [
        'nome' => 'Hambúrguer Clássico',
        'descricao' => 'Pão, carne 120g, queijo, alface, tomate',
        'preco' => 18.90,
        'categoria' => 'Hambúrgueres',
        'ativo' => true
    ],
    [
        'nome' => 'X-Bacon',
        'descricao' => 'Hambúrguer com bacon crocante',
        'preco' => 22.90,
        'categoria' => 'Hambúrgueres',
        'ativo' => true
    ],
    [
        'nome' => 'Coca-Cola 350ml',
        'descricao' => 'Refrigerante gelado',
        'preco' => 4.50,
        'categoria' => 'Bebidas',
        'ativo' => true
    ],
    [
        'nome' => 'Suco de Laranja',
        'descricao' => 'Suco natural de laranja',
        'preco' => 6.00,
        'categoria' => 'Bebidas',
        'ativo' => true
    ],
    [
        'nome' => 'Pudim de Leite',
        'descricao' => 'Sobremesa caseira',
        'preco' => 8.90,
        'categoria' => 'Sobremesas',
        'ativo' => true
    ],
    [
        'nome' => 'Filé à Parmegiana',
        'descricao' => 'Filé empanado com molho e queijo',
        'preco' => 32.90,
        'categoria' => 'Pratos Principais',
        'ativo' => true
    ]
];

foreach ($produtos as $prod) {
    $categoria = Categoria::where('nome', $prod['categoria'])->first();
    if ($categoria) {
        $produto = Produto::firstOrCreate(
            ['nome' => $prod['nome']],
            [                'descricao' => $prod['descricao'],
                'preco' => $prod['preco'],
                'categoria_id' => $categoria->id,
                'ativo' => $prod['ativo'] ?? true,
                'ativo' => true
            ]
        );
        echo "✅ Produto: {$produto->nome} - R$ {$produto->preco}\n";
    }
}

// Criar mesas
for ($i = 1; $i <= 10; $i++) {
    $mesa = Mesa::firstOrCreate(
        ['identificador' => "Mesa {$i}"],
        [
            'lugares' => rand(2, 6)
        ]
    );
    echo "✅ Mesa {$mesa->identificador} - Lugares: {$mesa->lugares}\n";
}

// Criar usuário garçom demo
$garcom = Usuario::firstOrCreate(
    ['email' => 'garcom@demo.com'],
    [
        'nome' => 'João Garçom',
        'password' => password_hash('123456', PASSWORD_DEFAULT),
        'role' => 'garcom'
    ]
);
echo "✅ Usuário Garçom: {$garcom->nome} - Email: {$garcom->email}\n";

echo "\n🎉 Dados de teste criados com sucesso!\n";
echo "👤 Login do Garçom: garcom@demo.com / 123456\n";
echo "🔗 Acesse: http://localhost:8000/garcom/dashboard\n";
?>
