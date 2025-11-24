<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testando API pedidosAtivos ===\n\n";

// Simular o método pedidosAtivos
try {
    $pedidos = App\Models\Pedido::whereIn('status', ['aberto', 'pendente', 'em_preparo', 'pronto'])
        ->with([
            'mesa',
            'usuario',
            'itens' => function($query) {
                $query->with(['produto.categoria', 'combo.produtos']);
            },
            'delivery.cliente'
        ])
        ->orderByRaw("FIELD(status, 'aberto', 'pendente', 'em_preparo', 'pronto')")
        ->orderBy('created_at', 'asc')
        ->get();
    
    echo "Total encontrado: " . $pedidos->count() . "\n\n";
    
    if ($pedidos->count() > 0) {
        echo "Primeiros 3 pedidos:\n";
        foreach ($pedidos->take(3) as $pedido) {
            echo "\nPedido #{$pedido->id}\n";
            echo "  Status: {$pedido->status}\n";
            echo "  Mesa: " . ($pedido->mesa_id ? "Mesa #{$pedido->mesa_id}" : "Delivery/Levar") . "\n";
            echo "  Itens: " . $pedido->itens->count() . "\n";
            
            if ($pedido->itens->count() > 0) {
                echo "  Primeiro item: ";
                $item = $pedido->itens->first();
                if ($item->produto) {
                    echo $item->produto->nome . " (Qtd: {$item->quantidade})\n";
                } else if ($item->combo) {
                    echo $item->combo->nome . " (Qtd: {$item->quantidade})\n";
                }
            }
        }
        
        echo "\n\n✅ API retornaria os pedidos corretamente!\n";
    } else {
        echo "❌ Nenhum pedido encontrado com os critérios.\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
}
