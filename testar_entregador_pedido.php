<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE: ENTREGADOR NO ACOMPANHAMENTO DO PEDIDO ===\n\n";

$pedido = App\Models\Pedido::with(['entregador', 'delivery.entregador'])->find(112);

if (!$pedido) {
    echo "❌ Pedido #112 não encontrado!\n";
    exit;
}

echo "📦 PEDIDO #112\n";
echo "   Status: {$pedido->status}\n";
echo "   Entregador ID: " . ($pedido->entregador_id ?? 'NULL') . "\n";
echo "\n";

// Verificar entregador do pedido
if ($pedido->entregador) {
    echo "👤 ENTREGADOR (via pedido):\n";
    echo "   ID: {$pedido->entregador->id}\n";
    echo "   Nome: {$pedido->entregador->nome}\n";
    echo "   Tipo Veículo: " . ($pedido->entregador->tipo_veiculo ?? 'N/A') . "\n";
    echo "\n";
} else {
    echo "⚠️ Pedido sem entregador atribuído diretamente\n\n";
}

// Verificar delivery
if ($pedido->delivery) {
    echo "🚚 DELIVERY:\n";
    echo "   ID: {$pedido->delivery->id}\n";
    echo "   Status: {$pedido->delivery->status}\n";
    echo "   Entregador ID: " . ($pedido->delivery->entregador_id ?? 'NULL') . "\n";
    echo "\n";
    
    if ($pedido->delivery->entregador) {
        echo "👤 ENTREGADOR (via delivery):\n";
        echo "   ID: {$pedido->delivery->entregador->id}\n";
        echo "   Nome: {$pedido->delivery->entregador->nome}\n";
        echo "   Tipo Veículo: " . ($pedido->delivery->entregador->tipo_veiculo ?? 'N/A') . "\n";
        echo "\n";
    } else {
        echo "⚠️ Delivery sem entregador atribuído\n\n";
        
        // Verificar se existem entregadores disponíveis
        $entregadores = App\Models\Entregador::where('status', 'ativo')
            ->where('disponivel', 1)
            ->get();
        
        if ($entregadores->count() > 0) {
            echo "📋 Entregadores disponíveis: {$entregadores->count()}\n";
            echo "   Atribuindo primeiro entregador...\n";
            
            $entregador = $entregadores->first();
            $pedido->delivery->entregador_id = $entregador->id;
            $pedido->delivery->entregador_nome = $entregador->nome;
            $pedido->delivery->entregador_telefone = $entregador->telefone;
            $pedido->delivery->save();
            
            echo "   ✅ Entregador {$entregador->nome} atribuído ao delivery!\n\n";
            
            // Recarregar
            $pedido->load(['delivery.entregador']);
        } else {
            echo "❌ Nenhum entregador disponível no sistema\n";
            echo "   Criando entregador de teste...\n";
            
            $entregador = App\Models\Entregador::create([
                'nome' => 'João Silva',
                'telefone' => '(11) 98765-4321',
                'tipo_veiculo' => 'moto',
                'placa_veiculo' => 'ABC-1234',
                'status' => 'ativo',
                'disponivel' => 1
            ]);
            
            echo "   ✅ Entregador criado: {$entregador->nome}\n";
            
            $pedido->delivery->entregador_id = $entregador->id;
            $pedido->delivery->entregador_nome = $entregador->nome;
            $pedido->delivery->entregador_telefone = $entregador->telefone;
            $pedido->delivery->save();
            
            echo "   ✅ Entregador atribuído ao delivery!\n\n";
            
            // Recarregar
            $pedido->load(['delivery.entregador']);
        }
    }
} else {
    echo "⚠️ Pedido sem delivery associado\n\n";
}

echo "=== TESTE DE RESPOSTA DA API ===\n";
$pedidoApi = App\Models\Pedido::with(['mesa', 'usuario', 'itens.produto', 'delivery.entregador', 'entregador'])->find(112);
$response = [
    'success' => true,
    'pedido' => $pedidoApi
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo "\n\n";

echo "=== FIM DO TESTE ===\n";

