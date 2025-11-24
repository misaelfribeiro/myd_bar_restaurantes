<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ESTRUTURA DA TABELA CLIENTES ===\n\n";

$columns = DB::select('SHOW COLUMNS FROM clientes');
foreach ($columns as $col) {
    echo "📋 {$col->Field} ({$col->Type})\n";
}

echo "\n=== CLIENTE DE TESTE (ID 2) ===\n\n";
$cliente = DB::table('clientes')->where('id', 2)->first();
if ($cliente) {
    echo "ID: {$cliente->id}\n";
    echo "Nome: {$cliente->nome}\n";
    if (isset($cliente->email)) echo "Email: {$cliente->email}\n";
    if (isset($cliente->telefone)) echo "Telefone: {$cliente->telefone}\n";
    if (isset($cliente->endereco)) echo "Endereço: {$cliente->endereco}\n";
}
