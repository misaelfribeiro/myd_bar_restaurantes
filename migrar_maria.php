<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== MOVENDO MARIA PARA TABELA USUARIOS ===\n\n";

// Busca Maria na tabela users
$mariaMaster = DB::table('users')
    ->where('email', 'maria@hotmail.com')
    ->first();

if (!$mariaMaster) {
    echo "Maria não encontrada na tabela users\n";
    exit;
}

// Verifica se já existe na tabela usuarios
$mariaExiste = DB::table('usuarios')
    ->where('email', 'maria@hotmail.com')
    ->first();

if ($mariaExiste) {
    echo "Maria já existe na tabela usuarios!\n";
    echo "ID: {$mariaExiste->id}\n";
    echo "Nome: {$mariaExiste->nome}\n";
    echo "Email: {$mariaExiste->email}\n";
    echo "Role: {$mariaExiste->role}\n";
    echo "Tenant: {$mariaExiste->tenant_code}\n\n";
    
    echo "Removendo Maria da tabela users...\n";
    DB::table('users')->where('id', $mariaMaster->id)->delete();
    echo "✓ Maria removida da tabela users\n";
    exit;
}

// Insere Maria na tabela usuarios
echo "Inserindo Maria na tabela usuarios...\n";
DB::table('usuarios')->insert([
    'nome' => $mariaMaster->name,
    'email' => $mariaMaster->email,
    'password' => $mariaMaster->password,
    'role' => 'admin', // Administrador da empresa
    'tenant_code' => $mariaMaster->tenant_code,
    'created_at' => now(),
    'updated_at' => now(),
]);

$novoId = DB::getPdo()->lastInsertId();
echo "✓ Maria inserida na tabela usuarios (ID: {$novoId})\n\n";

// Remove da tabela users
echo "Removendo Maria da tabela users...\n";
DB::table('users')->where('id', $mariaMaster->id)->delete();
echo "✓ Maria removida da tabela users\n\n";

echo "=== MIGRAÇÃO CONCLUÍDA ===\n";
echo "Maria agora está na tabela usuarios e pode fazer login em:\n";
echo "http://127.0.0.1:8080/login\n";
