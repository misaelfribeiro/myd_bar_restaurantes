<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pedido;
use App\Models\Produto;
use App\Models\ItemPedido;

echo "🍽️ Criando itens para o Pedido 30...\n\n";

// Buscar pedido 30
$pedido = Pedido::find(30);

if (!$pedido) {
    echo "❌ Pedido 30 não encontrado!\n";
    exit;
}

echo "📋 Pedido encontrado: #{$pedido->id}, Mesa: {$pedido->mesa_id}, Total: R$ " . number_format($pedido->total, 2, ',', '.') . "\n\n";

// Buscar alguns produtos para adicionar
$produtos = Produto::take(3)->get();

if ($produtos->isEmpty()) {
    echo "❌ Nenhum produto encontrado! Criando produtos...\n";
    
    Produto::create(['nome' => 'Hambúrguer Artesanal', 'preco' => 25.90, 'categoria_id' => 1, 'codigo' => 'BURG001']);
    Produto::create(['nome' => 'Refrigerante Lata', 'preco' => 5.50, 'categoria_id' => 2, 'codigo' => 'REF001']);
    Produto::create(['nome' => 'Batata Frita', 'preco' => 12.00, 'categoria_id' => 1, 'codigo' => 'BAT001']);
    
    $produtos = Produto::take(3)->get();
}

echo "📦 Produtos disponíveis:\n";
foreach ($produtos as $produto) {
    echo "- {$produto->nome}: R$ " . number_format($produto->preco, 2, ',', '.') . "\n";
}

echo "\n🔄 Removendo itens existentes do pedido...\n";
ItemPedido::where('pedido_id', $pedido->id)->delete();

echo "➕ Adicionando novos itens...\n";

$totalNovo = 0;

foreach ($produtos as $index => $produto) {
    $quantidade = rand(1, 3);
    $preco_unitario = $produto->preco;
    $subtotal = $quantidade * $preco_unitario;
    $observacoes = ($index == 0) ? 'Sem cebola, com molho especial' : null;
    
    ItemPedido::create([
        'pedido_id' => $pedido->id,
        'produto_id' => $produto->id,
        'quantidade' => $quantidade,
        'preco_unitario' => $preco_unitario,
        'subtotal' => $subtotal,
        'observacoes' => $observacoes
    ]);
    
    $totalNovo += $subtotal;
    
    echo "  - {$produto->nome}: {$quantidade}x R$ " . number_format($preco_unitario, 2, ',', '.') . " = R$ " . number_format($subtotal, 2, ',', '.') . "\n";
    if ($observacoes) {
        echo "    Obs: {$observacoes}\n";
    }
}

// Atualizar total do pedido
$pedido->total = $totalNovo;
$pedido->save();

echo "\n✅ Itens criados com sucesso!\n";
echo "📊 Total do pedido atualizado: R$ " . number_format($totalNovo, 2, ',', '.') . "\n";
echo "\n🎯 Agora teste o modal de pagamento no navegador!\n";
?>