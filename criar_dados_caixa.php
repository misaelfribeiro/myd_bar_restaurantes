<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Inicializa o aplicativo
$kernel->bootstrap();

use App\Models\Usuario;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Produto;

try {
    echo "🚀 Criando dados de teste para o sistema de caixa...\n\n";
    
    // Busca dados existentes
    $usuario = Usuario::first();
    $mesa = Mesa::first();
    
    if (!$usuario || !$mesa) {
        echo "❌ Erro: Não existem usuários ou mesas cadastrados\n";
        exit;
    }
    
    echo "👤 Usuário: {$usuario->nome}\n";
    echo "🪑 Mesa: {$mesa->numero}\n\n";
    
    // Cria pedidos de teste
    $pedidos = [];
    
    // Pedido 1 - Finalizado para pagamento
    $pedido1 = Pedido::create([
        'mesa_id' => $mesa->id,
        'usuario_id' => $usuario->id,
        'total' => 85.50,
        'status' => 'finalizado',
        'observacoes' => 'Pedido para teste do sistema de caixa'
    ]);
    $pedidos[] = $pedido1;
    echo "✅ Pedido 1 criado - ID: {$pedido1->id} - Total: R$ {$pedido1->total}\n";
    
    // Pedido 2 - Finalizado para pagamento
    $pedido2 = Pedido::create([
        'mesa_id' => $mesa->id,
        'usuario_id' => $usuario->id,
        'total' => 42.00,
        'status' => 'finalizado',
        'observacoes' => 'Segundo pedido para teste'
    ]);
    $pedidos[] = $pedido2;
    echo "✅ Pedido 2 criado - ID: {$pedido2->id} - Total: R$ {$pedido2->total}\n";
    
    // Pedido 3 - Finalizado para pagamento
    $pedido3 = Pedido::create([
        'mesa_id' => $mesa->id,
        'usuario_id' => $usuario->id,
        'total' => 127.80,
        'status' => 'finalizado',
        'observacoes' => 'Terceiro pedido para teste'
    ]);
    $pedidos[] = $pedido3;
    echo "✅ Pedido 3 criado - ID: {$pedido3->id} - Total: R$ {$pedido3->total}\n";
    
    echo "\n💰 Total de pedidos pendentes de pagamento: R$ " . 
         number_format(($pedido1->total + $pedido2->total + $pedido3->total), 2, ',', '.') . "\n\n";
    
    echo "🎯 Dados criados com sucesso!\n";
    echo "🌐 Acesse: http://127.0.0.1:8000/caixa\n";
    echo "📋 Para testar o sistema de caixa\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
