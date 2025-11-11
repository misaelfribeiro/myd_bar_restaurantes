<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 TESTE DIRETO DE CRIAÇÃO DE PEDIDOS\n";
echo "=====================================\n\n";

try {
    // 1. Verificar dados necessários
    echo "📊 VERIFICANDO DADOS DISPONÍVEIS:\n";
    $mesas = App\Models\Mesa::count();
    $produtos = App\Models\Produto::count();
    $usuarios = App\Models\Usuario::count();
    
    echo "- Mesas: $mesas\n";
    echo "- Produtos: $produtos\n"; 
    echo "- Usuários: $usuarios\n\n";
    
    if ($mesas == 0 || $produtos == 0 || $usuarios == 0) {
        echo "❌ ERRO: Dados insuficientes para teste!\n";
        exit(1);
    }
    
    // 2. Obter dados para o teste
    $mesa = App\Models\Mesa::first();
    $produto1 = App\Models\Produto::first();
    $produto2 = App\Models\Produto::skip(1)->first();
    $usuario = App\Models\Usuario::first();
    
    echo "📋 DADOS DO TESTE:\n";
    echo "- Mesa: {$mesa->identificador} (ID: {$mesa->id})\n";
    echo "- Produto 1: {$produto1->nome} - R$ {$produto1->preco}\n";
    echo "- Produto 2: {$produto2->nome} - R$ {$produto2->preco}\n";
    echo "- Usuário: {$usuario->nome}\n\n";
    
    // 3. Simular dados do pedido
    $itens = [
        [
            'produto_id' => $produto1->id,
            'quantidade' => 2
        ],
        [
            'produto_id' => $produto2->id,
            'quantidade' => 1
        ]
    ];
    
    echo "🍽️ CRIANDO PEDIDO:\n";
    echo "- 2x {$produto1->nome}\n";
    echo "- 1x {$produto2->nome}\n\n";
    
    // 4. Calcular total
    $total = ($produto1->preco * 2) + ($produto2->preco * 1);
    echo "💰 Total calculado: R$ " . number_format($total, 2, ',', '.') . "\n\n";
    
    // 5. Criar pedido diretamente
    DB::beginTransaction();
    
    $pedido = App\Models\Pedido::create([
        'usuario_id' => $usuario->id,
        'mesa_id' => $mesa->id,
        'total' => $total,
        'status' => 'aberto',
        'observacoes' => 'Teste direto via script'
    ]);
    
    echo "✅ Pedido criado com ID: {$pedido->id}\n";
    
    // 6. Criar itens do pedido
    foreach ($itens as $item) {
        $produto = App\Models\Produto::find($item['produto_id']);
        $subtotal = $produto->preco * $item['quantidade'];
        
        $itemPedido = App\Models\ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $item['produto_id'],
            'quantidade' => $item['quantidade'],
            'preco_unitario' => $produto->preco,
            'subtotal' => $subtotal
        ]);
        
        echo "✅ Item criado: {$item['quantidade']}x {$produto->nome}\n";
    }
    
    DB::commit();
    
    echo "\n🎉 TESTE CONCLUÍDO COM SUCESSO!\n";
    echo "Pedido #{$pedido->id} criado com {$pedido->itens()->count()} itens\n";
    
    // 7. Verificar resultado
    $pedidoCriado = App\Models\Pedido::with('itens.produto')->find($pedido->id);
    echo "\n📋 VERIFICAÇÃO FINAL:\n";
    echo "- Status: {$pedidoCriado->status}\n";
    echo "- Total: R$ " . number_format($pedidoCriado->total, 2, ',', '.') . "\n";
    echo "- Itens: {$pedidoCriado->itens->count()}\n";
    
    foreach ($pedidoCriado->itens as $item) {
        echo "  * {$item->quantidade}x {$item->produto->nome} = R$ " . number_format($item->subtotal, 2, ',', '.') . "\n";
    }
    
} catch (\Exception $e) {
    DB::rollback();
    echo "❌ ERRO NO TESTE:\n";
    echo "Tipo: " . get_class($e) . "\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . " (linha " . $e->getLine() . ")\n\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "Teste finalizado: " . date('Y-m-d H:i:s') . "\n";
