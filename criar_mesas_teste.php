<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

// Criar mesas de teste
use App\Models\Mesa;

echo "Criando mesas de teste...\n";

$mesas = [
    ['identificador' => 'Mesa 1', 'lugares' => 4],
    ['identificador' => 'Mesa 2', 'lugares' => 2], 
    ['identificador' => 'Mesa 3', 'lugares' => 6],
    ['identificador' => 'Mesa 4', 'lugares' => 8],
    ['identificador' => 'Mesa 5', 'lugares' => 4],
    ['identificador' => 'VIP 1', 'lugares' => 2],
    ['identificador' => 'VIP 2', 'lugares' => 4],
];

foreach ($mesas as $mesaData) {
    try {
        $mesa = Mesa::firstOrCreate(
            ['identificador' => $mesaData['identificador']], 
            $mesaData
        );
        
        if ($mesa->wasRecentlyCreated) {
            echo "✓ Mesa criada: {$mesaData['identificador']} ({$mesaData['lugares']} lugares)\n";
        } else {
            echo "- Mesa já existe: {$mesaData['identificador']}\n";
        }
    } catch (Exception $e) {
        echo "✗ Erro ao criar mesa {$mesaData['identificador']}: " . $e->getMessage() . "\n";
    }
}

echo "\nTotal de mesas no sistema: " . Mesa::count() . "\n";
echo "Concluído!\n";
