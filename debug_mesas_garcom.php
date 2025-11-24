<?php
// Debug: Verificar estado das mesas e pedidos
require_once 'vendor/autoload.php';

// Configurar ambiente Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    echo "<h1>🔍 Debug: Estado das Mesas e Pedidos</h1>";
    
    // Buscar mesas com pedidos
    $mesas = \App\Models\Mesa::with(['pedidos' => function($query) {
        $query->where('status', 'finalizado')->orWhere('status', 'aberto');
    }])->get();
    
    echo "<h2>📋 Mesas disponíveis:</h2>";
    foreach ($mesas as $mesa) {
        echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px; border-radius: 5px;'>";
        echo "<h3>Mesa {$mesa->numero}</h3>";
        echo "<p><strong>Status:</strong> {$mesa->status}</p>";
        echo "<p><strong>Pedidos:</strong> {$mesa->pedidos->count()}</p>";
        
        if ($mesa->pedidos->count() > 0) {
            echo "<ul>";
            foreach ($mesa->pedidos as $pedido) {
                echo "<li>Pedido #{$pedido->id} - Status: {$pedido->status} - Total: R$ {$pedido->total}</li>";
            }
            echo "</ul>";
            
            echo "<p>🧪 <a href='/myd_bar_restaurantes/public/api/pagamentos-teste/info/mesa/{$mesa->id}' target='_blank'>Testar API desta mesa</a></p>";
        }
        echo "</div>";
    }
    
    // Criar um pedido de teste se necessário
    $mesaComPedido = $mesas->where('pedidos.count', '>', 0)->first();
    if (!$mesaComPedido) {
        echo "<h2>⚠️ Criando pedido de teste...</h2>";
        
        $mesa = \App\Models\Mesa::first();
        if ($mesa) {
            $pedido = \App\Models\Pedido::create([
                'mesa_id' => $mesa->id,
                'usuario_id' => 1,
                'total' => 25.50,
                'status' => 'finalizado',
                'observacoes' => 'Pedido teste para debug garçom'
            ]);
            
            echo "<p>✅ Pedido #{$pedido->id} criado na Mesa {$mesa->numero}</p>";
            echo "<p>🧪 <a href='/myd_bar_restaurantes/public/api/pagamentos-teste/info/mesa/{$mesa->id}' target='_blank'>Testar API da mesa com pedido</a></p>";
        }
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 10px; color: #721c24;'>";
    echo "<strong>Erro:</strong> " . $e->getMessage();
    echo "</div>";
}
?>
