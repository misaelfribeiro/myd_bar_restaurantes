<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICAÇÃO DO PEDIDO #115 ===\n\n";

$pedido = App\Models\Pedido::with(['delivery.entregador', 'entregador', 'itens.produto', 'cliente'])
    ->find(115);

if (!$pedido) {
    echo "❌ Pedido #115 não encontrado!\n";
    exit;
}

echo "📦 PEDIDO #115\n";
echo "   Status: {$pedido->status}\n";
echo "   Tipo: {$pedido->tipo_pedido}\n";
echo "   Total: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
echo "   Cliente ID: {$pedido->cliente_id}\n";
if ($pedido->cliente) {
    echo "   Cliente: {$pedido->cliente->nome}\n";
}
echo "   Criado em: {$pedido->created_at}\n";
echo "\n";

// Verificar delivery
if ($pedido->delivery) {
    echo "🚚 DELIVERY (ID: {$pedido->delivery->id})\n";
    echo "   Status: {$pedido->delivery->status}\n";
    echo "   Cliente: {$pedido->delivery->cliente_nome}\n";
    echo "   Endereço: {$pedido->delivery->endereco_rua}, {$pedido->delivery->endereco_numero}\n";
    echo "   Bairro: {$pedido->delivery->endereco_bairro}\n";
    echo "   Cidade: {$pedido->delivery->endereco_cidade}\n";
    echo "\n";
    
    echo "👤 ENTREGADOR:\n";
    echo "   ID: " . ($pedido->delivery->entregador_id ?? 'NULL') . "\n";
    echo "   Nome: " . ($pedido->delivery->entregador_nome ?? 'NULL') . "\n";
    echo "   Telefone: " . ($pedido->delivery->entregador_telefone ?? 'NULL') . "\n";
    
    if ($pedido->delivery->entregador) {
        echo "   Veículo: {$pedido->delivery->entregador->tipo_veiculo}\n";
        echo "   Status: {$pedido->delivery->entregador->status}\n";
    } else {
        echo "   ⚠️ Relacionamento entregador não carregado\n";
    }
    echo "\n";
    
    echo "📍 LOCALIZAÇÃO:\n";
    echo "   Destino Lat: " . ($pedido->delivery->destino_latitude ?? 'NULL') . "\n";
    echo "   Destino Lng: " . ($pedido->delivery->destino_longitude ?? 'NULL') . "\n";
    echo "   Entregador Lat: " . ($pedido->delivery->entregador_latitude ?? 'NULL') . "\n";
    echo "   Entregador Lng: " . ($pedido->delivery->entregador_longitude ?? 'NULL') . "\n";
    echo "   Última atualização: " . ($pedido->delivery->entregador_localizacao_atualizada_em ?? 'NULL') . "\n";
    echo "\n";
    
    // Verificar se tem coordenadas
    if (!$pedido->delivery->destino_latitude || !$pedido->delivery->destino_longitude) {
        echo "⚠️ ATENÇÃO: Delivery sem coordenadas de destino!\n";
        echo "   Vou configurar coordenadas de exemplo...\n\n";
        
        $pedido->delivery->update([
            'destino_latitude' => -23.561414,
            'destino_longitude' => -46.656174,
            'entregador_latitude' => -23.550520,
            'entregador_longitude' => -46.633308,
            'entregador_localizacao_atualizada_em' => now()
        ]);
        
        echo "✅ Coordenadas configuradas!\n";
        echo "   Destino: -23.561414, -46.656174\n";
        echo "   Entregador: -23.550520, -46.633308\n\n";
    }
    
} else {
    echo "⚠️ Pedido sem delivery associado\n\n";
    
    echo "Criando delivery para este pedido...\n";
    
    $cliente = $pedido->cliente ?? App\Models\Cliente::first();
    
    $delivery = App\Models\Delivery::create([
        'pedido_id' => $pedido->id,
        'cliente_id' => $cliente->id,
        'cliente_nome' => $cliente->nome,
        'cliente_telefone' => $cliente->telefone,
        'cliente_email' => $cliente->email,
        'endereco_rua' => $cliente->endereco_rua ?? 'Rua Exemplo',
        'endereco_numero' => $cliente->endereco_numero ?? '123',
        'endereco_complemento' => $cliente->endereco_complemento,
        'endereco_bairro' => $cliente->endereco_bairro ?? 'Centro',
        'endereco_cidade' => $cliente->endereco_cidade ?? 'São Paulo',
        'endereco_cep' => $cliente->endereco_cep ?? '01310-100',
        'taxa_entrega' => 8.00,
        'tempo_estimado' => 30,
        'status' => 'saiu_entrega',
        'data_saida' => now(),
        'destino_latitude' => -23.561414,
        'destino_longitude' => -46.656174,
        'entregador_latitude' => -23.550520,
        'entregador_longitude' => -46.633308,
        'entregador_localizacao_atualizada_em' => now()
    ]);
    
    // Atribuir entregador se existir
    $entregador = App\Models\Entregador::where('status', 'ativo')->first();
    if ($entregador) {
        $delivery->update([
            'entregador_id' => $entregador->id,
            'entregador_nome' => $entregador->nome,
            'entregador_telefone' => $entregador->telefone
        ]);
    }
    
    echo "✅ Delivery criado (ID: {$delivery->id})\n\n";
    
    $pedido->load('delivery.entregador');
}

echo "🌐 COMO VISUALIZAR NO APP:\n";
echo "   1. Abra: http://myd.local/app-cliente\n";
echo "   2. Faça login com o cliente\n";
echo "   3. Vá em 'Pedidos' e clique no Pedido #115\n";
echo "   4. O mapa deve aparecer com o rastreamento!\n";
echo "\n";

echo "🔄 PARA SIMULAR MOVIMENTO:\n";
if ($pedido->delivery) {
    echo "   Execute: php atualizar_localizacao_entregador.php {$pedido->delivery->id}\n";
} else {
    echo "   Primeiro crie um delivery para este pedido\n";
}
echo "\n";

echo "=== API TEST ===\n";
$response = App\Models\Pedido::with(['mesa', 'usuario', 'itens.produto', 'delivery.entregador', 'entregador'])
    ->find(115);

echo json_encode([
    'success' => true,
    'pedido' => $response
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo "\n\n";

echo "=== FIM DA VERIFICAÇÃO ===\n";
