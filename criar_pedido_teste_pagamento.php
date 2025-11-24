<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\User;

echo "Criando pedido de teste para pagamento...\n";

$pedido = Pedido::create([
    'mesa_id' => 2,
    'usuario_id' => 1,
    'status' => 'finalizado',
    'total' => 89.90,
    'observacoes' => 'Pedido de teste para pagamento via API'
]);

echo "Pedido criado com sucesso!\n";
echo "ID: " . $pedido->id . "\n";
echo "Total: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
echo "Status: " . $pedido->status . "\n";
echo "\nVocê pode testar o pagamento com este pedido.\n";