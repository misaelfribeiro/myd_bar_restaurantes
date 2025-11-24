<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$deliveries = App\Models\Delivery::with('pedido')
    ->whereIn('status', ['confirmado', 'preparando', 'pronto'])
    ->get();

echo "Entregas disponíveis:\n\n";
foreach ($deliveries as $d) {
    echo "ID: " . $d->id . "\n";
    echo "Status: " . $d->status . "\n";
    echo "Pedido ID: " . $d->pedido_id . "\n";
    echo "Entregador: " . ($d->pedido->entregador_id ?? 'SEM ENTREGADOR') . "\n";
    echo "---\n";
}
