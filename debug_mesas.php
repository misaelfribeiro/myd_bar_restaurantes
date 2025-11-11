<?php

require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mesa;
use App\Models\Pedido;

echo "=== DEBUG MESAS ===\n";
echo "Todas as mesas no banco:\n";

$mesas = Mesa::all();
foreach ($mesas as $mesa) {
    echo "ID: {$mesa->id} | Numero: {$mesa->numero} | Identificador: " . ($mesa->identificador ?: 'null') . "\n";
}

echo "\n=== MESAS OCUPADAS ===\n";
$mesasOcupadas = Mesa::whereHas('pedidos', function($query) { 
    $query->where('status', 'aberto'); 
})->with(['pedidos' => function($query) { 
    $query->where('status', 'aberto')->latest(); 
}])->get();

foreach ($mesasOcupadas as $mesa) {
    $pedido = $mesa->pedidos->first();
    echo "Mesa ID: {$mesa->id} | Numero: {$mesa->numero} | Identificador: " . ($mesa->identificador ?: 'null');
    echo " | Pedido: " . ($pedido ? $pedido->id : 'none') . "\n";
}

echo "\n=== VERIFICANDO DUPLICATAS ===\n";
$numeros = $mesas->pluck('numero')->toArray();
$duplicados = array_count_values($numeros);
foreach ($duplicados as $numero => $count) {
    if ($count > 1) {
        echo "Mesa numero {$numero} aparece {$count} vezes!\n";
    }
}

$identificadores = $mesas->pluck('identificador')->filter()->toArray();
$duplicadosId = array_count_values($identificadores);
foreach ($duplicadosId as $id => $count) {
    if ($count > 1) {
        echo "Identificador '{$id}' aparece {$count} vezes!\n";
    }
}
