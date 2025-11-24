<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$usuario = DB::table('usuarios')->where('id', 23)->first();

if ($usuario) {
    echo "\n✓ Usuário ID 23 encontrado:\n";
    echo "  Nome: {$usuario->nome}\n";
    echo "  Email: {$usuario->email}\n";
    echo "  Role: {$usuario->role}\n\n";
} else {
    echo "\n✗ Usuário ID 23 NÃO encontrado na tabela usuarios\n\n";
}
