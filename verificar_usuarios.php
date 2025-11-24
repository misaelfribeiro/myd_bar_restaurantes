<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== USUÁRIOS NO SISTEMA ===\n";
$usuarios = \App\Models\Usuario::all(['id', 'nome', 'email']);
foreach ($usuarios as $usuario) {
    echo "ID: {$usuario->id} - Nome: {$usuario->nome} - Email: {$usuario->email}\n";
}
echo "\nTotal: " . $usuarios->count() . " usuários\n";
