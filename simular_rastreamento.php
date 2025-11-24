<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SIMULAÇÃO DE DELIVERY COM RASTREAMENTO ===\n\n";

// Buscar ou criar um delivery de teste
$delivery = App\Models\Delivery::with(['pedido', 'entregador'])->first();

if (!$delivery) {
    echo "❌ Nenhum delivery encontrado. Crie um delivery primeiro.\n";
    exit;
}

echo "📦 DELIVERY #{$delivery->id}\n";
echo "   Cliente: {$delivery->cliente_nome}\n";
echo "   Status: {$delivery->status}\n";
echo "   Endereço: {$delivery->endereco_rua}, {$delivery->endereco_numero} - {$delivery->endereco_bairro}\n";
echo "\n";

// Coordenadas de exemplo (São Paulo)
// Restaurante (ponto de partida): -23.550520, -46.633308
// Cliente (destino): -23.561414, -46.656174

$restauranteLat = -23.550520;
$restauranteLng = -46.633308;

$clienteLat = -23.561414;
$clienteLng = -46.656174;

// Atualizar destino
$delivery->update([
    'destino_latitude' => $clienteLat,
    'destino_longitude' => $clienteLng,
    'status' => 'saiu_entrega',
    'data_saida' => now()
]);

echo "✅ Destino atualizado:\n";
echo "   Latitude: {$clienteLat}\n";
echo "   Longitude: {$clienteLng}\n";
echo "\n";

// Simular localização inicial do entregador (no restaurante)
$delivery->update([
    'entregador_latitude' => $restauranteLat,
    'entregador_longitude' => $restauranteLng,
    'entregador_localizacao_atualizada_em' => now()
]);

echo "✅ Localização inicial do entregador:\n";
echo "   Latitude: {$restauranteLat}\n";
echo "   Longitude: {$restauranteLng}\n";
echo "\n";

// Calcular pontos intermediários para simular movimento
$steps = 5;
echo "🛵 Simulando movimento do entregador em {$steps} etapas...\n\n";

for ($i = 1; $i <= $steps; $i++) {
    $progress = $i / $steps;
    
    // Interpolação linear
    $currentLat = $restauranteLat + ($clienteLat - $restauranteLat) * $progress;
    $currentLng = $restauranteLng + ($clienteLng - $restauranteLng) * $progress;
    
    $delivery->update([
        'entregador_latitude' => $currentLat,
        'entregador_longitude' => $currentLng,
        'entregador_localizacao_atualizada_em' => now()
    ]);
    
    $percentage = round($progress * 100);
    echo "   [{$i}/{$steps}] {$percentage}% - Lat: {$currentLat}, Lng: {$currentLng}\n";
    
    sleep(1); // Aguardar 1 segundo entre atualizações
}

echo "\n✅ Chegou ao destino!\n";
echo "\n";

// Atualizar status para entregue
$delivery->update([
    'status' => 'entregue',
    'data_entrega' => now()
]);

echo "📊 STATUS FINAL:\n";
echo "   Status: {$delivery->status}\n";
echo "   Saiu às: {$delivery->data_saida->format('H:i:s')}\n";
echo "   Entregue às: {$delivery->data_entrega->format('H:i:s')}\n";
echo "   Tempo total: " . $delivery->data_saida->diffInMinutes($delivery->data_entrega) . " minutos\n";
echo "\n";

echo "🌐 Para ver no app cliente:\n";
echo "   1. Abra: http://myd.local/app-cliente\n";
echo "   2. Faça login com o cliente: {$delivery->cliente_nome}\n";
echo "   3. Acesse 'Meus Pedidos' e veja o pedido #{$delivery->pedido_id}\n";
echo "\n";

echo "=== FIM DA SIMULAÇÃO ===\n";
