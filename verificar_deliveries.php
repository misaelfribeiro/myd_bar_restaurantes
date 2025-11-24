<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

echo "=== VERIFICAÇÃO DELIVERIES ===\n\n";

$deliveries = \App\Models\Delivery::all();

foreach ($deliveries as $delivery) {
    echo "ID: {$delivery->id}\n";
    echo "Cliente: {$delivery->cliente_nome}\n";
    echo "Telefone: {$delivery->cliente_telefone}\n";
    echo "Status: {$delivery->status}\n";
    echo "Status Label: {$delivery->status_label}\n";
    echo "Status Color: {$delivery->status_color}\n";
    echo "Endereço Completo: {$delivery->endereco_completo}\n";
    echo "Tempo Estimado: {$delivery->tempo_estimado} min\n";
    echo "Taxa: R$ " . number_format($delivery->taxa_entrega, 2, ',', '.') . "\n";
    echo "---\n";
}