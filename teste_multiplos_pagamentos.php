<?php
// Teste para múltiplos pagamentos
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\{Pedido, Mesa, Produto, ItemPedido, Categoria, Usuario, Caixa};
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();
    
    echo "=== TESTE DE MÚLTIPLOS PAGAMENTOS ===\n\n";
    
    // 1. Verificar se há um caixa aberto ou abrir um novo
    $caixaAberto = Caixa::caixaAbertoHoje();
    if (!$caixaAberto) {
        echo "Abrindo caixa...\n";
        $usuario = Usuario::first();
        if (!$usuario) {
            throw new \Exception('Nenhum usuário encontrado');
        }
        
        $caixaAberto = Caixa::create([
            'usuario_id' => $usuario->id,
            'data_abertura' => now(),
            'saldo_inicial' => 100.00,
            'status' => 'aberto',
            'observacoes_abertura' => 'Teste de múltiplos pagamentos'
        ]);
        echo "✅ Caixa aberto com sucesso! ID: {$caixaAberto->id}\n\n";
    } else {
        echo "✅ Caixa já está aberto! ID: {$caixaAberto->id}\n\n";
    }
    
    // 2. Criar ou buscar mesa
    $mesa = Mesa::first();
    if (!$mesa) {
        $mesa = Mesa::create([
            'numero' => 1,
            'capacidade' => 4,
            'status' => 'ocupada'
        ]);
    }
    echo "✅ Mesa disponível: {$mesa->numero}\n";
    
    // 3. Criar ou buscar produtos
    $categoria = Categoria::firstOrCreate(['nome' => 'Bebidas']);
    $produto1 = Produto::firstOrCreate([
        'nome' => 'Refrigerante'
    ], [
        'categoria_id' => $categoria->id,
        'preco' => 5.00,
        'disponivel' => true
    ]);
    
    $produto2 = Produto::firstOrCreate([
        'nome' => 'Hambúrguer'
    ], [
        'categoria_id' => $categoria->id,
        'preco' => 15.00,
        'disponivel' => true
    ]);
    
    echo "✅ Produtos disponíveis:\n";
    echo "   - {$produto1->nome}: R$ {$produto1->preco}\n";
    echo "   - {$produto2->nome}: R$ {$produto2->preco}\n\n";
    
    // 4. Criar pedido de teste
    $pedido = Pedido::create([
        'mesa_id' => $mesa->id,
        'usuario_id' => $usuario->id ?? 1,
        'status' => 'finalizado',
        'observacoes' => 'Pedido para teste de múltiplos pagamentos'
    ]);
    
    // Adicionar itens ao pedido
    ItemPedido::create([
        'pedido_id' => $pedido->id,
        'produto_id' => $produto1->id,
        'quantidade' => 2,
        'preco_unitario' => $produto1->preco
    ]);
    
    ItemPedido::create([
        'pedido_id' => $pedido->id,
        'produto_id' => $produto2->id,
        'quantidade' => 1,
        'preco_unitario' => $produto2->preco
    ]);
    
    $pedido->refresh();
    $total = $pedido->total;
    
    echo "✅ Pedido criado!\n";
    echo "   ID: {$pedido->id}\n";
    echo "   Mesa: {$mesa->numero}\n";
    echo "   Total: R$ {$total}\n\n";
    
    // 5. Simular dados de múltiplos pagamentos
    $multiplosPagamentos = [
        [
            'forma_pagamento' => 'dinheiro',
            'valor' => 15.00
        ],
        [
            'forma_pagamento' => 'cartao_credito', 
            'valor' => 10.00
        ]
    ];
    
    echo "=== SIMULANDO MÚLTIPLOS PAGAMENTOS ===\n";
    echo "Formas de pagamento:\n";
    foreach ($multiplosPagamentos as $i => $forma) {
        echo "   " . ($i + 1) . ". {$forma['forma_pagamento']}: R$ {$forma['valor']}\n";
    }
    echo "Total: R$ " . array_sum(array_column($multiplosPagamentos, 'valor')) . "\n\n";
    
    // 6. Simular request e controller
    echo "=== TESTANDO LÓGICA DO CONTROLLER ===\n";
    
    $app = app();
    $controller = $app->make(\App\Http\Controllers\CaixaController::class);
    
    // Criar mock request
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'multiplos_pagamentos' => json_encode($multiplosPagamentos)
    ]);
    
    // Chamar método processarPagamento
    $response = $controller->processarPagamento($request, $pedido);
    
    // Verificar resposta
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $responseData = $response->getData(true);
        if (isset($responseData['success']) && $responseData['success']) {
            echo "✅ SUCESSO!\n";
            echo "Mensagem: {$responseData['message']}\n";
            echo "Pagamentos processados: " . count($responseData['pagamentos']) . "\n";
            echo "Total pago: R$ {$responseData['total_pago']}\n\n";
            
            // Verificar no banco de dados
            $pagamentosDB = \App\Models\Pagamento::where('pedido_id', $pedido->id)->get();
            echo "=== VERIFICAÇÃO NO BANCO ===\n";
            echo "Pagamentos salvos: {$pagamentosDB->count()}\n";
            foreach ($pagamentosDB as $pg) {
                echo "   - {$pg->forma_pagamento}: R$ {$pg->valor} (Status: {$pg->status})\n";
            }
            
            $pedido->refresh();
            echo "\nStatus do pedido: {$pedido->status}\n";
            echo "Pedido está pago? " . ($pedido->isPago() ? 'SIM' : 'NÃO') . "\n";
            
        } else {
            echo "❌ ERRO!\n";
            echo "Resposta: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "❌ ERRO: Resposta inválida\n";
        var_dump($response);
    }
    
    DB::commit();
    echo "\n✅ Teste concluído com sucesso!\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
}
