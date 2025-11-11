<?php
// Script para criar produtos de teste com códigos e tipos de preparo

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Configurar conexão com banco
$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'myd_bar_restaurantes',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    echo "🚀 Adicionando produtos com códigos e tipos de preparo...\n\n";
    
    // Verificar se as categorias existem
    $categorias = DB::table('categorias')->get();
    
    if ($categorias->isEmpty()) {
        echo "❌ Nenhuma categoria encontrada! Criando categorias básicas...\n";
        
        $categoriaIds = [
            'bebidas' => DB::table('categorias')->insertGetId(['nome' => 'Bebidas', 'created_at' => now(), 'updated_at' => now()]),
            'pratos' => DB::table('categorias')->insertGetId(['nome' => 'Pratos Principais', 'created_at' => now(), 'updated_at' => now()]),
            'lanches' => DB::table('categorias')->insertGetId(['nome' => 'Lanches', 'created_at' => now(), 'updated_at' => now()]),
            'sobremesas' => DB::table('categorias')->insertGetId(['nome' => 'Sobremesas', 'created_at' => now(), 'updated_at' => now()])
        ];
    } else {
        $categoriaIds = [
            'bebidas' => $categorias->first(fn($c) => stripos($c->nome, 'bebida') !== false)?->id ?? $categorias->first()->id,
            'pratos' => $categorias->first(fn($c) => stripos($c->nome, 'prato') !== false)?->id ?? $categorias->first()->id,
            'lanches' => $categorias->first(fn($c) => stripos($c->nome, 'lanche') !== false)?->id ?? $categorias->first()->id,
            'sobremesas' => $categorias->first(fn($c) => stripos($c->nome, 'sobremesa') !== false)?->id ?? $categorias->first()->id
        ];
    }
    
    // Produtos para adicionar/atualizar
    $produtos = [
        // Bebidas (pronto)
        [
            'nome' => 'Coca-Cola 350ml',
            'codigo' => 'CC350',
            'descricao' => 'Refrigerante Coca-Cola lata 350ml gelado',
            'preco' => 5.50,
            'categoria_id' => $categoriaIds['bebidas'],
            'tipo_preparo' => 'pronto'
        ],
        [
            'nome' => 'Suco de Laranja Natural',
            'codigo' => 'SLN001',
            'descricao' => 'Suco de laranja natural 300ml',
            'preco' => 8.90,
            'categoria_id' => $categoriaIds['bebidas'],
            'tipo_preparo' => 'preparo'
        ],
        [
            'nome' => 'Cerveja Heineken Long Neck',
            'codigo' => 'HLN330',
            'descricao' => 'Cerveja Heineken Long Neck 330ml gelada',
            'preco' => 12.50,
            'categoria_id' => $categoriaIds['bebidas'],
            'tipo_preparo' => 'pronto'
        ],
        [
            'nome' => 'Água Mineral 500ml',
            'codigo' => 'AM500',
            'descricao' => 'Água mineral sem gás 500ml',
            'preco' => 3.50,
            'categoria_id' => $categoriaIds['bebidas'],
            'tipo_preparo' => 'pronto'
        ],
        
        // Pratos Principais (preparo)
        [
            'nome' => 'Picanha Grelhada',
            'codigo' => 'PG001',
            'descricao' => 'Picanha grelhada com arroz, feijão, farofa e vinagrete',
            'preco' => 45.90,
            'categoria_id' => $categoriaIds['pratos'],
            'tipo_preparo' => 'preparo'
        ],
        [
            'nome' => 'Salmão ao Molho de Maracujá',
            'codigo' => 'SMM002',
            'descricao' => 'Filé de salmão grelhado com molho de maracujá e legumes',
            'preco' => 52.80,
            'categoria_id' => $categoriaIds['pratos'],
            'tipo_preparo' => 'preparo'
        ],
        [
            'nome' => 'Frango à Parmegiana',
            'codigo' => 'FP003',
            'descricao' => 'Filé de frango empanado com molho de tomate e queijo',
            'preco' => 32.90,
            'categoria_id' => $categoriaIds['pratos'],
            'tipo_preparo' => 'preparo'
        ],
        [
            'nome' => 'Risotto de Camarão',
            'codigo' => 'RC004',
            'descricao' => 'Risotto cremoso com camarões frescos e ervas finas',
            'preco' => 48.50,
            'categoria_id' => $categoriaIds['pratos'],
            'tipo_preparo' => 'preparo'
        ],
        
        // Lanches (alguns prontos, alguns de preparo)
        [
            'nome' => 'Hambúrguer Artesanal',
            'codigo' => 'HA101',
            'descricao' => 'Hambúrguer artesanal 180g com queijo, alface e tomate',
            'preco' => 28.90,
            'categoria_id' => $categoriaIds['lanches'],
            'tipo_preparo' => 'preparo'
        ],
        [
            'nome' => 'Sanduíche Natural',
            'codigo' => 'SN102',
            'descricao' => 'Sanduíche natural de peito de peru com cream cheese',
            'preco' => 15.50,
            'categoria_id' => $categoriaIds['lanches'],
            'tipo_preparo' => 'pronto'
        ],
        [
            'nome' => 'Hot Dog Especial',
            'codigo' => 'HD103',
            'descricao' => 'Hot dog com salsicha artesanal, molhos especiais e batata palha',
            'preco' => 18.90,
            'categoria_id' => $categoriaIds['lanches'],
            'tipo_preparo' => 'preparo'
        ],
        
        // Sobremesas (algumas prontas, algumas de preparo)
        [
            'nome' => 'Petit Gateau',
            'codigo' => 'PG201',
            'descricao' => 'Petit gateau de chocolate com sorvete de baunilha',
            'preco' => 22.90,
            'categoria_id' => $categoriaIds['sobremesas'],
            'tipo_preparo' => 'preparo'
        ],
        [
            'nome' => 'Pudim de Leite',
            'codigo' => 'PL202',
            'descricao' => 'Pudim de leite condensado caseiro',
            'preco' => 12.50,
            'categoria_id' => $categoriaIds['sobremesas'],
            'tipo_preparo' => 'pronto'
        ],
        [
            'nome' => 'Sorvete Artesanal',
            'codigo' => 'SA203',
            'descricao' => 'Duas bolas de sorvete artesanal - escolha o sabor',
            'preco' => 16.90,
            'categoria_id' => $categoriaIds['sobremesas'],
            'tipo_preparo' => 'preparo'
        ]
    ];
    
    echo "📦 Adicionando/atualizando produtos...\n";
    
    foreach ($produtos as $produto) {
        $produto['ativo'] = true;
        $produto['created_at'] = now();
        $produto['updated_at'] = now();
        
        // Verificar se produto já existe pelo código
        $produtoExistente = DB::table('produtos')->where('codigo', $produto['codigo'])->first();
        
        if ($produtoExistente) {
            DB::table('produtos')
                ->where('codigo', $produto['codigo'])
                ->update($produto);
            echo "📝 Atualizado: {$produto['nome']} (Código: {$produto['codigo']})\n";
        } else {
            DB::table('produtos')->insert($produto);
            echo "➕ Adicionado: {$produto['nome']} (Código: {$produto['codigo']})\n";
        }
    }
    
    echo "\n✅ Produtos criados/atualizados com sucesso!\n\n";
    
    // Mostrar resumo
    $totalProdutos = DB::table('produtos')->count();
    $produtosComCodigo = DB::table('produtos')->whereNotNull('codigo')->count();
    $produtosPreparo = DB::table('produtos')->where('tipo_preparo', 'preparo')->count();
    $produtosProntos = DB::table('produtos')->where('tipo_preparo', 'pronto')->count();
    
    echo "📊 RESUMO:\n";
    echo "Total de produtos: {$totalProdutos}\n";
    echo "Produtos com código: {$produtosComCodigo}\n";
    echo "Produtos de preparo: {$produtosPreparo}\n";
    echo "Produtos prontos: {$produtosProntos}\n\n";
    
    echo "🔍 TESTE DE BUSCA:\n";
    echo "Agora você pode testar a busca por:\n";
    echo "- Códigos: CC350, SLN001, PG001, HA101, etc.\n";
    echo "- Nomes: Coca, Picanha, Hambúrguer, Pudim, etc.\n\n";
    
    echo "🍽️ TIPOS DE PREPARO:\n";
    echo "Produtos que solicitarão observações (preparo): Suco de Laranja, Picanha, Salmão, Frango, Risotto, Hambúrguer, Hot Dog, Petit Gateau, Sorvete\n";
    echo "Produtos prontos (sem observações): Coca-Cola, Heineken, Água, Sanduíche Natural, Pudim\n\n";
    
    echo "🎯 Sistema de busca de produtos implementado com sucesso!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
