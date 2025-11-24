<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Cliente;

$clientes = Cliente::all();
echo "Total de clientes: " . $clientes->count() . "\n\n";

foreach($clientes as $c) {
    echo "ID: {$c->id}, Nome: {$c->nome}, Tel: {$c->telefone}\n";
    echo "  Endereço: {$c->endereco_rua}, {$c->endereco_numero} - {$c->endereco_bairro}\n\n";
}
