<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Colunas da tabela clientes:" . PHP_EOL;
$columns = DB::select('SHOW COLUMNS FROM clientes');
foreach($columns as $col) {
    echo $col->Field . PHP_EOL;
}

echo PHP_EOL . "Procurando 'ponto_referencia'...";
$found = false;
foreach($columns as $col) {
    if ($col->Field === 'ponto_referencia') {
        $found = true;
        break;
    }
}
echo $found ? " ✓ ENCONTRADA" : " ✗ NÃO EXISTE";
echo PHP_EOL;
