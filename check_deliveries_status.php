<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Delivery;

echo "=== Deliveries sem Entregador ===\n\n";

$deliveries = Delivery::whereNull('entregador_id')->get();

foreach($deliveries as $d) {
    echo "ID: {$d->id}\n";
    echo "Status: {$d->status}\n";
    echo "Disponível Plataforma: " . ($d->disponivel_plataforma ? 'SIM' : 'NÃO') . "\n";
    echo "Pedido ID: {$d->pedido_id}\n";
    echo "Tentativas: {$d->tentativas_notificacao}\n";
    echo "---\n";
}

echo "\n=== Query do App (status=pendente, entregador_id=null) ===\n";
$appQuery = Delivery::where('status', 'pendente')->whereNull('entregador_id')->get();
echo "Total: " . $appQuery->count() . "\n";

echo "\n=== Query Correta (disponivel_plataforma=true, entregador_id=null) ===\n";
$correctQuery = Delivery::where('disponivel_plataforma', true)->whereNull('entregador_id')->get();
echo "Total: " . $correctQuery->count() . "\n";
foreach($correctQuery as $d) {
    echo "ID: {$d->id}, Status: {$d->status}, Pedido: {$d->pedido_id}\n";
}
