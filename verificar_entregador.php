<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$delivery = App\Models\Delivery::with('entregador')->find(55);

echo "=== DELIVERY #55 ===\n";
echo "Entregador ID: " . ($delivery->entregador_id ?? 'NULL') . "\n";
echo "Entregador Nome (campo): " . ($delivery->entregador_nome ?? 'NULL') . "\n";
echo "\n";

if ($delivery->entregador) {
    echo "=== ENTREGADOR (relacionamento) ===\n";
    echo "ID: " . $delivery->entregador->id . "\n";
    echo "Nome: " . $delivery->entregador->nome . "\n";
    echo "Tipo Veículo: " . ($delivery->entregador->tipo_veiculo ?? 'NULL') . "\n";
} else {
    echo "Entregador não encontrado no relacionamento\n";
}

echo "\n=== ACCESSORS ===\n";
echo "entregador_nome_completo: " . ($delivery->entregador_nome_completo ?? 'NULL') . "\n";
echo "veiculo_entregador: " . ($delivery->veiculo_entregador ?? 'NULL') . "\n";
