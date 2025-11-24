<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Verificando pedidos para o monitor ===\n\n";

// Buscar pedidos ativos
$pedidos = DB::table('pedidos')
    ->whereIn('status', ['aberto', 'pendente', 'em_preparo', 'pronto'])
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get(['id', 'status', 'created_at', 'mesa_id']);

echo "Total de pedidos encontrados: " . $pedidos->count() . "\n\n";

foreach ($pedidos as $pedido) {
    echo "Pedido #{$pedido->id}\n";
    echo "  Status: {$pedido->status}\n";
    echo "  Mesa: " . ($pedido->mesa_id ? "Mesa #{$pedido->mesa_id}" : 'Delivery/Levar') . "\n";
    echo "  Criado: {$pedido->created_at}\n";
    
    // Contar itens
    $itens = DB::table('item_pedidos')
        ->where('pedido_id', $pedido->id)
        ->count();
    echo "  Itens: {$itens}\n";
    echo "\n";
}
