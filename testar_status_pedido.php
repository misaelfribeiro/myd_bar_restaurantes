<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pedido;
use App\Models\Delivery;

echo "=== TESTE DE STATUS DE PEDIDO ===\n\n";

// Buscar um pedido de delivery recente
$pedido = Pedido::whereHas('delivery')
    ->with('delivery')
    ->orderBy('id', 'desc')
    ->first();

if (!$pedido) {
    echo "❌ Nenhum pedido de delivery encontrado!\n";
    echo "Crie um pedido pelo app primeiro.\n";
    exit;
}

echo "Pedido encontrado: #{$pedido->id}\n";
echo "Status atual: {$pedido->status}\n";
echo "Cliente ID: {$pedido->delivery->cliente_id}\n\n";

// Simular progressão de status
$statusFlow = [
    'aberto' => 'Pedido recebido',
    'confirmado' => 'Pedido confirmado pelo restaurante',
    'preparando' => 'Pedido sendo preparado',
    'pronto' => 'Pedido pronto',
    'saiu_entrega' => 'Pedido saiu para entrega',
    'entregue' => 'Pedido entregue'
];

echo "Selecione o novo status:\n\n";
$i = 1;
foreach ($statusFlow as $status => $desc) {
    $current = ($pedido->status === $status) ? ' ← ATUAL' : '';
    echo "{$i}. {$status} - {$desc}{$current}\n";
    $i++;
}
echo "\n0. Cancelar pedido\n";
echo "\nDigite o número da opção: ";

$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$opcao = (int)trim($line);
fclose($handle);

$statusArray = array_keys($statusFlow);
$novoStatus = null;

if ($opcao === 0) {
    $novoStatus = 'cancelado';
} elseif ($opcao >= 1 && $opcao <= count($statusArray)) {
    $novoStatus = $statusArray[$opcao - 1];
} else {
    echo "\n❌ Opção inválida!\n";
    exit;
}

// Atualizar status
echo "\n⏳ Atualizando pedido #{$pedido->id} para status: {$novoStatus}...\n";

$pedido->status = $novoStatus;
$pedido->save();

// Se for delivery, atualizar também o status do delivery
if ($pedido->delivery) {
    $deliveryStatusMap = [
        'aberto' => 'pendente',
        'confirmado' => 'confirmado',
        'preparando' => 'confirmado',
        'pronto' => 'confirmado',
        'saiu_entrega' => 'em_transito',
        'entregue' => 'entregue',
        'cancelado' => 'cancelado'
    ];
    
    $deliveryStatus = $deliveryStatusMap[$novoStatus] ?? 'pendente';
    $pedido->delivery->status = $deliveryStatus;
    $pedido->delivery->save();
    
    echo "✅ Status do delivery atualizado para: {$deliveryStatus}\n";
}

echo "✅ Pedido atualizado com sucesso!\n\n";
echo "Status anterior: {$pedido->getOriginal('status')}\n";
echo "Status novo: {$pedido->status}\n\n";

echo "Agora abra o app cliente e veja o pedido #{$pedido->id} sendo atualizado em tempo real!\n";
echo "O polling vai atualizar a cada 10 segundos.\n";
