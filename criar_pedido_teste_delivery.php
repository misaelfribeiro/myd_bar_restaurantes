<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

echo "=== CRIAR PEDIDO DELIVERY TESTE ===\n";

try {
    DB::beginTransaction();
    
    // Criar pedido sem mesa (delivery)
    $pedido = \App\Models\Pedido::create([
        'mesa_id' => null,
        'usuario_id' => 1,
        'status' => 'aberto',
        'total' => 25.50,
        'observacoes' => 'Pedido de teste para delivery'
    ]);
    
    echo "Pedido criado: ID {$pedido->id}\n";
    
    // Adicionar alguns itens
    \App\Models\ItemPedido::create([
        'pedido_id' => $pedido->id,
        'produto_id' => 1,
        'quantidade' => 2,
        'preco_unitario' => 10.00,
        'subtotal' => 20.00,
        'observacoes' => 'Sem cebola'
    ]);
    
    \App\Models\ItemPedido::create([
        'pedido_id' => $pedido->id,
        'produto_id' => 2,
        'quantidade' => 1,
        'preco_unitario' => 5.50,
        'subtotal' => 5.50,
        'observacoes' => null
    ]);
    
    echo "Itens adicionados!\n";
    
    // Criar delivery
    $delivery = \App\Models\Delivery::create([
        'pedido_id' => $pedido->id,
        'cliente_nome' => 'João Silva',
        'cliente_telefone' => '11999999999',
        'endereco_rua' => 'Rua das Flores',
        'endereco_numero' => '123',
        'endereco_complemento' => 'Apto 45',
        'endereco_bairro' => 'Centro',
        'endereco_cidade' => 'São Paulo',
        'endereco_cep' => '01234567',
        'taxa_entrega' => 8.00,
        'tempo_estimado' => 35,
        'distancia_km' => 2.8,
        'observacoes' => 'Próximo ao banco Itaú',
        'status' => 'pendente'
    ]);
    
    echo "Delivery criado: ID {$delivery->id}\n";
    
    DB::commit();
    
    echo "\nPedido de delivery criado com sucesso!\n";
    echo "URL: http://127.0.0.1:8000/pedidos/{$pedido->id}\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "ERRO: " . $e->getMessage() . "\n";
}