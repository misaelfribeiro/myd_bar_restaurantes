<?php
require_once 'vendor/autoload.php';

// Carregar o ambiente Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Usar models
use App\Models\Mesa;
use App\Models\Pedido;

echo "=== DEBUG MESA E PEDIDOS ===\n";

// Verificar mesa 1
$mesa = Mesa::find(1);
if ($mesa) {
    echo "Mesa 1 encontrada: {$mesa->identificador}\n";
    
    $pedidos = $mesa->pedidos()->with('itens.produto')->get();
    echo "Total de pedidos na mesa: " . $pedidos->count() . "\n\n";
    
    foreach ($pedidos as $pedido) {
        echo "Pedido #{$pedido->id}\n";
        echo "  - Status: {$pedido->status}\n";
        echo "  - Total: R$ {$pedido->total}\n";
        echo "  - Criado em: {$pedido->created_at}\n";
        echo "  - Itens: " . $pedido->itens->count() . "\n";
        
        foreach ($pedido->itens as $item) {
            echo "    - {$item->produto->nome} (Qtd: {$item->quantidade}) - R$ " . ($item->preco_unitario * $item->quantidade) . "\n";
        }
        echo "\n";
    }
    
    // Verificar pedidos abertos especificamente
    $pedidosAbertos = $mesa->pedidos()->where('status', 'aberto')->get();
    echo "Pedidos com status 'aberto': " . $pedidosAbertos->count() . "\n";
    
} else {
    echo "Mesa 1 não encontrada!\n";
}

// Verificar todas as mesas com pedidos
echo "\n=== TODAS AS MESAS COM PEDIDOS ===\n";
$mesasComPedidos = Mesa::whereHas('pedidos')->with(['pedidos' => function($query) {
    $query->orderBy('created_at', 'desc');
}])->get();

foreach ($mesasComPedidos as $mesa) {
    echo "Mesa {$mesa->identificador}:\n";
    foreach ($mesa->pedidos as $pedido) {
        echo "  - Pedido #{$pedido->id} - Status: {$pedido->status} - Total: R$ {$pedido->total}\n";
    }
    echo "\n";
}
