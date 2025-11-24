<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Delivery;
use App\Models\Entregador;

$entregador = Entregador::first();
echo "=== Teste API entregasAtivas ===\n";
echo "Entregador: {$entregador->nome}\n\n";

// Simular o endpoint
$entregas = Delivery::where('entregador_id', $entregador->id)
    ->whereNotIn('status', ['entregue', 'cancelado'])
    ->with(['pedido'])
    ->orderBy('created_at', 'desc')
    ->get();

echo "Total: " . $entregas->count() . " entregas ativas\n\n";

// Mostrar as 3 primeiras
foreach($entregas->take(3) as $delivery) {
    echo "═══════════════════════════════════════\n";
    echo "ID: {$delivery->id}\n";
    echo "Pedido: #{$delivery->pedido->numero_pedido}\n";
    echo "Status: {$delivery->status}\n";
    echo "Cliente: {$delivery->cliente_nome}\n";
    echo "Endereço: {$delivery->endereco_completo}\n";
    echo "Valor Entregador: R$ " . number_format($delivery->valor_entregador, 2, ',', '.') . "\n";
    
    if ($delivery->origem_latitude && $delivery->origem_longitude) {
        echo "Restaurante: {$delivery->origem_latitude}, {$delivery->origem_longitude}\n";
    } else {
        echo "⚠️ Sem coordenadas do restaurante\n";
    }
    
    if ($delivery->destino_latitude && $delivery->destino_longitude) {
        echo "Destino: {$delivery->destino_latitude}, {$delivery->destino_longitude}\n";
    } else {
        echo "⚠️ Sem coordenadas do destino\n";
    }
    
    echo "\n";
}

// Verificar se tem coordenadas
echo "\n=== Verificação de Coordenadas ===\n";
$semOrigem = $entregas->filter(fn($d) => !$d->origem_latitude || !$d->origem_longitude)->count();
$semDestino = $entregas->filter(fn($d) => !$d->destino_latitude || !$d->destino_longitude)->count();

echo "Entregas SEM coordenadas do restaurante: $semOrigem\n";
echo "Entregas SEM coordenadas do destino: $semDestino\n";
