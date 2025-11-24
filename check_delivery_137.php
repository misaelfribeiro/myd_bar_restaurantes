<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$delivery = App\Models\Delivery::with('pedido')->find(137);

if ($delivery) {
    echo "Delivery #137:\n";
    echo "Status: " . $delivery->status . "\n";
    echo "Entregador ID: " . ($delivery->pedido->entregador_id ?? 'NULL') . "\n";
    echo "Disponível Plataforma: " . ($delivery->disponivel_plataforma ? 'SIM' : 'NÃO') . "\n";
} else {
    echo "Delivery não encontrada\n";
}
