<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pedido = App\Models\Pedido::with('delivery')->find(137);

if ($pedido) {
    echo "Pedido #137:\n";
    echo "Delivery ID: " . ($pedido->delivery ? $pedido->delivery->id : 'NULL') . "\n";
    echo "Pedido ID na Delivery: " . ($pedido->delivery ? $pedido->delivery->pedido_id : 'NULL') . "\n";
} else {
    echo "Pedido não encontrado\n";
}

// Verificar delivery 80
$delivery = App\Models\Delivery::find(80);
if ($delivery) {
    echo "\nDelivery #80:\n";
    echo "Pedido ID: " . $delivery->pedido_id . "\n";
}
