<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$delivery = App\Models\Delivery::with('pedido')->find(80);

if ($delivery) {
    echo "Delivery #80:\n";
    echo "status: " . $delivery->status . "\n";
    echo "disponivel_plataforma: " . ($delivery->disponivel_plataforma ? 'true' : 'false') . "\n";
    echo "tipo_entrega: " . ($delivery->tipo_entrega ?? 'NULL') . "\n";
    echo "Pedido ID: " . $delivery->pedido_id . "\n";
    echo "Pedido Status: " . ($delivery->pedido ? $delivery->pedido->status : 'NULL') . "\n";
    echo "Entregador ID: " . ($delivery->pedido && $delivery->pedido->entregador_id ? $delivery->pedido->entregador_id : 'NULL') . "\n";
}
