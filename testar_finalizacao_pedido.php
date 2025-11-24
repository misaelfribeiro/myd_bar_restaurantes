<?php

// Incluir o autoload do Laravel
require_once __DIR__ . '/vendor/autoload.php';

// Configurar o ambiente Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    Illuminate\Http\Request::capture()
);

use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\Usuario;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\ItemPedido;

echo "🧪 TESTE - Finalização de Pedido via API\n";
echo "=====================================\n\n";

try {
    // 1. Verificar se existe um pedido aberto para teste
    $pedido = Pedido::where('status', 'aberto')->first();
    
    if (!$pedido) {
        echo "⚠️  Não há pedidos abertos. Criando um pedido de teste...\n";
        
        // Buscar uma mesa e usuário
        $mesa = Mesa::first();
        $usuario = Usuario::first();
        
        if (!$mesa || !$usuario) {
            echo "❌ Erro: Mesa ou usuário não encontrado\n";
            exit;
        }
        
        // Criar pedido de teste
        $pedido = Pedido::create([
            'mesa_id' => $mesa->id,
            'usuario_id' => $usuario->id,
            'status' => 'aberto',
            'total' => 25.50,
            'observacoes' => 'Pedido de teste para finalização'
        ]);
        
        echo "✅ Pedido de teste criado: #{$pedido->id}\n";
    }
    
    echo "📋 Pedido encontrado: #{$pedido->id}\n";
    echo "   Mesa: {$pedido->mesa->identificador}\n";
    echo "   Status atual: {$pedido->status}\n";
    echo "   Total: R$ " . number_format($pedido->total, 2, ',', '.') . "\n\n";
    
    // 2. Testar a finalização via API
    echo "🔄 Testando finalização via API PATCH...\n";
    
    // Simular requisição PATCH para /api/pedidos/{id}
    $url = "http://localhost/myd_bar_restaurantes/public/api/pedidos/{$pedido->id}";
    
    $data = [
        'status' => 'finalizado'
    ];
    
    $options = [
        'http' => [
            'method' => 'PATCH',
            'header' => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            'content' => json_encode($data),
            'ignore_errors' => true
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $httpCode = null;
    
    // Extrair código HTTP da resposta
    if (isset($http_response_header)) {
        $httpCode = $http_response_header[0];
    }
    
    echo "📡 Resposta da API:\n";
    echo "   Status HTTP: " . ($httpCode ?: 'Desconhecido') . "\n";
    echo "   Resposta: " . ($response ?: 'Vazia') . "\n\n";
    
    // 3. Verificar se o pedido foi atualizado no banco
    $pedido->refresh();
    echo "✅ Verificação no banco de dados:\n";
    echo "   Status após API: {$pedido->status}\n";
    
    if ($pedido->status === 'finalizado') {
        echo "🎉 SUCESSO! Pedido finalizado corretamente!\n\n";
        
        // 4. Testar via método direto também
        echo "🔄 Teste adicional: finalizando via model...\n";
        
        // Criar outro pedido de teste
        $pedido2 = Pedido::create([
            'mesa_id' => $pedido->mesa_id,
            'usuario_id' => $pedido->usuario_id,
            'status' => 'aberto',
            'total' => 15.75,
            'observacoes' => 'Segundo pedido de teste'
        ]);
        
        // Finalizar direto pelo model
        $pedido2->update(['status' => 'finalizado']);
        
        echo "✅ Pedido #{$pedido2->id} finalizado via model\n";
        echo "   Status: {$pedido2->status}\n\n";
        
    } else {
        echo "❌ ERRO! Pedido não foi finalizado. Status atual: {$pedido->status}\n\n";
    }
    
    echo "📊 RESUMO FINAL:\n";
    echo "================\n";
    $pedidosAbertos = Pedido::where('status', 'aberto')->count();
    $pedidosFinalizados = Pedido::where('status', 'finalizado')->count();
    
    echo "- Pedidos abertos: {$pedidosAbertos}\n";
    echo "- Pedidos finalizados: {$pedidosFinalizados}\n";
    
} catch (Exception $e) {
    echo "❌ ERRO durante o teste: " . $e->getMessage() . "\n";
    echo "   Arquivo: " . $e->getFile() . "\n";
    echo "   Linha: " . $e->getLine() . "\n";
}

echo "\n🏁 Teste finalizado: " . date('Y-m-d H:i:s') . "\n";
