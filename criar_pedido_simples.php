<?php

use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\Produto;
use App\Models\ItemPedido;

echo "Criando pedidos de demonstração...\n";

// Pegar primeira mesa
$mesa = Mesa::first();
$produtos = Produto::take(3)->get();

if (!$mesa || $produtos->count() == 0) {
    echo "Erro: Dados não encontrados\n";
    return;
}

// Criar pedido
$pedido = new Pedido();
$pedido->usuario_id = 1;
$pedido->mesa_id = $mesa->id;
$pedido->status = 'aberto';
$pedido->observacoes = 'Pedido de teste';
$pedido->total = 0;
$pedido->save();

$total = 0;
foreach ($produtos as $produto) {
    $quantidade = 1;
    $subtotal = $produto->preco * $quantidade;
    
    $item = new ItemPedido();
    $item->pedido_id = $pedido->id;
    $item->produto_id = $produto->id;
    $item->quantidade = $quantidade;
    $item->preco = $produto->preco;
    $item->subtotal = $subtotal;
    $item->save();
    
    $total += $subtotal;
}

$pedido->total = $total;
$pedido->save();

echo "Pedido #{$pedido->id} criado com sucesso! Total: R$ " . number_format($total, 2, ',', '.') . "\n";
