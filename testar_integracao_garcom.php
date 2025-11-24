<?php
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap da aplicação Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$request = Request::capture();
$app->instance('request', $request);

echo "<h1>🧪 TESTE DE INTEGRAÇÃO - MODO GARÇOM com API UNIFICADA</h1>";
echo "<p>Data/Hora: " . date('Y-m-d H:i:s') . "</p>";

try {
    // 1. Verificar se há caixa aberto
    $caixa = \App\Models\Caixa::where('status', 'aberto')->first();
    if (!$caixa) {
        echo "<h2>📦 Criando Caixa de Teste</h2>";
        $caixa = \App\Models\Caixa::create([
            'usuario_id' => 1,
            'data_abertura' => now(),
            'valor_inicial' => 100.00,
            'status' => 'aberto',
            'total_vendas' => 0,
            'total_dinheiro' => 0,
            'total_cartao' => 0,
            'total_cartao_credito' => 0,
            'total_cartao_debito' => 0,
            'total_pix' => 0,
            'total_vale' => 0
        ]);
        echo "<p>✅ Caixa criado com ID: {$caixa->id}</p>";
    } else {
        echo "<h2>📦 Caixa já aberto</h2>";
        echo "<p>✅ Caixa ID: {$caixa->id} - Status: {$caixa->status}</p>";
    }

    // 2. Criar uma mesa de teste
    echo "<h2>🪑 Criando Mesa de Teste</h2>";
    $mesa = \App\Models\Mesa::firstOrCreate(
        ['numero' => 99],
        [
            'numero' => 99,
            'capacidade' => 4,
            'status' => 'ocupada',
            'garcom_id' => 1
        ]
    );
    echo "<p>✅ Mesa {$mesa->numero} - Status: {$mesa->status}</p>";

    // 3. Verificar se há produtos
    $produto = \App\Models\Produto::first();
    if (!$produto) {
        echo "<h2>🍔 Criando Produto de Teste</h2>";
        $categoria = \App\Models\Categoria::firstOrCreate(
            ['nome' => 'Teste'],
            ['nome' => 'Teste', 'descricao' => 'Categoria de teste']
        );
        
        $produto = \App\Models\Produto::create([
            'nome' => 'Hambúrguer Teste',
            'descricao' => 'Produto para teste',
            'preco' => 25.00,
            'categoria_id' => $categoria->id,
            'disponivel' => true,
            'codigo' => 'TEST001'
        ]);
        echo "<p>✅ Produto criado: {$produto->nome} - R$ {$produto->preco}</p>";
    } else {
        echo "<h2>🍔 Produto disponível</h2>";
        echo "<p>✅ Produto: {$produto->nome} - R$ {$produto->preco}</p>";
    }

    // 4. Criar pedidos de teste na mesa
    echo "<h2>📋 Criando Pedidos de Teste</h2>";
    
    // Limpar pedidos antigos da mesa de teste
    \App\Models\Pedido::where('mesa_id', $mesa->id)
        ->where('status', '!=', 'pago')
        ->delete();

    $pedido1 = \App\Models\Pedido::create([
        'mesa_id' => $mesa->id,
        'usuario_id' => 1,
        'status' => 'finalizado',
        'observacoes' => 'Pedido teste para integração garçom-API',
        'total' => 50.00
    ]);

    \App\Models\ItemPedido::create([
        'pedido_id' => $pedido1->id,
        'produto_id' => $produto->id,
        'quantidade' => 2,
        'preco_unitario' => 25.00,
        'subtotal' => 50.00,
        'observacoes' => 'Teste integração'
    ]);

    $pedido2 = \App\Models\Pedido::create([
        'mesa_id' => $mesa->id,
        'usuario_id' => 1,
        'status' => 'finalizado',
        'observacoes' => 'Segundo pedido para teste',
        'total' => 30.00
    ]);

    \App\Models\ItemPedido::create([
        'pedido_id' => $pedido2->id,
        'produto_id' => $produto->id,
        'quantidade' => 1,
        'preco_unitario' => 30.00,
        'subtotal' => 30.00,
        'observacoes' => 'Teste 2'
    ]);

    echo "<p>✅ Pedido 1: ID {$pedido1->id} - R$ 50,00 - Status: {$pedido1->status}</p>";
    echo "<p>✅ Pedido 2: ID {$pedido2->id} - R$ 30,00 - Status: {$pedido2->status}</p>";
    echo "<p><strong>Total da Mesa: R$ 80,00</strong></p>";

    // 5. Testar API de informações da mesa
    echo "<h2>🔍 Testando API de Informações</h2>";
    $url = "http://localhost/myd_bar_restaurantes/public/api/pagamentos-teste/info/mesa/{$mesa->id}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<p>📡 GET {$url}</p>";
    echo "<p><strong>Status HTTP:</strong> {$httpCode}</p>";
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data && $data['success']) {
            echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; color: #155724;'>
                    ✅ <strong>API FUNCIONANDO!</strong><br>
                    Mesa: {$data['data']['mesa']['numero']}<br>
                    Total: R$ " . number_format($data['data']['mesa']['total_geral'], 2, ',', '.') . "<br>
                    Pedidos: " . count($data['data']['pedidos']) . "
                  </div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; color: #721c24;'>
                    ❌ API retornou erro: " . ($data['message'] ?? 'Desconhecido') . "
                  </div>";
        }
    } else {
        echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; color: #856404;'>
                ⚠️ API retornou status {$httpCode}
              </div>";
    }

    // 6. Instruções para teste manual
    echo "<h2>📱 COMO TESTAR A INTEGRAÇÃO</h2>";
    echo "<div style='background: #d1ecf1; padding: 15px; border: 1px solid #bee5eb;'>";
    echo "<ol>";
    echo "<li>🔗 <strong><a href='/myd_bar_restaurantes/public/garcom/mesas' target='_blank'>Acesse o Modo Garçom</a></strong></li>";
    echo "<li>📋 Procure a <strong>Mesa {$mesa->numero}</strong> na lista</li>";
    echo "<li>💰 Clique em <strong>\"Finalizar Mesa\"</strong></li>";
    echo "<li>✅ Verifique se o modal abre com as informações corretas:</li>";
    echo "<ul>";
    echo "<li>Mesa: {$mesa->numero}</li>";
    echo "<li>Pedidos: 2</li>";
    echo "<li>Total: R$ 80,00</li>";
    echo "</ul>";
    echo "<li>💳 Teste um pagamento (ex: R$ 80,00 em dinheiro)</li>";
    echo "<li>🎉 Verifique se o pagamento é processado com sucesso</li>";
    echo "</ol>";
    echo "</div>";

    echo "<h2>🔧 LINKS ÚTEIS</h2>";
    echo "<p>🔗 <a href='/myd_bar_restaurantes/public/garcom/mesas' target='_blank'>Modo Garçom</a></p>";
    echo "<p>🧪 <a href='/myd_bar_restaurantes/teste_api_corrigido.php' target='_blank'>Teste Direto da API</a></p>";
    echo "<p>📊 <a href='/myd_bar_restaurantes/teste_api_pagamentos.html' target='_blank'>Interface Completa de Testes</a></p>";

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; color: #721c24;'>";
    echo "<h3>❌ Erro:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p><strong>Arquivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Linha:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
}
?>
