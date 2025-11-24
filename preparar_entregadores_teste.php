<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Entregador;

echo "=== PREPARAR ENTREGADORES PARA TESTE ===\n\n";

// Coordenadas próximas ao restaurante (M F Dias Da Silva)
// Lat: -2.49681804, Long: -44.22904324

$entregador1 = Entregador::find(1); // MAYCON
$entregador1->disponivel = true;
$entregador1->localizacao_atual = [
    'latitude' => -2.497, // ~100m de distância
    'longitude' => -44.229,
    'accuracy' => 10,
    'updated_at' => now()->toDateTimeString()
];
$entregador1->save();
echo "✅ MAYCON (ID:1) - Disponível e com localização (próximo do restaurante)\n";

$entregador3 = Entregador::find(3); // MARCIO
$entregador3->disponivel = true;
$entregador3->localizacao_atual = [
    'latitude' => -2.500, // ~300m de distância
    'longitude' => -44.230,
    'accuracy' => 10,
    'updated_at' => now()->toDateTimeString()
];
$entregador3->save();
echo "✅ MARCIO (ID:3) - Disponível e com localização (próximo do restaurante)\n";

echo "\n=== CONCLUÍDO ===\n";
