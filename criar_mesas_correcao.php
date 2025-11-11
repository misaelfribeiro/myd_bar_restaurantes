<?php

require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mesa;

echo "=== CRIANDO MESAS DE TESTE ===\n";

// Verificar mesas existentes
$existentes = Mesa::count();
echo "Mesas existentes: {$existentes}\n";

if ($existentes == 0) {
    // Criar mesas de teste
    $mesas = [
        ['numero' => 1, 'identificador' => null, 'capacidade' => 4, 'lugares' => 4],
        ['numero' => 2, 'identificador' => 'Mesa VIP', 'capacidade' => 2, 'lugares' => 2],
        ['numero' => 3, 'identificador' => null, 'capacidade' => 6, 'lugares' => 6],
        ['numero' => 4, 'identificador' => 'Mesa Varanda', 'capacidade' => 4, 'lugares' => 4],
        ['numero' => 5, 'identificador' => null, 'capacidade' => 8, 'lugares' => 8],
        ['numero' => 6, 'identificador' => null, 'capacidade' => 4, 'lugares' => 4],
        ['numero' => 7, 'identificador' => 'Mesa Jardim', 'capacidade' => 6, 'lugares' => 6],
        ['numero' => 8, 'identificador' => null, 'capacidade' => 2, 'lugares' => 2],
    ];

    foreach ($mesas as $mesaData) {
        $mesa = Mesa::create($mesaData);
        $identificador = $mesa->identificador ?: 'Mesa ' . $mesa->numero;
        echo "Criada: {$identificador} (ID: {$mesa->id})\n";
    }
} else {
    echo "Mesas já existem. Listando:\n";
    Mesa::all()->each(function($mesa) {
        $identificador = $mesa->identificador ?: 'Mesa ' . $mesa->numero;
        echo "- {$identificador} (ID: {$mesa->id})\n";
    });
}

echo "\n=== MESAS CRIADAS COM SUCESSO ===\n";
echo "Total: " . Mesa::count() . " mesas\n";
