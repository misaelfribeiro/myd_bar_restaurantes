<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

// Verificar se já existe
$existing = \App\Models\User::where('email', 'admin@admin.com')->first();

if (!$existing) {
    $user = \App\Models\User::create([
        'name' => 'Admin Teste',
        'email' => 'admin@admin.com', 
        'password' => bcrypt('123456'),
        'role' => 'admin'
    ]);
    
    echo "Usuário criado: " . $user->email . "\n";
} else {
    echo "Usuário já existe: " . $existing->email . "\n";
}