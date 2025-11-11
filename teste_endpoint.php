<?php

require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mesa;
use App\Models\Pedido;

echo "=== TESTE ENDPOINT DASHBOARD-DATA ===\n";

// Simular o que o controller faz
$userId = 1;

$mesasOcupadas = Mesa::whereHas('pedidos', function($query) { 
    $query->where('status', 'aberto'); 
})->count();

$mesasOcupadasInfo = Mesa::with(['pedidos' => function($query) { 
    $query->where('status', 'aberto')->latest(); 
}])->whereHas('pedidos', function($query) { 
    $query->where('status', 'aberto');
})->get();

$mesasDisponiveisInfo = Mesa::whereDoesntHave('pedidos', function($query) { 
    $query->where('status', 'aberto');
})->limit(6)->get();

echo "Mesas ocupadas: {$mesasOcupadas}\n";
echo "Mesas ocupadas info: " . $mesasOcupadasInfo->count() . "\n";
echo "Mesas disponíveis info: " . $mesasDisponiveisInfo->count() . "\n";

echo "\n--- MESAS OCUPADAS ---\n";
foreach ($mesasOcupadasInfo as $mesa) {
    $pedido = $mesa->pedidos->first();
    $nome = $mesa->identificador ?: "Mesa {$mesa->numero}";
    echo "- {$nome} (Pedido: {$pedido->id})\n";
}

echo "\n--- MESAS DISPONÍVEIS ---\n";
foreach ($mesasDisponiveisInfo as $mesa) {
    $nome = $mesa->identificador ?: "Mesa {$mesa->numero}";
    echo "- {$nome}\n";
}
