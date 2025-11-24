<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Hash;

$user = App\Models\User::where('email', 'admin@eatsfood.com.br')->first();
$user->password = Hash::make('12345678');
$user->save();

echo "✓ Senha do usuário Master resetada!\n";
echo "  Email: admin@eatsfood.com.br\n";
echo "  Senha: 12345678\n";
