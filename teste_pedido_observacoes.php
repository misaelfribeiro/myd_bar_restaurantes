<?php
// Script para criar pedido de teste com observações nos itens

require_once 'bootstrap/app.php';

$app = \Illuminate\Foundation\Application::getInstance();
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Produto;
use App\Models\Mesa;

echo "🧪 CRIANDO PEDIDO DE TESTE COM OBSERVAÇÕES\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    DB::beginTransaction();
    
    // Verificar se existem produtos e mesas
    $produto1 = Produto::first();
    $produto2 = Produto::skip(1)->first();
    $mesa = Mesa::first();
    
    if (!$produto1 || !$produto2 || !$mesa) {
        echo "❌ Produtos ou mesa não encontrados.\n";
        echo "Criando dados básicos...\n\n";
        
        // Criar mesa se não existir
        if (!$mesa) {
            $mesa = Mesa::create([
                'numero' => 1,
                'identificador' => 'Mesa 01',
                'capacidade' => 4,
                'disponivel' => true
            ]);
            echo "✅ Mesa criada: {$mesa->identificador}\n";
        }
        
        // Criar produtos se não existirem
        if (!$produto1) {
            $produto1 = Produto::create([
                'nome' => 'Hambúrguer Especial',
                'preco' => 25.50,
                'categoria_id' => 1,
                'ativo' => true,
                'codigo' => 'HAM001',
                'tipo_preparo' => 'preparo'
            ]);
            echo "✅ Produto criado: {$produto1->nome}\n";
        }
        
        if (!$produto2) {
            $produto2 = Produto::create([
                'nome' => 'Coca-Cola 350ml',
                'preco' => 6.50,
                'categoria_id' => 1,
                'ativo' => true,
                'codigo' => 'BEB001',
                'tipo_preparo' => 'pronto'
            ]);
            echo "✅ Produto criado: {$produto2->nome}\n";
        }
    }
    
    // Criar pedido de teste
    $pedido = Pedido::create([
        'usuario_id' => 1,
        'mesa_id' => $mesa->id,
        'total' => 0, // Será calculado depois
        'status' => 'finalizado',
        'observacoes' => 'Pedido de teste para verificar observações dos itens'
    ]);
    
    echo "✅ Pedido criado: #{$pedido->id}\n";
    echo "📍 Mesa: {$mesa->identificador}\n";
    echo "📝 Observações do pedido: {$pedido->observacoes}\n\n";
    
    // Criar itens com observações específicas
    $item1 = ItemPedido::create([
        'pedido_id' => $pedido->id,
        'produto_id' => $produto1->id,
        'quantidade' => 2,
        'preco_unitario' => $produto1->preco,
        'subtotal' => $produto1->preco * 2,
        'observacoes' => 'Mal passado, sem cebola, com queijo extra'
    ]);
    
    $item2 = ItemPedido::create([
        'pedido_id' => $pedido->id,
        'produto_id' => $produto2->id,
        'quantidade' => 1,
        'preco_unitario' => $produto2->preco,
        'subtotal' => $produto2->preco,
        'observacoes' => 'Sem gelo, bem gelada'
    ]);
    
    // Atualizar total do pedido
    $pedido->total = $item1->subtotal + $item2->subtotal;
    $pedido->save();
    
    echo "🍔 Item 1: {$item1->quantidade}x {$produto1->nome}\n";
    echo "   💰 Preço: R$ " . number_format($item1->subtotal, 2, ',', '.') . "\n";
    echo "   💬 Observações: {$item1->observacoes}\n\n";
    
    echo "🥤 Item 2: {$item2->quantidade}x {$produto2->nome}\n";
    echo "   💰 Preço: R$ " . number_format($item2->subtotal, 2, ',', '.') . "\n";
    echo "   💬 Observações: {$item2->observacoes}\n\n";
    
    echo "💵 Total do pedido: R$ " . number_format($pedido->total, 2, ',', '.') . "\n\n";
    
    DB::commit();
    
    echo "✅ SUCESSO! Pedido de teste criado.\n";
    echo "🌐 Acesse: http://localhost:8000/garcom/meus-pedidos\n";
    echo "👀 Você deve ver o pedido #{$pedido->id} com as observações dos itens.\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "📁 Arquivo: " . $e->getFile() . "\n";
    echo "📍 Linha: " . $e->getLine() . "\n";
}
