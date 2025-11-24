<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Criar um entregador de teste
$entregador = \App\Models\Entregador::create([
    'nome' => 'João da Silva',
    'email' => 'joao@teste.com',
    'telefone' => '(11) 99999-9999',
    'cpf' => '123.456.789-10',
    'data_nascimento' => '1990-01-01',
    'cep' => '12345-678',
    'endereco' => 'Rua Teste',
    'numero' => '123',
    'bairro' => 'Centro',
    'cidade' => 'São Paulo',
    'estado' => 'SP',
    'tipo' => 'terceirizado',
    'tipo_veiculo' => 'moto',
    'placa_veiculo' => 'ABC-1234',
    'status' => 'pendente',
    'ativo' => true
]);

echo "Entregador criado: " . $entregador->nome . "\n";
echo "ID: " . $entregador->id . "\n";