<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pedido;
use App\Models\Usuario;
use App\Models\Mesa;
use App\Models\Produto;
use App\Models\ItemPedido;

echo "=== CRIANDO PEDIDO DE TESTE PARA ADMIN ===\n";

// Buscar dados necessários
$admin = Usuario::where('role', 'admin')->first();
$mesa = Mesa::first();
$produtos = Produto::limit(3)->get();

if (!$admin || !$mesa || $produtos->count() < 3) {
    echo "❌ Dados insuficientes para criar pedido de teste\n";
    exit;
}

try {
    // Criar pedido
    $pedido = Pedido::create([
        'mesa_id' => $mesa->id,
        'usuario_id' => $admin->id,
        'status' => 'em_preparo', // Status que permite exclusão para admin
        'total' => 0 // Será calculado
    ]);

    echo "✅ Pedido criado: ID {$pedido->id}\n";

    $total = 0;
    
    // Adicionar itens
    foreach ($produtos as $index => $produto) {
        $quantidade = $index + 1; // 1, 2, 3
        $subtotal = $produto->preco * $quantidade;
        
        ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => $quantidade,
            'preco_unitario' => $produto->preco,
            'subtotal' => $subtotal,
            'observacoes' => "Observação para {$produto->nome}"
        ]);
        
        $total += $subtotal;
        
        echo "- Item adicionado: {$produto->nome} (Qtd: {$quantidade}, R$ {$produto->preco})\n";
    }

    // Atualizar total do pedido
    $pedido->update(['total' => $total]);
    
    echo "✅ Total do pedido: R$ " . number_format($total, 2, ',', '.') . "\n";
    echo "✅ Pedido de teste criado com sucesso!\n";
    echo "\n=== INFORMAÇÕES PARA TESTE ===\n";
    echo "ID do Pedido: {$pedido->id}\n";
    echo "Status: {$pedido->status}\n";
    echo "Mesa: {$mesa->identificador}\n";
    echo "Usuário: {$admin->nome} ({$admin->role})\n";
    echo "URL de teste: http://127.0.0.1:8000/pedidos/{$pedido->id}/edit\n";

} catch (Exception $e) {
    echo "❌ Erro ao criar pedido: {$e->getMessage()}\n";
}