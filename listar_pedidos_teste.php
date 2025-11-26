<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Pedidos Disponíveis para Teste ===\n\n";

$pedidos = \App\Models\Pedido::select('id', 'numero_pedido', 'total', 'status', 'tenant_code')
    ->orderBy('id', 'desc')
    ->limit(10)
    ->get();

if ($pedidos->count() > 0) {
    foreach ($pedidos as $pedido) {
        echo "Número: {$pedido->numero_pedido} | Total: R$ " . number_format($pedido->total, 2, ',', '.') . " | Status: {$pedido->status}\n";
    }
} else {
    echo "Nenhum pedido encontrado. Vou criar um pedido de teste...\n\n";
    
    // Criar pedido de teste
    $pedido = \App\Models\Pedido::create([
        'numero_pedido' => 'PED-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
        'tenant_code' => 'RESTAURANTE0003',
        'total' => 65.00,
        'status' => 'aberto',
        'tipo' => 'delivery',
        'nome_cliente' => 'Cliente Teste Mercado Pago',
        'telefone_cliente' => '98988889999',
        'endereco_entrega' => 'Rua Teste, 123',
        'metodo_pagamento' => 'pix',
        'payment_status' => 'pending'
    ]);
    
    echo "✅ Pedido de teste criado!\n";
    echo "Número: {$pedido->numero_pedido} | Total: R$ 65,00 | Status: {$pedido->status}\n";
}

echo "\n=== Use este NÚMERO no formulário de teste ===\n";
echo "Acesse: http://localhost:8000/teste-mercadopago.html\n";
