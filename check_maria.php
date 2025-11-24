<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICANDO USUÁRIO MARIA ===\n\n";

// Verifica na tabela users (Master)
$usersMaster = DB::table('users')
    ->where('email', 'like', '%maria%')
    ->orWhere('name', 'like', '%MARIA%')
    ->get();

if ($usersMaster->count() > 0) {
    echo "ENCONTRADO NA TABELA USERS (MASTER):\n";
    foreach ($usersMaster as $user) {
        echo "ID: {$user->id}\n";
        echo "Nome: {$user->name}\n";
        echo "Email: {$user->email}\n";
        echo "Tenant: {$user->tenant_code}\n";
        echo "---\n";
    }
    echo "\n";
}

// Verifica na tabela usuarios (Empresas)
$usuariosEmpresa = DB::table('usuarios')
    ->where('email', 'like', '%maria%')
    ->orWhere('nome', 'like', '%MARIA%')
    ->get();

if ($usuariosEmpresa->count() > 0) {
    echo "ENCONTRADO NA TABELA USUARIOS (EMPRESAS):\n";
    foreach ($usuariosEmpresa as $usuario) {
        echo "ID: {$usuario->id}\n";
        echo "Nome: {$usuario->nome}\n";
        echo "Email: {$usuario->email}\n";
        echo "Role: {$usuario->role}\n";
        echo "Tenant: {$usuario->tenant_code}\n";
        echo "---\n";
    }
    echo "\n";
}

// Verifica sessão atual
if (auth()->guard('admin')->check()) {
    $user = auth()->guard('admin')->user();
    echo "SESSÃO ATIVA (GUARD ADMIN):\n";
    echo "ID: {$user->id}\n";
    echo "Nome: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Tenant: {$user->tenant_code}\n";
}

if (auth()->guard('web')->check()) {
    $user = auth()->guard('web')->user();
    echo "SESSÃO ATIVA (GUARD WEB):\n";
    echo "ID: {$user->id}\n";
    echo "Nome: {$user->nome}\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
    echo "Tenant: {$user->tenant_code}\n";
}
