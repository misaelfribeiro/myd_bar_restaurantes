<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pedido;
use App\Models\Pagamento;

echo "=== DIAGNÓSTICO DE PEDIDOS E CAIXA ===\n\n";

// Verificar todos os pedidos
$pedidos = Pedido::with('mesa', 'pagamentos')->get();
echo "Total de pedidos no sistema: " . $pedidos->count() . "\n\n";

echo "=== PEDIDOS POR STATUS ===\n";
$porStatus = $pedidos->groupBy('status');

foreach ($porStatus as $status => $pedidosStatus) {
    echo "Status '{$status}': " . $pedidosStatus->count() . " pedidos\n";
    
    foreach ($pedidosStatus as $pedido) {
        echo "  - Pedido #{$pedido->id} - Mesa: " . ($pedido->mesa->identificador ?? 'N/A') . 
             " - Total: R$ " . number_format($pedido->total, 2, ',', '.') . 
             " - Pagamentos: " . $pedido->pagamentos->count() . "\n";
    }
    echo "\n";
}

echo "=== PAGAMENTOS ===\n";
$pagamentos = Pagamento::all();
echo "Total de pagamentos: " . $pagamentos->count() . "\n";

foreach ($pagamentos as $pagamento) {
    echo "Pagamento ID: {$pagamento->id}\n";
    echo "  Pedido: #{$pagamento->pedido_id}\n";
    echo "  Valor: R$ " . number_format($pagamento->valor, 2, ',', '.') . "\n";
    echo "  Status: {$pagamento->status}\n";
    echo "  Data: {$pagamento->data_pagamento}\n\n";
}

echo "=== LÓGICA ATUAL DO CAIXA ===\n";
echo "O caixa busca pedidos com:\n";
echo "- Status: 'finalizado'\n";
echo "- Que não estão totalmente pagos\n\n";

$pedidosParaCaixa = Pedido::with('mesa', 'pagamentos')
    ->where('status', 'finalizado')
    ->get()
    ->filter(function($pedido) {
        $totalPago = $pedido->pagamentos()->where('status', 'confirmado')->sum('valor');
        return $totalPago < $pedido->total;
    });

echo "Pedidos que apareceriam no caixa: " . $pedidosParaCaixa->count() . "\n";

foreach ($pedidosParaCaixa as $pedido) {
    echo "  - Pedido #{$pedido->id} - Restante: R$ " . 
         number_format($pedido->total - $pedido->pagamentos->where('status', 'confirmado')->sum('valor'), 2, ',', '.') . "\n";
}
