<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pedidos = App\Models\Pedido::whereIn('status', ['aberto', 'pendente', 'em_preparo', 'pronto'])
    ->orderBy('created_at', 'desc')
    ->take(5)
    ->get();

echo "Últimos pedidos:\n";
foreach ($pedidos as $p) {
    echo "#" . $p->id . " - Status: " . $p->status . " - Criado: " . $p->created_at->format('H:i') . "\n";
}
