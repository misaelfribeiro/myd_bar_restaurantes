<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Delivery;
use App\Models\Entregador;

$entregador = Entregador::first();
echo "=== Entregas do Entregador #{$entregador->id} ({$entregador->nome}) ===\n\n";

// Query atual (errada)
echo "Query ATUAL (status IN coletado, em_rota):\n";
$atual = Delivery::where('entregador_id', $entregador->id)
    ->whereIn('status', ['coletado', 'em_rota'])
    ->get();
echo "Total: " . $atual->count() . "\n\n";

// Query correta
echo "Query CORRETA (status NOT IN entregue, cancelado):\n";
$correta = Delivery::where('entregador_id', $entregador->id)
    ->whereNotIn('status', ['entregue', 'cancelado'])
    ->get();
echo "Total: " . $correta->count() . "\n";
foreach($correta as $d) {
    echo "  ID: {$d->id}, Status: {$d->status}, Pedido: #{$d->pedido->numero_pedido}\n";
}

echo "\n=== Todas as entregas do entregador ===\n";
$todas = Delivery::where('entregador_id', $entregador->id)->get();
foreach($todas as $d) {
    echo "ID: {$d->id}, Status: {$d->status}, Aceito em: {$d->aceito_em}\n";
}
