<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pedido;
use App\Models\Usuario;
use App\Models\ItemPedido;

echo "=== TESTE DA FUNCIONALIDADE DE EXCLUSÃO DE ITENS ===\n";

// Buscar um pedido com múltiplos itens
$pedido = Pedido::with('itens')->whereHas('itens', function($query) {
    $query->havingRaw('COUNT(*) >= 2');
}, '>=', 2)->first();

if (!$pedido) {
    echo "❌ Nenhum pedido com múltiplos itens encontrado para teste\n";
    exit;
}

echo "✅ Pedido encontrado: ID {$pedido->id} com {$pedido->itens->count()} itens\n";

// Buscar um usuário admin
$admin = Usuario::where('role', 'admin')->first();
if (!$admin) {
    echo "❌ Nenhum usuário admin encontrado\n";
    exit;
}

echo "✅ Usuário admin encontrado: {$admin->nome} ({$admin->email})\n";

// Listar itens do pedido
echo "\n=== ITENS DO PEDIDO ANTES DA EXCLUSÃO ===\n";
$totalAntes = $pedido->total;
foreach ($pedido->itens as $index => $item) {
    echo "- Item {$item->id}: {$item->produto->nome} (Qtd: {$item->quantidade}, Preço: R$ {$item->preco_unitario})\n";
}
echo "Total antes: R$ " . number_format($totalAntes, 2, ',', '.') . "\n";

// Selecionar um item para excluir (não o último)
$itemParaExcluir = $pedido->itens->first();
echo "\n🗑️ Tentando excluir item {$itemParaExcluir->id}: {$itemParaExcluir->produto->nome}\n";

// Simular exclusão
try {
    // Verificar se é o último item
    if ($pedido->itens()->count() <= 1) {
        echo "❌ Não é possível excluir o último item do pedido\n";
    } else {
        $itemParaExcluir->delete();
        
        // Recalcular total
        $novoTotal = $pedido->itens()->sum(\DB::raw('quantidade * preco_unitario'));
        $pedido->update(['total' => $novoTotal]);
        
        echo "✅ Item excluído com sucesso!\n";
        
        // Recarregar pedido
        $pedido->refresh();
        $pedido->load('itens');
        
        echo "\n=== ITENS DO PEDIDO APÓS A EXCLUSÃO ===\n";
        foreach ($pedido->itens as $item) {
            echo "- Item {$item->id}: {$item->produto->nome} (Qtd: {$item->quantidade}, Preço: R$ {$item->preco_unitario})\n";
        }
        echo "Total depois: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
        echo "Diferença: R$ " . number_format($totalAntes - $pedido->total, 2, ',', '.') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao excluir item: " . $e->getMessage() . "\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";