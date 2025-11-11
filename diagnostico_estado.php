<?php
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 DIAGNÓSTICO ATUAL DO SISTEMA\n";
echo "===============================\n\n";

// Verificar pedidos
$totalPedidos = App\Models\Pedido::count();
$pedidosAbertos = App\Models\Pedido::where('status', 'aberto')->count();
$pedidosFinalizados = App\Models\Pedido::where('status', 'finalizado')->count();

echo "📋 PEDIDOS:\n";
echo "- Total: $totalPedidos\n";
echo "- Abertos: $pedidosAbertos\n";  
echo "- Finalizados: $pedidosFinalizados\n\n";

// Verificar mesas ocupadas
$mesasOcupadas = App\Models\Mesa::whereHas('pedidos', function($q) {
    $q->where('status', 'aberto');
})->count();
$totalMesas = App\Models\Mesa::count();

echo "🪑 MESAS:\n";
echo "- Total: $totalMesas\n";
echo "- Ocupadas: $mesasOcupadas\n";
echo "- Livres: " . ($totalMesas - $mesasOcupadas) . "\n\n";

// Listar pedidos abertos com detalhes
echo "🔍 DETALHES DOS PEDIDOS ABERTOS:\n";
$pedidos = App\Models\Pedido::with(['mesa', 'usuario'])
    ->where('status', 'aberto')
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($pedidos as $pedido) {
    echo "- Pedido #{$pedido->id} - Mesa: {$pedido->mesa->identificador} - ";
    echo "Total: R$ " . number_format($pedido->total, 2, ',', '.') . " - ";
    echo "Criado: {$pedido->created_at->format('H:i:s')}\n";
}

if ($pedidos->count() == 0) {
    echo "  (Nenhum pedido aberto)\n";
}

echo "\n🔍 MESAS COM MÚLTIPLOS PEDIDOS ABERTOS:\n";
$mesasComMultiplosPedidos = App\Models\Mesa::withCount(['pedidos' => function($q) {
    $q->where('status', 'aberto');
}])->having('pedidos_count', '>', 1)->get();

foreach ($mesasComMultiplosPedidos as $mesa) {
    echo "- {$mesa->identificador}: {$mesa->pedidos_count} pedidos abertos\n";
}

if ($mesasComMultiplosPedidos->count() == 0) {
    echo "  ✅ Nenhuma mesa com múltiplos pedidos\n";
} else {
    echo "  ❌ PROBLEMA: Mesas com múltiplos pedidos abertos encontradas!\n";
}

echo "\n===============================\n";
echo "Diagnóstico concluído: " . date('H:i:s') . "\n";
