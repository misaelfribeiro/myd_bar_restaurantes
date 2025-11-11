<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Criando múltiplos pedidos de teste...\n";

try {
    $produtos = App\Models\Produto::limit(4)->get();
    $mesas = App\Models\Mesa::limit(5)->get();
    $usuario = App\Models\Usuario::first();

    if ($produtos->count() > 0 && $mesas->count() > 0 && $usuario) {
        foreach ($mesas as $index => $mesa) {
            $produto1 = $produtos->get($index % $produtos->count());
            $produto2 = $produtos->get(($index + 1) % $produtos->count());
            
            $total = ($produto1->preco * 2) + ($produto2->preco * 1);
            
            $pedido = App\Models\Pedido::create([
                'mesa_id' => $mesa->id,
                'usuario_id' => $usuario->id,
                'status' => $index < 3 ? 'aberto' : 'finalizado',
                'total' => $total
            ]);
            
            // Item 1
            App\Models\ItemPedido::create([
                'pedido_id' => $pedido->id,
                'produto_id' => $produto1->id,
                'quantidade' => 2,
                'preco_unitario' => $produto1->preco,
                'subtotal' => $produto1->preco * 2
            ]);
            
            // Item 2
            App\Models\ItemPedido::create([
                'pedido_id' => $pedido->id,
                'produto_id' => $produto2->id,
                'quantidade' => 1,
                'preco_unitario' => $produto2->preco,
                'subtotal' => $produto2->preco * 1
            ]);
            
            echo "✅ Pedido #{$pedido->id} - Mesa: {$mesa->nome} - Status: {$pedido->status} - R$ " . number_format($total, 2, ',', '.') . "\n";
        }
        
        echo "\n🎉 " . $mesas->count() . " pedidos de teste criados com sucesso!\n";
    } else {
        echo "❌ Erro: Dados insuficientes\n";
        echo "Produtos: " . $produtos->count() . "\n";
        echo "Mesas: " . $mesas->count() . "\n";
        echo "Usuário: " . ($usuario ? "✅" : "❌") . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
