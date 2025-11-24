<?php
// Script para criar dados de teste para múltiplos pagamentos
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\{Pedido, Mesa, Produto, ItemPedido, Categoria, Usuario, Caixa};
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();
    
    echo "=== CRIANDO DADOS PARA TESTE DE MÚLTIPLOS PAGAMENTOS ===\n\n";
    
    // 1. Criar usuário se não existir
    $usuario = Usuario::firstOrCreate([
        'email' => 'caixa@teste.com'
    ], [
        'nome' => 'Operador Caixa',
        'password' => bcrypt('123456'),
        'tipo' => 'admin'
    ]);
    echo "✅ Usuário criado/encontrado: {$usuario->nome}\n";
    
    // 2. Criar categoria
    $categoria = Categoria::firstOrCreate([
        'nome' => 'Lanches'
    ], [
        'descricao' => 'Categoria de lanches'
    ]);
    echo "✅ Categoria: {$categoria->nome}\n";
    
    // 3. Criar produtos
    $produtos = [
        ['nome' => 'Hambúrguer Clássico', 'preco' => 18.50],
        ['nome' => 'Batata Frita', 'preco' => 12.00],
        ['nome' => 'Refrigerante', 'preco' => 6.50],
        ['nome' => 'Suco Natural', 'preco' => 8.00]
    ];
    
    foreach ($produtos as $prodData) {
        Produto::firstOrCreate([
            'nome' => $prodData['nome']
        ], [
            'categoria_id' => $categoria->id,
            'preco' => $prodData['preco'],
            'disponivel' => true,
            'descricao' => 'Produto para teste'
        ]);
        echo "✅ Produto: {$prodData['nome']} - R$ {$prodData['preco']}\n";
    }
    
    // 4. Criar mesas
    for ($i = 1; $i <= 5; $i++) {
        Mesa::firstOrCreate([
            'numero' => $i
        ], [
            'capacidade' => 4,
            'status' => 'livre'
        ]);
    }
    echo "✅ Mesas 1-5 criadas\n";
    
    // 5. Criar pedidos de teste
    $mesa1 = Mesa::where('numero', 1)->first();
    $mesa2 = Mesa::where('numero', 2)->first();
    
    // Pedido 1: Mesa 1 - R$ 25,00 (Hambúrguer + Refrigerante)
    $pedido1 = Pedido::create([
        'mesa_id' => $mesa1->id,
        'usuario_id' => $usuario->id,
        'status' => 'finalizado',
        'observacoes' => 'Pedido para teste de múltiplos pagamentos'
    ]);
    
    ItemPedido::create([
        'pedido_id' => $pedido1->id,
        'produto_id' => Produto::where('nome', 'Hambúrguer Clássico')->first()->id,
        'quantidade' => 1,
        'preco_unitario' => 18.50
    ]);
    
    ItemPedido::create([
        'pedido_id' => $pedido1->id,
        'produto_id' => Produto::where('nome', 'Refrigerante')->first()->id,
        'quantidade' => 1,
        'preco_unitario' => 6.50
    ]);
    
    // Pedido 2: Mesa 2 - R$ 38,50 (Hambúrguer + Batata + Suco + Refrigerante)
    $pedido2 = Pedido::create([
        'mesa_id' => $mesa2->id,
        'usuario_id' => $usuario->id,
        'status' => 'finalizado',
        'observacoes' => 'Pedido para teste de pagamento misto'
    ]);
    
    ItemPedido::create([
        'pedido_id' => $pedido2->id,
        'produto_id' => Produto::where('nome', 'Hambúrguer Clássico')->first()->id,
        'quantidade' => 1,
        'preco_unitario' => 18.50
    ]);
    
    ItemPedido::create([
        'pedido_id' => $pedido2->id,
        'produto_id' => Produto::where('nome', 'Batata Frita')->first()->id,
        'quantidade' => 1,
        'preco_unitario' => 12.00
    ]);
    
    ItemPedido::create([
        'pedido_id' => $pedido2->id,
        'produto_id' => Produto::where('nome', 'Suco Natural')->first()->id,
        'quantidade' => 1,
        'preco_unitario' => 8.00
    ]);
    
    $pedido1->refresh();
    $pedido2->refresh();
    
    echo "\n✅ Pedidos criados:\n";
    echo "   - Pedido {$pedido1->id} (Mesa {$mesa1->numero}): R$ {$pedido1->total}\n";
    echo "   - Pedido {$pedido2->id} (Mesa {$mesa2->numero}): R$ {$pedido2->total}\n";
    
    DB::commit();
    
    echo "\n🎉 DADOS CRIADOS COM SUCESSO!\n\n";
    echo "=== INSTRUÇÕES PARA TESTE ===\n";
    echo "1. Acesse: http://127.0.0.1:8000/caixa\n";
    echo "2. Abra o caixa com saldo inicial de R$ 100,00\n";
    echo "3. Na seção 'Pedidos Aguardando Pagamento', você verá os pedidos criados\n";
    echo "4. Clique em 'Receber Pagamento' em qualquer pedido\n";
    echo "5. Na tela de recebimento, teste:\n";
    echo "   - Pagamento simples (uma forma)\n";
    echo "   - Pagamento múltiplo (várias formas)\n\n";
    echo "=== CENÁRIOS DE TESTE SUGERIDOS ===\n";
    echo "Pedido Mesa 1 (R$ 25,00):\n";
    echo "   - Teste 1: R$ 15,00 dinheiro + R$ 10,00 cartão\n";
    echo "   - Teste 2: R$ 10,00 PIX + R$ 15,00 vale refeição\n\n";
    echo "Pedido Mesa 2 (R$ 38,50):\n";
    echo "   - Teste 3: R$ 20,00 dinheiro + R$ 18,50 cartão de débito\n";
    echo "   - Teste 4: R$ 10,00 PIX + R$ 15,00 cartão + R$ 13,50 vale refeição\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
}
