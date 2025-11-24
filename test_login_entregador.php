<?php

// Teste direto da API de login do entregador
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simular request de login
$request = Illuminate\Http\Request::create(
    '/api/entregadores/auth/login',
    'POST',
    [
        'email' => 'tesdsd@teste.com.br',
        'senha' => '123456'
    ],
    [],
    [],
    ['CONTENT_TYPE' => 'application/json'],
    json_encode([
        'email' => 'tesdsd@teste.com.br',
        'senha' => '123456'
    ])
);

$response = $kernel->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Headers:\n";
foreach ($response->headers->all() as $key => $values) {
    echo "  $key: " . implode(', ', $values) . "\n";
}
echo "\nBody:\n";
echo $response->getContent() . "\n";

$kernel->terminate($request, $response);
