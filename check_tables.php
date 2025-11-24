<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Tabelas relacionadas a pedidos ===\n\n";

$tables = DB::select('SHOW TABLES');

foreach($tables as $t) {
    $table = array_values((array)$t)[0];
    if(strpos($table, 'item') !== false || strpos($table, 'pedido') !== false) {
        echo $table . "\n";
    }
}
