<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$delivery = App\Models\Delivery::with('pedido.entregador')->find(78);

if ($delivery) {
    echo "Delivery #78:\n";
    echo "Pedido ID: " . ($delivery->pedido_id ?? 'NULL') . "\n";
    echo "Pedido existe: " . ($delivery->pedido ? 'SIM' : 'NÃO') . "\n";
    if ($delivery->pedido) {
        echo "Entregador ID no pedido: " . ($delivery->pedido->entregador_id ?? 'NULL') . "\n";
        echo "Entregador existe: " . ($delivery->pedido->entregador ? 'SIM' : 'NÃO') . "\n";
    }
} else {
    echo "Delivery não encontrada\n";
}
