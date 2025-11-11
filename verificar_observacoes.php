<?php

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;

echo "=== VERIFICAÇÃO DE OBSERVAÇÕES NOS ITENS ===\n\n";

try {
    // Verificar se existem itens com observações
    $itensComObservacoes = ItemPedido::whereNotNull('observacoes')
        ->where('observacoes', '!=', '')
        ->with(['produto', 'pedido'])
        ->get();
    
    if ($itensComObservacoes->count() > 0) {
        echo "✅ Encontrados {$itensComObservacoes->count()} itens com observações:\n\n";
        
        foreach ($itensComObservacoes as $item) {
            echo "📦 Produto: {$item->produto->nome}\n";
            echo "📝 Observações: {$item->observacoes}\n";
            echo "🎫 Pedido: #{$item->pedido->id}\n";
            echo "---\n";
        }
    } else {
        echo "⚠️ Nenhum item com observações encontrado.\n";
        echo "Vou criar um item de teste...\n\n";
        
        // Buscar um pedido e produto para criar um item teste
        $pedido = Pedido::first();
        $produto = Produto::first();
        
        if ($pedido && $produto) {
            $itemTeste = ItemPedido::create([
                'pedido_id' => $pedido->id,
                'produto_id' => $produto->id,
                'quantidade' => 1,
                'preco_unitario' => $produto->preco,
                'subtotal' => $produto->preco,
                'observacoes' => 'Teste: mal passado, sem cebola'
            ]);
            
            echo "✅ Item de teste criado com observações!\n";
            echo "📦 Produto: {$produto->nome}\n";
            echo "📝 Observações: {$itemTeste->observacoes}\n";
        } else {
            echo "❌ Não foi possível encontrar pedido ou produto para teste.\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
