<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Settings do Sistema ===\n\n";

$settings = DB::table('settings')->get();

foreach($settings as $s) {
    echo "{$s->chave}: {$s->valor}\n";
}
