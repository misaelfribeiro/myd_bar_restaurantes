<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pedido;
use App\Models\Mesa;

echo "Criando novos pedidos para teste de mesa...\n\n";

// Criar 2 pedidos finalizados para a mesa 2
$pedido1 = Pedido::create([
    'mesa_id' => 2,
    'usuario_id' => 1,
    'status' => 'finalizado',
    'total' => 45.50,
    'observacoes' => 'Pedido 1 - Teste de pagamento de mesa'
]);

$pedido2 = Pedido::create([
    'mesa_id' => 2,
    'usuario_id' => 1,
    'status' => 'finalizado', 
    'total' => 32.75,
    'observacoes' => 'Pedido 2 - Teste de pagamento de mesa'
]);

echo "Pedidos criados com sucesso!\n";
echo "Pedido 1 - ID: {$pedido1->id} - Total: R$ " . number_format($pedido1->total, 2, ',', '.') . "\n";
echo "Pedido 2 - ID: {$pedido2->id} - Total: R$ " . number_format($pedido2->total, 2, ',', '.') . "\n";

$totalMesa = $pedido1->total + $pedido2->total;
echo "\nTotal da mesa: R$ " . number_format($totalMesa, 2, ',', '.') . "\n";
echo "\nVocê pode testar o pagamento da mesa 2 via API agora.\n";