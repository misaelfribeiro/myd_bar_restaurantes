<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testando Update Manual ===\n\n";

$payment = \App\Models\Payment::find(2);

echo "Status ANTES: {$payment->status}\n";

$payment->status = 'approved';
$saved = $payment->save();

echo "Save result: " . ($saved ? 'TRUE' : 'FALSE') . "\n";

$payment->refresh();
echo "Status DEPOIS: {$payment->status}\n";

// Verificar no banco direto
$direct = \DB::table('payments')->where('id', 2)->first();
echo "Status no banco (direto): {$direct->status}\n";
