<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pedido;

echo "=== VERIFICANDO PEDIDOS E PAGAMENTOS ===\n";

$pedidos = Pedido::with('pagamentos', 'mesa')->where('status', 'finalizado')->get();

foreach ($pedidos as $pedido) {
    echo "\nPedido #{$pedido->id} - Mesa {$pedido->mesa->identificador}\n";
    echo "  Total: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
    echo "  Total Pago: R$ " . number_format($pedido->total_pago, 2, ',', '.') . "\n";
    echo "  Saldo Restante: R$ " . number_format($pedido->saldo_restante, 2, ',', '.') . "\n";
    echo "  Status Pagamento: " . ($pedido->isPago() ? 'PAGO' : 'PENDENTE') . "\n";
    echo "  Pagamentos:\n";
    
    foreach ($pedido->pagamentos as $pagamento) {
        echo "    - R$ " . number_format($pagamento->valor, 2, ',', '.') . 
             " ({$pagamento->forma_pagamento}) - Status: {$pagamento->status}\n";
    }
    
    if ($pedido->pagamentos->count() == 0) {
        echo "    - Nenhum pagamento encontrado\n";
    }
}

// Testar filtro
echo "\n=== PEDIDOS PENDENTES PARA CAIXA ===\n";
$pedidosPendentes = Pedido::with('mesa', 'pagamentos')
    ->where('status', 'finalizado')
    ->get()
    ->filter(function($pedido) {
        return !$pedido->isPago();
    });

echo "Total de pedidos pendentes: " . $pedidosPendentes->count() . "\n";

foreach ($pedidosPendentes as $pedido) {
    echo "- Pedido #{$pedido->id} - Restante: R$ " . number_format($pedido->saldo_restante, 2, ',', '.') . "\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";
