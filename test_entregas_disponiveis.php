<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Delivery;
use App\Models\Entregador;

echo "=== Teste do Endpoint entregasDisponiveis ===\n\n";

// Pegar primeiro entregador
$entregador = Entregador::first();
if (!$entregador) {
    echo "Nenhum entregador encontrado!\n";
    exit;
}

echo "Entregador: {$entregador->nome} (ID: {$entregador->id})\n\n";

// Simular a query do endpoint corrigido
$entregas = Delivery::where('disponivel_plataforma', true)
    ->whereNull('entregador_id')
    ->whereNotIn('status', ['cancelado', 'entregue'])
    ->with(['pedido'])
    ->orderBy('created_at', 'desc')
    ->get()
    ->filter(function($delivery) use ($entregador) {
        $notificados = $delivery->entregadores_notificados ?? [];
        return !in_array($entregador->id, $notificados);
    })
    ->values();

echo "Total de entregas disponíveis: " . $entregas->count() . "\n\n";

foreach($entregas as $delivery) {
    echo "ID: {$delivery->id}\n";
    echo "Pedido: {$delivery->pedido_id} (#{$delivery->pedido->numero_pedido})\n";
    echo "Cliente: {$delivery->cliente_nome}\n";
    echo "Endereço: {$delivery->endereco_completo}\n";
    echo "Valor Entregador: R$ {$delivery->valor_entregador}\n";
    echo "Status: {$delivery->status}\n";
    echo "---\n";
}
