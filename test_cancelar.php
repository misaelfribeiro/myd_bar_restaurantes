<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$delivery = App\Models\Delivery::find(78);

if ($delivery) {
    echo "Antes:\n";
    echo "disponivel_plataforma: " . ($delivery->disponivel_plataforma ? 'true' : 'false') . "\n";
    echo "tipo_entrega: " . ($delivery->tipo_entrega ?? 'NULL') . "\n";
    
    $delivery->update([
        'disponivel_plataforma' => false,
        'disponibilizado_em' => null,
        'tentativas_notificacao' => 0,
        'ultima_notificacao_em' => null,
        'entregadores_notificados' => null,
        'raio_busca_km' => 5
    ]);
    
    $delivery->refresh();
    
    echo "\nDepois:\n";
    echo "disponivel_plataforma: " . ($delivery->disponivel_plataforma ? 'true' : 'false') . "\n";
    echo "tipo_entrega: " . ($delivery->tipo_entrega ?? 'NULL') . "\n";
    echo "\nSucesso!\n";
} else {
    echo "Delivery não encontrada\n";
}
