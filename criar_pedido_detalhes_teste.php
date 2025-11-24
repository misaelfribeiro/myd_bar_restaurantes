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

echo "=== CRIANDO PEDIDO PARA TESTE DE DETALHES ===\n";

$admin = Usuario::where('role', 'admin')->first();
$mesa = Mesa::first();
$produtos = Produto::limit(4)->get();

if (!$admin || !$mesa || $produtos->count() < 4) {
    echo "❌ Dados insuficientes\n";
    exit;
}

// Criar pedido com status que permite exclusão
$pedido = Pedido::create([
    'mesa_id' => $mesa->id,
    'usuario_id' => $admin->id,
    'status' => 'em_preparo', // Status que permite exclusão
    'total' => 0
]);

$total = 0;
foreach ($produtos as $index => $produto) {
    $quantidade = $index + 1; // 1, 2, 3, 4
    $subtotal = $produto->preco * $quantidade;
    
    ItemPedido::create([
        'pedido_id' => $pedido->id,
        'produto_id' => $produto->id,
        'quantidade' => $quantidade,
        'preco_unitario' => $produto->preco,
        'subtotal' => $subtotal,
        'observacoes' => $index % 2 === 0 ? "Observação para {$produto->nome}" : null
    ]);
    
    $total += $subtotal;
    echo "- Adicionado: {$produto->nome} (Qtd: {$quantidade}, R$ " . number_format($produto->preco, 2, ',', '.') . ")\n";
}

$pedido->update(['total' => $total]);

echo "\n✅ PEDIDO CRIADO COM SUCESSO!\n";
echo "ID: {$pedido->id}\n";
echo "Status: {$pedido->status}\n";
echo "Mesa: {$mesa->identificador}\n";
echo "Total de itens: {$produtos->count()}\n";
echo "Valor total: R$ " . number_format($total, 2, ',', '.') . "\n";

echo "\n=== LINKS DE TESTE ===\n";
echo "📍 Detalhes: http://127.0.0.1:8000/pedidos/{$pedido->id}/detalhes\n";
echo "📍 Edição: http://127.0.0.1:8000/pedidos/{$pedido->id}/edit\n";

echo "\n=== FUNCIONALIDADES A TESTAR ===\n";
echo "✅ Botões de exclusão aparecem para admin/gerente\n";
echo "✅ Exclusão via AJAX funciona\n";
echo "✅ Total é recalculado automaticamente\n";
echo "✅ Interface é atualizada em tempo real\n";
echo "✅ Proteção contra exclusão do último item\n";
echo "✅ Mensagens de feedback (toast)\n";

echo "\n🎯 PRONTO PARA TESTE!\n";