<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pedido;
use App\Models\Mesa;

echo "=== CRIANDO PEDIDO PARA TESTE DO BOTÃO FINALIZAR ===\n\n";

// Criar novo pedido para mesa 2
$pedido = Pedido::create([
    'mesa_id' => 2,
    'usuario_id' => 1,
    'status' => 'finalizado',
    'total' => 67.80,
    'observacoes' => 'Pedido para teste do botão finalizar com API'
]);

echo "✅ Pedido criado com sucesso!\n";
echo "📋 ID: {$pedido->id}\n";
echo "🪑 Mesa: 2\n";
echo "💰 Total: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
echo "📊 Status: {$pedido->status}\n";

echo "\n🎯 Agora você pode testar o botão 'Finalizar' na Mesa 2!\n";
echo "🔄 Recarregue a página do garçom se necessário.\n";