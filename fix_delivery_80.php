<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$delivery = App\Models\Delivery::find(80);
$delivery->update(['status' => 'preparando']);

echo "Status atualizado para: " . $delivery->status . "\n";
