<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$entregadores = App\Models\Entregador::all();

echo "===== ENTREGADORES E TOKENS FCM =====\n\n";

foreach ($entregadores as $e) {
    echo "ID: {$e->id}\n";
    echo "Nome: {$e->nome}\n";
    echo "Email: {$e->email}\n";
    echo "Disponível: " . ($e->disponivel ? 'Sim' : 'Não') . "\n";
    echo "Device Token: " . ($e->device_token ?? 'NULL') . "\n";
    echo "Notificações Push: " . ($e->notificacoes_push ?? 'NULL') . "\n";
    echo "Localização: " . (is_array($e->localizacao_atual) ? json_encode($e->localizacao_atual) : $e->localizacao_atual ?? 'NULL') . "\n";
    echo str_repeat('-', 50) . "\n";
}

// Buscar entregas disponíveis
$entregas = App\Models\Delivery::where('disponivel_plataforma', true)
    ->whereNull('entregador_id')
    ->get();

echo "\n===== ENTREGAS DISPONÍVEIS =====\n";
echo "Total: " . $entregas->count() . "\n\n";

foreach ($entregas as $delivery) {
    echo "ID: {$delivery->id}\n";
    echo "Status: {$delivery->status}\n";
    echo "Cliente: {$delivery->cliente_nome}\n";
    echo "Valor Entregador: R$ {$delivery->valor_entregador}\n";
    echo "Notificados: " . json_encode($delivery->entregadores_notificados ?? []) . "\n";
    echo str_repeat('-', 50) . "\n";
}
