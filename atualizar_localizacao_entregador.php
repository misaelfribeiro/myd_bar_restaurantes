<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

if ($argc < 2) {
    echo "Uso: php atualizar_localizacao_entregador.php <delivery_id>\n";
    exit(1);
}

$deliveryId = $argv[1];

echo "=== SIMULAÇÃO DE MOVIMENTO DO ENTREGADOR ===\n\n";

$delivery = App\Models\Delivery::find($deliveryId);

if (!$delivery) {
    echo "❌ Delivery #{$deliveryId} não encontrado!\n";
    exit(1);
}

echo "📦 DELIVERY #{$delivery->id}\n";
echo "   Cliente: {$delivery->cliente_nome}\n";
echo "   Status: {$delivery->status}\n";
echo "\n";

// Posições
$origemLat = $delivery->entregador_latitude;
$origemLng = $delivery->entregador_longitude;
$destinoLat = $delivery->destino_latitude;
$destinoLng = $delivery->destino_longitude;

echo "📍 Origem: {$origemLat}, {$origemLng}\n";
echo "📍 Destino: {$destinoLat}, {$destinoLng}\n";
echo "\n";

// Calcular distância aproximada
$distance = sqrt(pow($destinoLat - $origemLat, 2) + pow($destinoLng - $origemLng, 2));
$steps = 10; // 10 atualizações até o destino

echo "🛵 Movendo entregador em {$steps} etapas (aguarde {$steps}s)...\n";
echo "   Pressione Ctrl+C para parar\n\n";

for ($i = 1; $i <= $steps; $i++) {
    $progress = $i / $steps;
    
    // Interpolação com um pouco de variação aleatória para parecer mais real
    $randomOffsetLat = (rand(-10, 10) / 100000); // Pequena variação
    $randomOffsetLng = (rand(-10, 10) / 100000);
    
    $currentLat = $origemLat + ($destinoLat - $origemLat) * $progress + $randomOffsetLat;
    $currentLng = $origemLng + ($destinoLng - $origemLng) * $progress + $randomOffsetLng;
    
    // Atualizar no banco
    $delivery->update([
        'entregador_latitude' => $currentLat,
        'entregador_longitude' => $currentLng,
        'entregador_localizacao_atualizada_em' => now()
    ]);
    
    $percentage = round($progress * 100);
    $bar = str_repeat('█', floor($percentage / 5)) . str_repeat('░', 20 - floor($percentage / 5));
    
    echo sprintf("   [%s] %3d%% - Lat: %.6f, Lng: %.6f\n", 
        $bar, $percentage, $currentLat, $currentLng
    );
    
    sleep(1);
}

echo "\n✅ Entregador chegou ao destino!\n\n";

// Marcar como entregue
$delivery->update([
    'status' => 'entregue',
    'data_entrega' => now()
]);

echo "📊 Delivery atualizado para 'entregue'\n";
echo "   Tempo total: " . $delivery->data_saida->diffInMinutes($delivery->data_entrega) . " minutos\n";
echo "\n";

echo "=== SIMULAÇÃO CONCLUÍDA ===\n";
