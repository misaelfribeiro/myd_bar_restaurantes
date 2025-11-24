<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Cliente;

// Buscar cliente
$cliente = Cliente::find(23);

if (!$cliente) {
    echo "Cliente não encontrado!\n";
    exit;
}

// Criar token
$token = $cliente->createToken('teste')->plainTextToken;

echo "Token gerado: {$token}\n\n";
echo "Use este comando para testar a API:\n\n";
echo "curl -X PUT http://localhost:8000/api/app/auth/profile \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -H \"Accept: application/json\" \\\n";
echo "  -H \"Authorization: Bearer {$token}\" \\\n";
echo "  -d '{\"endereco_rua\":\"Rua API Teste\",\"endereco_numero\":\"999\",\"endereco_bairro\":\"Centro\",\"endereco_cidade\":\"SP\",\"endereco_cep\":\"12345-678\"}'\n";
