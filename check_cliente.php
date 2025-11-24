<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$cliente = DB::table('clientes')->first();
if ($cliente) {
    echo "Cliente ID: {$cliente->id}\n";
    echo "Nome: {$cliente->nome}\n";
    echo "Telefone: {$cliente->telefone}\n";
} else {
    echo "Nenhum cliente encontrado\n";
}
