<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pagamento = \App\Models\Pagamento::first();

if ($pagamento) {
    echo "Pagamento encontrado:\n";
    echo "ID: {$pagamento->id}\n";
    echo "Status: {$pagamento->status}\n";
    echo "Valor: {$pagamento->valor}\n";
    echo "Data: {$pagamento->data_pagamento}\n";
    echo "Forma: {$pagamento->forma_pagamento}\n";
} else {
    echo "Nenhum pagamento encontrado\n";
}
