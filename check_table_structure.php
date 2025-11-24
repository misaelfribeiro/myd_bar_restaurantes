<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Estrutura da tabela pedidos ===\n\n";

$columns = DB::select("SHOW COLUMNS FROM pedidos");

foreach ($columns as $column) {
    echo "{$column->Field} ({$column->Type}) - Null: {$column->Null} - Default: {$column->Default}\n";
}
