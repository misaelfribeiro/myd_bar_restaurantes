<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PEDIDOS DISPONIVEIS (IDs REAIS) ===\n\n";

$pedidos = \App\Models\Pedido::whereIn('status', ['aberto', 'pendente'])
    ->orderBy('id', 'desc')
    ->limit(15)
    ->get();

foreach($pedidos as $p) {
    echo "ID: {$p->id} | Número: {$p->numero_pedido} | Total: R$ " . number_format($p->total, 2, ',', '.') . "\n";
}

echo "\nUse um destes IDs no formulário!\n";
