<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$columns = DB::select('SHOW COLUMNS FROM pedidos');
echo "Colunas da tabela pedidos:\n";
foreach($columns as $col) {
    echo "- " . $col->Field . " (" . $col->Type . ")\n";
}

// Verificar se tem troco_para
$hasFormaPagamento = false;
$hasTrocoPara = false;
foreach($columns as $col) {
    if ($col->Field === 'forma_pagamento') $hasFormaPagamento = true;
    if ($col->Field === 'troco_para') $hasTrocoPara = true;
}

echo "\n";
echo "forma_pagamento existe: " . ($hasFormaPagamento ? 'SIM' : 'NÃO') . "\n";
echo "troco_para existe: " . ($hasTrocoPara ? 'SIM' : 'NÃO') . "\n";
