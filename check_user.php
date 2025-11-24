<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::first();
if ($user) {
    echo "User ID: {$user->id}\n";
    echo "Nome: {$user->nome}\n";
    echo "Email: {$user->email}\n";
} else {
    echo "Nenhum usuário encontrado\n";
}
