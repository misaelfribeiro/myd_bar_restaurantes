<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$delivery = App\Models\Delivery::find(55);

if ($delivery) {
    // Atribuir entregador
    $delivery->entregador_id = 1;
    $delivery->entregador_nome = 'MAYCON DA SILVA GOMES';
    $delivery->save();
    
    echo "✅ Entregador atribuído ao delivery #55\n\n";
    
    // Recarregar com relacionamento
    $delivery = App\Models\Delivery::with('entregador')->find(55);
    
    echo "=== DADOS ATUALIZADOS ===\n";
    echo "Entregador ID: " . $delivery->entregador_id . "\n";
    echo "Entregador Nome (campo): " . $delivery->entregador_nome . "\n";
    echo "Entregador Nome (accessor): " . $delivery->entregador_nome_completo . "\n";
    echo "Veículo (accessor): " . $delivery->veiculo_entregador . "\n";
} else {
    echo "❌ Delivery #55 não encontrado\n";
}
