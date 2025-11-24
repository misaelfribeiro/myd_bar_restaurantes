<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CRIAR DELIVERY COM RASTREAMENTO ATIVO ===\n\n";

// Buscar um cliente
$cliente = App\Models\Cliente::first();

if (!$cliente) {
    echo "❌ Nenhum cliente encontrado.\n";
    exit;
}

// Buscar um pedido sem delivery
$pedido = App\Models\Pedido::whereDoesntHave('delivery')
    ->where('status', '!=', 'cancelado')
    ->first();

if (!$pedido) {
    echo "⚠️ Nenhum pedido sem delivery encontrado. Criando novo pedido...\n";
    
    // Criar pedido de teste
    $pedido = App\Models\Pedido::create([
        'cliente_id' => $cliente->id,
        'total' => 50.00,
        'status' => 'confirmado',
        'tipo_pedido' => 'delivery',
        'forma_pagamento' => 'dinheiro'
    ]);
    
    echo "✅ Pedido #{$pedido->id} criado\n\n";
}

// Buscar ou criar entregador
$entregador = App\Models\Entregador::where('status', 'ativo')->first();

if (!$entregador) {
    echo "⚠️ Nenhum entregador encontrado. Criando entregador de teste...\n";
    
    $entregador = App\Models\Entregador::create([
        'nome' => 'Carlos Entregador',
        'telefone' => '(11) 98765-4321',
        'tipo_veiculo' => 'moto',
        'placa_veiculo' => 'ABC-1234',
        'status' => 'ativo',
        'disponivel' => 1
    ]);
    
    echo "✅ Entregador '{$entregador->nome}' criado\n\n";
}

// Coordenadas
$restauranteLat = -23.550520;  // Avenida Paulista (exemplo)
$restauranteLng = -46.633308;

$clienteLat = -23.561414;      // 1.2km de distância
$clienteLng = -46.656174;

// Criar delivery
$delivery = App\Models\Delivery::create([
    'cliente_id' => $cliente->id,
    'cliente_nome' => $cliente->nome,
    'cliente_telefone' => $cliente->telefone,
    'cliente_email' => $cliente->email,
    'endereco_rua' => $cliente->endereco_rua ?? 'Rua Exemplo',
    'endereco_numero' => $cliente->endereco_numero ?? '123',
    'endereco_complemento' => $cliente->endereco_complemento,
    'endereco_bairro' => $cliente->endereco_bairro ?? 'Centro',
    'endereco_cidade' => $cliente->endereco_cidade ?? 'São Paulo',
    'endereco_cep' => $cliente->endereco_cep ?? '01310-100',
    'pedido_id' => $pedido->id,
    'taxa_entrega' => 8.00,
    'tempo_estimado' => 30,
    'status' => 'saiu_entrega',
    'data_saida' => now(),
    'entregador_id' => $entregador->id,
    'entregador_nome' => $entregador->nome,
    'entregador_telefone' => $entregador->telefone,
    'destino_latitude' => $clienteLat,
    'destino_longitude' => $clienteLng,
    'entregador_latitude' => $restauranteLat,
    'entregador_longitude' => $restauranteLng,
    'entregador_localizacao_atualizada_em' => now()
]);

echo "✅ DELIVERY CRIADO COM SUCESSO!\n\n";
echo "📦 DELIVERY #{$delivery->id}\n";
echo "   Cliente: {$delivery->cliente_nome}\n";
echo "   Status: {$delivery->status}\n";
echo "   Pedido: #{$pedido->id}\n";
echo "   Entregador: {$entregador->nome} ({$entregador->tipo_veiculo})\n";
echo "\n";

echo "📍 LOCALIZAÇÃO:\n";
echo "   Restaurante: {$restauranteLat}, {$restauranteLng}\n";
echo "   Cliente (destino): {$clienteLat}, {$clienteLng}\n";
echo "   Entregador (atual): {$delivery->entregador_latitude}, {$delivery->entregador_longitude}\n";
echo "\n";

echo "🌐 COMO VISUALIZAR:\n";
echo "   1. Abra: http://myd.local/app-cliente\n";
echo "   2. Faça login com telefone: {$cliente->telefone}\n";
echo "   3. Vá em 'Pedidos' e clique no Pedido #{$pedido->id}\n";
echo "   4. Você verá o mapa com o entregador a caminho!\n";
echo "\n";

echo "🔄 PARA SIMULAR MOVIMENTO:\n";
echo "   Execute: php atualizar_localizacao_entregador.php {$delivery->id}\n";
echo "\n";

echo "=== PRONTO! ===\n";
