<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mesa;

echo "Criando 10 mesas...\n";

for ($i = 1; $i <= 10; $i++) {
    Mesa::create([
        'numero' => $i,
        'identificador' => 'Mesa ' . str_pad($i, 2, '0', STR_PAD_LEFT),
        'lugares' => rand(2, 6),
        'capacidade' => rand(2, 6),
        'status' => 'disponivel'
    ]);
    echo "✅ Mesa {$i} criada\n";
}

echo "\n✅ Todas as 10 mesas foram criadas com sucesso!\n";

// Verificar total
$total = Mesa::count();
echo "Total de mesas no banco: {$total}\n";
