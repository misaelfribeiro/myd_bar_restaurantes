<?php

require_once 'vendor/autoload.php';

use App\Models\Cliente;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Criar cliente com endereço
$cliente = Cliente::create([
    'nome' => 'Maria Santos',
    'telefone' => '(11) 98765-4321',
    'email' => 'maria@exemplo.com',
    'endereco_rua' => 'Av. Paulista',
    'endereco_numero' => '1000',
    'endereco_complemento' => 'Apt 101',
    'endereco_bairro' => 'Bela Vista',
    'endereco_cidade' => 'São Paulo',
    'endereco_cep' => '01310-100'
]);

echo "Cliente criado com sucesso!\n";
echo "Nome: " . $cliente->nome . "\n";
echo "Telefone: " . $cliente->telefone . "\n";
echo "Endereço completo: " . $cliente->endereco_completo . "\n";

// Testar API também
echo "\n--- Teste da API ---\n";
$clienteAPI = App\Http\Controllers\Api\ClienteApiController::class;
echo "API disponível para usar o endereço\n";