<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Verificar se existe usuário master
$user = App\Models\User::where('email', 'admin@eatsfood.com.br')->first();

if ($user) {
    echo "✓ Usuario existe:\n";
    echo "  Nome: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Tenant: {$user->tenant_code}\n";
} else {
    echo "✗ Usuario NAO existe - Criando...\n";
    
    // Criar usuário Master
    $user = App\Models\User::create([
        'name' => 'EatsFood Master',
        'email' => 'admin@eatsfood.com.br',
        'password' => Hash::make('12345678'),
        'tenant_code' => 'EATSFOOD'
    ]);
    
    echo "✓ Usuario Master criado com sucesso!\n";
    echo "  Email: admin@eatsfood.com.br\n";
    echo "  Senha: 12345678\n";
}
