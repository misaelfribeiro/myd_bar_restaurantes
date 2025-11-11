<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Criando pedido de teste...\n";

try {
    // Criar um pedido de teste
    $produto = App\Models\Produto::first();
    $mesa = App\Models\Mesa::first();
    $usuario = App\Models\Usuario::first();

    if ($produto && $mesa && $usuario) {
        $pedido = App\Models\Pedido::create([
            'mesa_id' => $mesa->id,
            'usuario_id' => $usuario->id,
            'status' => 'aberto',
            'total' => $produto->preco * 2
        ]);
        
        App\Models\ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'preco_unitario' => $produto->preco,
            'subtotal' => $produto->preco * 2
        ]);
          echo "✅ Pedido de teste criado com sucesso!\n";
        echo "📍 Mesa: " . $mesa->nome . "\n";
        echo "👤 Usuário: " . $usuario->nome . "\n";
        echo "🍽️ Produto: " . $produto->nome . "\n";
        echo "💰 Total: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
    } else {
        echo "❌ Erro: Produto, mesa ou usuário não encontrados\n";
        echo "Produto: " . ($produto ? "✅" : "❌") . "\n";
        echo "Mesa: " . ($mesa ? "✅" : "❌") . "\n"; 
        echo "Usuário: " . ($usuario ? "✅" : "❌") . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
