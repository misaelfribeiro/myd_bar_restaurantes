<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

echo "=== TESTE DELIVERY ===\n\n";

// Verificar se há deliveries no banco
$deliveries = \App\Models\Delivery::all();
echo "Total de deliveries: " . $deliveries->count() . "\n\n";

if ($deliveries->count() > 0) {
    echo "Últimos 3 deliveries:\n";
    foreach ($deliveries->take(3) as $delivery) {
        echo "ID: {$delivery->id}\n";
        echo "Cliente: {$delivery->cliente_nome}\n";
        echo "Telefone: {$delivery->cliente_telefone}\n";
        echo "Status: {$delivery->status}\n";
        echo "Criado em: {$delivery->created_at}\n";
        echo "---\n";
    }
}

// Teste de criação
echo "\n=== TESTE DE CRIAÇÃO ===\n";

$dados = [
    'cliente_nome' => 'João Silva',
    'cliente_telefone' => '(11) 99999-9999',
    'cliente_email' => 'joao@teste.com',
    'endereco_rua' => 'Rua das Flores',
    'endereco_numero' => '123',
    'endereco_complemento' => 'Apto 45',
    'endereco_bairro' => 'Centro',
    'endereco_cidade' => 'São Paulo',
    'endereco_cep' => '01234-567',
    'endereco_referencia' => 'Próximo ao banco',
    'taxa_entrega' => 5.50,
    'tempo_estimado' => 30,
    'distancia_km' => 2.5,
    'observacoes' => 'Teste de criação',
    'observacoes_internas' => 'Dados de teste'
];

try {
    $delivery = \App\Models\Delivery::create($dados);
    echo "Delivery criado com sucesso!\n";
    echo "ID: {$delivery->id}\n";
    echo "Cliente: {$delivery->cliente_nome}\n";
    echo "Status: {$delivery->status}\n";
    
} catch (\Exception $e) {
    echo "ERRO na criação:\n";
    echo "Tipo: " . get_class($e) . "\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}