<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Estrutura ai_conversation_sessions ===\n\n";
$cols = DB::select('SHOW COLUMNS FROM ai_conversation_sessions');
foreach($cols as $col) {
    echo "{$col->Field} ({$col->Type}) {$col->Key}\n";
}

echo "\n=== Cliente válido ===\n";
$cliente = DB::table('clientes')->first();
if($cliente) {
    echo "ID: {$cliente->id}, Nome: {$cliente->nome}\n";
}
