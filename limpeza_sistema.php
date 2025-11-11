<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧹 LIMPEZA E CORREÇÃO DO SISTEMA\n";
echo "=================================\n\n";

try {
    echo "1. 📊 ESTADO ATUAL:\n";
    
    $totalPedidos = App\Models\Pedido::count();
    $pedidosAbertos = App\Models\Pedido::where('status', 'aberto')->count();
    $mesasOcupadas = App\Models\Mesa::whereHas('pedidos', function($q) {
        $q->where('status', 'aberto');
    })->count();
    
    echo "- Total de pedidos: $totalPedidos\n";
    echo "- Pedidos abertos: $pedidosAbertos\n";
    echo "- Mesas ocupadas: $mesasOcupadas\n\n";
    
    // Verificar mesas com múltiplos pedidos
    echo "2. 🔍 VERIFICANDO MESAS COM MÚLTIPLOS PEDIDOS:\n";
    $mesasComMultiplos = App\Models\Mesa::withCount(['pedidos' => function($q) {
        $q->where('status', 'aberto');
    }])->having('pedidos_count', '>', 1)->get();
    
    if ($mesasComMultiplos->count() > 0) {
        echo "❌ PROBLEMA ENCONTRADO - Mesas com múltiplos pedidos:\n";
        foreach ($mesasComMultiplos as $mesa) {
            echo "   - {$mesa->identificador}: {$mesa->pedidos_count} pedidos abertos\n";
            
            // Finalizar pedidos extras, mantendo apenas o mais recente
            $pedidos = App\Models\Pedido::where('mesa_id', $mesa->id)
                                       ->where('status', 'aberto')
                                       ->orderBy('created_at', 'desc')
                                       ->get();
            
            $primeiro = true;
            foreach ($pedidos as $pedido) {
                if (!$primeiro) {
                    $pedido->update(['status' => 'finalizado']);
                    echo "     ✅ Pedido #{$pedido->id} finalizado automaticamente\n";
                }
                $primeiro = false;
            }
        }
    } else {
        echo "✅ Nenhuma mesa com múltiplos pedidos encontrada\n";
    }
    
    echo "\n3. 🧪 TESTANDO CRIAÇÃO DE PEDIDO:\n";
    
    // Encontrar mesa livre
    $mesaLivre = App\Models\Mesa::whereDoesntHave('pedidos', function($q) {
        $q->where('status', 'aberto');
    })->first();
    
    if (!$mesaLivre) {
        echo "❌ Nenhuma mesa livre encontrada\n";
        echo "   Finalizando um pedido para liberar mesa...\n";
        
        $pedidoParaFinalizar = App\Models\Pedido::where('status', 'aberto')->first();
        if ($pedidoParaFinalizar) {
            $pedidoParaFinalizar->update(['status' => 'finalizado']);
            $mesaLivre = App\Models\Mesa::find($pedidoParaFinalizar->mesa_id);
            echo "   ✅ Mesa {$mesaLivre->identificador} liberada\n";
        }
    }
    
    if ($mesaLivre) {
        $produto = App\Models\Produto::where('ativo', true)->first();
        $usuario = App\Models\Usuario::first();
        
        echo "   Criando pedido de teste:\n";
        echo "   - Mesa: {$mesaLivre->identificador}\n";
        echo "   - Produto: {$produto->nome}\n";
        echo "   - Usuário: {$usuario->nome}\n";
        
        DB::beginTransaction();
        
        $pedido = App\Models\Pedido::create([
            'usuario_id' => $usuario->id,
            'mesa_id' => $mesaLivre->id,
            'total' => $produto->preco,
            'status' => 'aberto',
            'observacoes' => 'Teste após correções - ' . date('H:i:s')
        ]);
        
        App\Models\ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 1,
            'preco_unitario' => $produto->preco,
            'subtotal' => $produto->preco
        ]);
        
        DB::commit();
        
        echo "   ✅ Pedido #{$pedido->id} criado com sucesso!\n";
    }
    
    echo "\n4. 📊 ESTADO FINAL:\n";
    
    $totalPedidosFinal = App\Models\Pedido::count();
    $pedidosAbertosFinal = App\Models\Pedido::where('status', 'aberto')->count();
    $mesasOcupadasFinal = App\Models\Mesa::whereHas('pedidos', function($q) {
        $q->where('status', 'aberto');
    })->count();
    
    echo "- Total de pedidos: $totalPedidosFinal\n";
    echo "- Pedidos abertos: $pedidosAbertosFinal\n";
    echo "- Mesas ocupadas: $mesasOcupadasFinal\n";
    
    echo "\n✅ LIMPEZA CONCLUÍDA COM SUCESSO!\n";
    echo "🔗 Teste a interface: http://localhost:8000/garcom/pedido-rapido\n";
    
} catch (\Exception $e) {
    DB::rollback();
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . " (linha " . $e->getLine() . ")\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Script finalizado: " . date('H:i:s') . "\n";
