<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Cliente;

// Buscar cliente pelo telefone
$telefone = '9888848951';
$cliente = Cliente::where('telefone', $telefone)->first();

if (!$cliente) {
    echo "Cliente não encontrado!\n";
    exit;
}

echo "Cliente encontrado: {$cliente->nome}\n";
echo "Endereço atual:\n";
echo "  Rua: {$cliente->endereco_rua}\n";
echo "  Número: {$cliente->endereco_numero}\n";
echo "  Bairro: {$cliente->endereco_bairro}\n";
echo "  Cidade: {$cliente->endereco_cidade}\n";
echo "  CEP: {$cliente->endereco_cep}\n\n";

// Atualizar endereço
$cliente->update([
    'endereco_rua' => 'Rua de Teste',
    'endereco_numero' => '123',
    'endereco_bairro' => 'Bairro Teste',
    'endereco_cidade' => 'São Paulo',
    'endereco_cep' => '01234-567'
]);

echo "Atualizando endereço...\n\n";

// Buscar novamente
$cliente = Cliente::where('telefone', $telefone)->first();
echo "Endereço após update:\n";
echo "  Rua: {$cliente->endereco_rua}\n";
echo "  Número: {$cliente->endereco_numero}\n";
echo "  Bairro: {$cliente->endereco_bairro}\n";
echo "  Cidade: {$cliente->endereco_cidade}\n";
echo "  CEP: {$cliente->endereco_cep}\n";

echo "\n✓ Update funcionou!\n";
