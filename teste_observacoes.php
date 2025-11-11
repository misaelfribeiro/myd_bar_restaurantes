<?php

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ItemPedido;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

echo "=== TESTE DE OBSERVAÇÕES ===\n\n";

try {
    // Verificar se a coluna observacoes existe
    $columns = DB::select("SHOW COLUMNS FROM item_pedidos");
    echo "Colunas da tabela item_pedidos:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
        if ($column->Field === 'observacoes') {
            echo "  ✅ Coluna observações encontrada!\n";
        }
    }

    echo "\n=== TESTANDO CRIAÇÃO DE ITEM COM OBSERVAÇÕES ===\n";
    
    // Buscar um pedido existente
    $pedido = Pedido::first();
    if (!$pedido) {
        echo "❌ Nenhum pedido encontrado para teste.\n";
        exit;
    }
    
    echo "✅ Pedido encontrado: ID {$pedido->id}\n";
    
    // Buscar um produto
    $produto = \App\Models\Produto::first();
    if (!$produto) {
        echo "❌ Nenhum produto encontrado para teste.\n";
        exit;
    }
    
    echo "✅ Produto encontrado: {$produto->nome} (ID: {$produto->id})\n";
    
    // Criar item com observações
    $itemTeste = ItemPedido::create([
        'pedido_id' => $pedido->id,
        'produto_id' => $produto->id,
        'quantidade' => 1,
        'preco_unitario' => $produto->preco,
        'subtotal' => $produto->preco,
        'observacoes' => 'Teste de observações: mal passado, sem cebola'
    ]);
    
    echo "✅ Item criado com sucesso! ID: {$itemTeste->id}\n";
    echo "📝 Observações salvas: {$itemTeste->observacoes}\n";
    
    // Verificar se foi salvo corretamente
    $itemVerificacao = ItemPedido::find($itemTeste->id);
    echo "🔍 Verificação: Observações recuperadas: {$itemVerificacao->observacoes}\n";
    
    // Limpeza - remover item teste
    $itemTeste->delete();
    echo "🧹 Item de teste removido.\n";
    
    echo "\n✅ TESTE CONCLUÍDO COM SUCESSO!\n";
    echo "Sistema de observações está funcionando corretamente.\n";
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
}
