<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'misael_ribeiro@hotmail.com';
$clientes = \App\Models\Cliente::where('email', $email)->get();

echo "Clientes com email: {$email}" . PHP_EOL;
echo "Total: " . $clientes->count() . PHP_EOL . PHP_EOL;

foreach($clientes as $c) {
    echo "ID: {$c->id} - Nome: {$c->nome} - Criado em: {$c->created_at}" . PHP_EOL;
}

echo PHP_EOL . "Deseja remover o duplicado (ID 32)? (s/n): ";
$resposta = trim(fgets(STDIN));

if (strtolower($resposta) === 's') {
    $duplicado = \App\Models\Cliente::find(32);
    if ($duplicado) {
        $duplicado->delete();
        echo "Cliente ID 32 removido com sucesso!" . PHP_EOL;
    }
} else {
    echo "Operação cancelada." . PHP_EOL;
}
