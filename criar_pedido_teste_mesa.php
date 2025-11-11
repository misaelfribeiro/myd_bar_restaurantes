<?php

require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\ItemPedido;

echo "=== CRIANDO PEDIDO DE TESTE ===\n";

// Buscar uma mesa
$mesa = Mesa::first();
echo "Mesa selecionada: {$mesa->numero} (ID: {$mesa->id})\n";

// Criar um pedido aberto nesta mesa
$pedido = Pedido::create([
    'mesa_id' => $mesa->id,
    'usuario_id' => 1,
    'total' => 25.50,
    'status' => 'aberto',
    'observacoes' => 'Pedido de teste'
]);

echo "Pedido criado: ID {$pedido->id}\n";

// Verificar mesas ocupadas
$mesasOcupadas = Mesa::whereHas('pedidos', function($query) { 
    $query->where('status', 'aberto'); 
})->count();

echo "Mesas ocupadas agora: {$mesasOcupadas}\n";
echo "=== TESTE CONCLUÍDO ===\n";
