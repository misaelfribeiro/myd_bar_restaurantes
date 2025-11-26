<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PEDIDOS ABERTOS/PENDENTES ===\n\n";

$pedidos = \App\Models\Pedido::whereIn('status', ['aberto', 'pendente'])
    ->orderBy('id', 'desc')
    ->get();

if ($pedidos->isEmpty()) {
    echo "Nenhum pedido aberto ou pendente encontrado.\n";
} else {
    foreach ($pedidos as $pedido) {
        // Verificar se tem pagamento
        $pagamento = \App\Models\Payment::where('pedido_id', $pedido->id)
            ->orderBy('id', 'desc')
            ->first();
        
        $statusPagamento = 'SEM PAGAMENTO';
        
        if ($pagamento) {
            if ($pagamento->status === 'approved') {
                $statusPagamento = '✅ PAGO';
            } elseif ($pagamento->status === 'pending') {
                $statusPagamento = '⏳ PENDENTE';
            } else {
                $statusPagamento = '❌ ' . strtoupper($pagamento->status);
            }
        }
        
        echo "ID: {$pedido->id} | ";
        echo "Nº: {$pedido->numero_pedido} | ";
        echo "Total: R$ " . number_format($pedido->total, 2, ',', '.') . " | ";
        echo "Status Pedido: {$pedido->status} | ";
        echo "Pagamento: {$statusPagamento}\n";
    }
}

echo "\n=== RESUMO ===\n";
$semPagamento = \App\Models\Pedido::whereIn('status', ['aberto', 'pendente'])
    ->whereDoesntHave('pagamento')
    ->count();
    
$comPagamentoPendente = \App\Models\Pedido::whereIn('status', ['aberto', 'pendente'])
    ->whereHas('pagamento', function($q) {
        $q->where('status', 'pending');
    })
    ->count();
    
$comPagamentoAprovado = \App\Models\Pedido::whereIn('status', ['aberto', 'pendente'])
    ->whereHas('pagamento', function($q) {
        $q->where('status', 'approved');
    })
    ->count();

echo "Sem pagamento registrado: {$semPagamento}\n";
echo "Com pagamento pendente: {$comPagamentoPendente}\n";
echo "Com pagamento aprovado: {$comPagamentoAprovado}\n";
