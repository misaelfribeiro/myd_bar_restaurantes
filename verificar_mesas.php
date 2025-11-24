<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mesa;

echo "Verificando mesas existentes...\n";
$total = Mesa::count();
echo "Total de mesas: {$total}\n\n";

if ($total > 0) {
    echo "Mesas existentes:\n";
    $mesas = Mesa::all();
    foreach ($mesas as $mesa) {
        echo "Mesa {$mesa->numero} - {$mesa->identificador} ({$mesa->capacidade} lugares) - Status: {$mesa->status}\n";
    }
} else {
    echo "Nenhuma mesa encontrada.\n";
}
