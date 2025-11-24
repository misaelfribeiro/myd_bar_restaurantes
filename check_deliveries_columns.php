<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Estrutura da tabela deliveries ===\n\n";

$columns = DB::select("SHOW COLUMNS FROM deliveries");

foreach($columns as $col) {
    echo "{$col->Field} - {$col->Type}\n";
}
