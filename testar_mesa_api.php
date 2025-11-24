<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Mesa;
use App\Models\Pedido;

echo "=== TESTE DA API DE PAGAMENTO DE MESAS ===\n\n";

// Buscar mesa 2 (que usamos antes) e verificar se tem pedidos finalizados
$mesa = Mesa::find(2);
if (!$mesa) {
    echo "Mesa 2 não encontrada.\n";
    exit;
}

echo "Mesa: {$mesa->numero} - {$mesa->local}\n";

// Verificar pedidos finalizados
$pedidosFinalizados = $mesa->pedidos()->where('status', 'finalizado')->get();
echo "Pedidos finalizados: {$pedidosFinalizados->count()}\n";

if ($pedidosFinalizados->count() > 0) {
    $total = $pedidosFinalizados->sum('total');
    echo "Total da mesa: R$ " . number_format($total, 2, ',', '.') . "\n";
    
    foreach ($pedidosFinalizados as $pedido) {
        echo "  - Pedido {$pedido->id}: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
    }
    
    echo "\nVocê pode testar o pagamento da mesa {$mesa->id} via API.\n";
    echo "URL: /api/pagamentos-teste/mesa/{$mesa->id}\n";
} else {
    echo "Esta mesa não possui pedidos finalizados para pagamento.\n";
}