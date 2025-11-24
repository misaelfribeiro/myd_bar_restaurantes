<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Debug Auth ===\n\n";

// Verificar se está logado
echo "Auth guard 'web' check: " . (auth()->guard('web')->check() ? "SIM" : "NAO") . "\n";
echo "Auth guard 'admin' check: " . (auth()->guard('admin')->check() ? "SIM" : "NAO") . "\n";

if (auth()->guard('admin')->check()) {
    $user = auth()->guard('admin')->user();
    echo "\nUsuario logado (admin guard):\n";
    echo "  Nome: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Tenant: {$user->tenant_code}\n";
}

if (auth()->guard('web')->check()) {
    $user = auth()->guard('web')->user();
    echo "\nUsuario logado (web guard):\n";
    echo "  Nome: {$user->nome}\n";
    echo "  Email: {$user->email}\n";
}

// Testar função isMaster
echo "\nisMaster(): " . (isMaster() ? "TRUE" : "FALSE") . "\n";
