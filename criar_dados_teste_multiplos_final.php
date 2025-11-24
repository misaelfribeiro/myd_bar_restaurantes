<?php
// CRIAR DADOS DE TESTE PARA MÚLTIPLOS PAGAMENTOS
echo "<h1>🛠️ CRIANDO DADOS DE TESTE PARA MÚLTIPLOS PAGAMENTOS</h1>";

require_once 'vendor/autoload.php';

// Configurar ambiente Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    // Verificar se há caixa aberto
    $caixa = \App\Models\Caixa::where('status', 'aberto')->first();
    
    if (!$caixa) {
        echo "<p>⚠️ Nenhum caixa aberto. Criando caixa...</p>";
        
        $caixa = \App\Models\Caixa::create([
            'usuario_id' => 1,
            'valor_inicial' => 100.00,
            'data_abertura' => now(),
            'status' => 'aberto',
            'observacoes' => 'Caixa de teste para múltiplos pagamentos'
        ]);
        
        echo "<p>✅ Caixa #{$caixa->id} criado e aberto</p>";
    } else {
        echo "<p>✅ Caixa #{$caixa->id} já está aberto</p>";
    }
    
    // Buscar mesa de teste
    $mesa = \App\Models\Mesa::first();
    if (!$mesa) {
        $mesa = \App\Models\Mesa::create([
            'numero' => 1,
            'status' => 'disponivel',
            'capacidade' => 4
        ]);
        echo "<p>✅ Mesa #{$mesa->numero} criada</p>";
    }
    
    // Criar produto de teste se não existir
    $produto = \App\Models\Produto::first();
    if (!$produto) {
        $categoria = \App\Models\Categoria::first();
        if (!$categoria) {
            $categoria = \App\Models\Categoria::create([
                'nome' => 'Testes',
                'descricao' => 'Categoria para testes'
            ]);
        }
        
        $produto = \App\Models\Produto::create([
            'nome' => 'Hambúrguer Teste',
            'preco' => 25.00,
            'categoria_id' => $categoria->id,
            'ativo' => true,
            'descricao' => 'Produto para teste de múltiplos pagamentos'
        ]);
        echo "<p>✅ Produto '{$produto->nome}' criado</p>";
    }
    
    // Criar pedido de teste para múltiplos pagamentos
    $pedidoTeste = \App\Models\Pedido::create([
        'mesa_id' => $mesa->id,
        'usuario_id' => 1,
        'total' => 75.50, // Valor ideal para testar múltiplos pagamentos
        'status' => 'finalizado', // Importante: finalizado para poder receber pagamento
        'observacoes' => 'Pedido para teste de múltiplos pagamentos via API unificada'
    ]);
    
    // Criar item do pedido
    \App\Models\ItemPedido::create([
        'pedido_id' => $pedidoTeste->id,
        'produto_id' => $produto->id,
        'quantidade' => 3,
        'preco_unitario' => 25.00,
        'subtotal' => 75.00,
        'observacoes' => 'Item teste para múltiplos pagamentos'
    ]);
    
    // Atualizar total do pedido
    $pedidoTeste->update(['total' => 75.50]);
    
    echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 8px;'>";
    echo "<h2>🎯 DADOS DE TESTE CRIADOS COM SUCESSO!</h2>";
    echo "<p><strong>Caixa ID:</strong> {$caixa->id} (Status: {$caixa->status})</p>";
    echo "<p><strong>Mesa:</strong> {$mesa->numero}</p>";
    echo "<p><strong>Pedido ID:</strong> {$pedidoTeste->id}</p>";
    echo "<p><strong>Total do Pedido:</strong> R$ {$pedidoTeste->total}</p>";
    echo "<p><strong>Status:</strong> {$pedidoTeste->status}</p>";
    echo "</div>";
    
    echo "<div style='background: #e7f3ff; padding: 20px; margin: 20px 0; border-radius: 8px; text-align: center;'>";
    echo "<h2>🧪 LINKS PARA TESTE</h2>";
    
    echo "<p>";
    echo "<a href='/myd_bar_restaurantes/public/caixa/recebimento/{$pedidoTeste->id}' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 10px;'>💳 Testar Pagamento Único</a>";
    echo "</p>";
    
    echo "<p>";
    echo "<a href='/myd_bar_restaurantes/public/caixa/recebimento/{$pedidoTeste->id}' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 10px;'>💰 Testar Múltiplos Pagamentos</a>";
    echo "</p>";
    
    echo "<p>";
    echo "<a href='/myd_bar_restaurantes/teste_api_simples.php' style='background: #6f42c1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 10px;'>🔬 API Direta</a>";
    echo "</p>";
    
    echo "<p>";
    echo "<a href='/myd_bar_restaurantes/public/caixa' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 10px;'>📊 Voltar ao Caixa</a>";
    echo "</p>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
    echo "<h3>💡 Dicas para Teste de Múltiplos Pagamentos</h3>";
    echo "<ul>";
    echo "<li><strong>Cenário 1:</strong> R$ 30,00 (dinheiro) + R$ 45,50 (cartão) = R$ 75,50</li>";
    echo "<li><strong>Cenário 2:</strong> R$ 25,00 (PIX) + R$ 25,00 (cartão crédito) + R$ 25,50 (dinheiro) = R$ 75,50</li>";
    echo "<li><strong>Cenário 3:</strong> R$ 40,00 (vale refeição) + R$ 35,50 (cartão débito) = R$ 75,50</li>";
    echo "<li><strong>Abra o Console (F12):</strong> Para ver os logs da API unificada</li>";
    echo "<li><strong>Teste Fallback:</strong> Se a API falhar, o sistema usa método original</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 20px; color: #721c24; border-radius: 8px;'>";
    echo "<h2>❌ ERRO</h2>";
    echo "<p><strong>Mensagem:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Arquivo:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "</div>";
}
?>
