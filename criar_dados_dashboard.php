<?php

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->class;

use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\Produto;
use App\Models\ItemPedido;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;

echo "🛠️ CRIANDO DADOS DE TESTE PARA DASHBOARD\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    DB::beginTransaction();
    
    // 1. Criar categoria se não existir
    $categoria = Categoria::firstOrCreate([
        'nome' => 'Pratos Principais'
    ], [
        'descricao' => 'Categoria para testes'
    ]);
    echo "✅ Categoria: {$categoria->nome}\n";
    
    // 2. Criar produtos se não existirem
    $produtos = [];
    $produtosData = [
        ['nome' => 'Hambúrguer Especial', 'preco' => 25.50, 'codigo' => 'HAM001'],
        ['nome' => 'Coca-Cola 350ml', 'preco' => 6.50, 'codigo' => 'BEB001'],
        ['nome' => 'Batata Frita', 'preco' => 12.00, 'codigo' => 'LAT001']
    ];
    
    foreach ($produtosData as $produtoData) {
        $produto = Produto::firstOrCreate([
            'codigo' => $produtoData['codigo']
        ], [
            'nome' => $produtoData['nome'],
            'preco' => $produtoData['preco'],
            'categoria_id' => $categoria->id,
            'ativo' => true,
            'tipo_preparo' => 'preparo'
        ]);
        $produtos[] = $produto;
        echo "✅ Produto: {$produto->nome}\n";
    }
    
    // 3. Criar mesas se não existirem
    $mesas = [];
    for ($i = 1; $i <= 10; $i++) {
        $mesa = Mesa::firstOrCreate([
            'numero' => $i
        ], [
            'identificador' => "Mesa $i",
            'capacidade' => 4,
            'disponivel' => true
        ]);
        $mesas[] = $mesa;
    }
    echo "✅ Criadas " . count($mesas) . " mesas\n";
    
    // 4. Criar pedidos de hoje para o garçom
    $userId = 1;
    
    // Pedido 1 - Aberto (Mesa ocupada)
    $pedido1 = Pedido::create([
        'usuario_id' => $userId,
        'mesa_id' => $mesas[0]->id,
        'total' => 32.00,
        'status' => 'aberto',
        'observacoes' => 'Pedido em andamento',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    ItemPedido::create([
        'pedido_id' => $pedido1->id,
        'produto_id' => $produtos[0]->id,
        'quantidade' => 1,
        'preco_unitario' => 25.50,
        'subtotal' => 25.50,
        'observacoes' => 'Mal passado'
    ]);
    
    ItemPedido::create([
        'pedido_id' => $pedido1->id,
        'produto_id' => $produtos[1]->id,
        'quantidade' => 1,
        'preco_unitario' => 6.50,
        'subtotal' => 6.50,
        'observacoes' => 'Sem gelo'
    ]);
    
    echo "✅ Pedido aberto criado: #{$pedido1->id} - Mesa {$mesas[0]->numero}\n";
    
    // Pedido 2 - Finalizado
    $pedido2 = Pedido::create([
        'usuario_id' => $userId,
        'mesa_id' => $mesas[1]->id,
        'total' => 37.50,
        'status' => 'finalizado',
        'observacoes' => 'Pedido concluído',
        'created_at' => now()->subHour(),
        'updated_at' => now()
    ]);
    
    ItemPedido::create([
        'pedido_id' => $pedido2->id,
        'produto_id' => $produtos[0]->id,
        'quantidade' => 1,
        'preco_unitario' => 25.50,
        'subtotal' => 25.50
    ]);
    
    ItemPedido::create([
        'pedido_id' => $pedido2->id,
        'produto_id' => $produtos[2]->id,
        'quantidade' => 1,
        'preco_unitario' => 12.00,
        'subtotal' => 12.00
    ]);
    
    echo "✅ Pedido finalizado criado: #{$pedido2->id} - Mesa {$mesas[1]->numero}\n";
    
    // Pedido 3 - Aberto em outra mesa
    $pedido3 = Pedido::create([
        'usuario_id' => $userId,
        'mesa_id' => $mesas[2]->id,
        'total' => 18.50,
        'status' => 'aberto',
        'observacoes' => 'Cliente esperando',
        'created_at' => now()->subMinutes(30),
        'updated_at' => now()
    ]);
    
    ItemPedido::create([
        'pedido_id' => $pedido3->id,
        'produto_id' => $produtos[2]->id,
        'quantidade' => 1,
        'preco_unitario' => 12.00,
        'subtotal' => 12.00
    ]);
    
    ItemPedido::create([
        'pedido_id' => $pedido3->id,
        'produto_id' => $produtos[1]->id,
        'quantidade' => 1,
        'preco_unitario' => 6.50,
        'subtotal' => 6.50
    ]);
    
    echo "✅ Segundo pedido aberto criado: #{$pedido3->id} - Mesa {$mesas[2]->numero}\n";
    
    DB::commit();
    
    echo "\n🎉 DADOS DE TESTE CRIADOS COM SUCESSO!\n\n";
    echo "📊 RESUMO:\n";
    echo "  - 3 produtos criados\n";
    echo "  - 10 mesas criadas\n";
    echo "  - 3 pedidos criados (2 abertos, 1 finalizado)\n";
    echo "  - 2 mesas ocupadas\n";
    echo "  - Total vendido hoje: R$ 88,00\n\n";
    echo "🌐 Acesse: http://localhost:8000/garcom/dashboard\n";
    echo "🔄 O dashboard deve mostrar dados em tempo real agora!\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "📁 Arquivo: " . $e->getFile() . "\n";
    echo "📍 Linha: " . $e->getLine() . "\n";
}
