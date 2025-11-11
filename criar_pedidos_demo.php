<?php

// Carregamento do Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

// Inicialização do kernel console
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\Produto;
use App\Models\ItemPedido;

echo "🍽️ Criando pedidos de demonstração...\n\n";

// Pegar algumas mesas e produtos
$mesas = Mesa::take(3)->get();
$produtos = Produto::take(4)->get();

if ($mesas->count() == 0 || $produtos->count() == 0) {
    echo "❌ Erro: Execute primeiro o script de dados básicos (criar_dados_garcom.php)\n";
    exit;
}

foreach ($mesas as $index => $mesa) {
    // Criar pedido
    $pedido = Pedido::create([
        'usuario_id' => 1, // Garçom demo
        'mesa_id' => $mesa->id,
        'total' => 0, // Será calculado
        'status' => 'aberto',
        'observacoes' => 'Pedido de demonstração'
    ]);
    
    $totalPedido = 0;
    
    // Adicionar 2-3 itens aleatórios ao pedido
    $itensPedido = $produtos->random(rand(2, 3));
    
    foreach ($itensPedido as $produto) {
        $quantidade = rand(1, 3);
        $subtotal = $produto->preco * $quantidade;
        
        ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => $quantidade,
            'preco' => $produto->preco,
            'subtotal' => $subtotal
        ]);
        
        $totalPedido += $subtotal;
        
        echo "  ➕ {$quantidade}x {$produto->nome} - R$ " . number_format($subtotal, 2, ',', '.') . "\n";
    }
    
    // Atualizar total do pedido
    $pedido->update(['total' => $totalPedido]);
    
    echo "✅ Pedido #{$pedido->id} criado para {$mesa->identificador} - Total: R$ " . number_format($totalPedido, 2, ',', '.') . "\n\n";
}

echo "🎉 Pedidos de demonstração criados com sucesso!\n";
echo "🔗 Acesse o Modo Garçom em: http://localhost:8000/garcom/dashboard\n";
echo "📋 Visualize as mesas em: http://localhost:8000/garcom/mesas\n";
?>
