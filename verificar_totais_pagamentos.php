<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pagamento;
use App\Models\Pedido;
use Carbon\Carbon;

echo "=== VERIFICANDO PAGAMENTOS E TOTAIS ===\n";

$hoje = Carbon::today();
echo "Data de hoje: " . $hoje->format('Y-m-d') . "\n\n";

// Listar todos os pagamentos de hoje
$pagamentos = Pagamento::whereDate('data_pagamento', $hoje)->get();

echo "=== TODOS OS PAGAMENTOS DE HOJE ===\n";
echo "Total de pagamentos: " . $pagamentos->count() . "\n\n";

foreach ($pagamentos as $pagamento) {
    echo "Pagamento ID: {$pagamento->id}\n";
    echo "  Pedido: #{$pagamento->pedido_id}\n";
    echo "  Valor: R$ " . number_format($pagamento->valor, 2, ',', '.') . "\n";
    echo "  Forma: {$pagamento->forma_pagamento}\n";
    echo "  Status: {$pagamento->status}\n";
    echo "  Data: " . $pagamento->data_pagamento . "\n\n";
}

// Pagamentos confirmados
$pagamentosConfirmados = Pagamento::whereDate('data_pagamento', $hoje)
    ->where('status', 'confirmado')
    ->get();

echo "=== PAGAMENTOS CONFIRMADOS ===\n";
echo "Total: " . $pagamentosConfirmados->count() . "\n";
echo "Valor total: R$ " . number_format($pagamentosConfirmados->sum('valor'), 2, ',', '.') . "\n";
echo "Troco total: R$ " . number_format($pagamentosConfirmados->sum('troco'), 2, ',', '.') . "\n\n";

// Por forma de pagamento
echo "=== POR FORMA DE PAGAMENTO ===\n";
$porForma = $pagamentosConfirmados->groupBy('forma_pagamento');

foreach ($porForma as $forma => $pagamentosForma) {
    echo "$forma: " . $pagamentosForma->count() . " pagamentos - R$ " . 
         number_format($pagamentosForma->sum('valor'), 2, ',', '.') . "\n";
}

// Verificar pedidos finalizados
echo "\n=== PEDIDOS FINALIZADOS ===\n";
$pedidosFinalizados = Pedido::where('status', 'finalizado')->with('pagamentos')->get();

foreach ($pedidosFinalizados as $pedido) {
    echo "Pedido #{$pedido->id}:\n";
    echo "  Total: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
    echo "  Pagamentos: " . $pedido->pagamentos->count() . "\n";
    
    $totalPago = $pedido->pagamentos->where('status', 'confirmado')->sum('valor');
    echo "  Total Pago: R$ " . number_format($totalPago, 2, ',', '.') . "\n";
    echo "  Status Pagamento: " . ($totalPago >= $pedido->total ? 'PAGO' : 'PENDENTE') . "\n\n";
}

echo "=== TESTE CONCLUÍDO ===\n";
