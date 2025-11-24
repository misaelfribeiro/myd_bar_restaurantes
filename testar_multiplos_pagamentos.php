<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Caixa;
use App\Models\Pedido;
use App\Models\Usuario;
use App\Models\Mesa;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\ItemPedido;
use App\Models\Pagamento;

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE DE MÚLTIPLOS PAGAMENTOS ===\n\n";

try {
    // 1. Verificar se há caixa aberto
    $caixaAberto = Caixa::where('status', 'aberto')->first();
    if (!$caixaAberto) {
        echo "❌ Nenhum caixa aberto encontrado. Abrindo caixa...\n";
        
        $usuario = Usuario::first();
        if (!$usuario) {
            throw new Exception("Nenhum usuário encontrado");
        }
        
        $caixaAberto = Caixa::create([
            'usuario_id' => $usuario->id,
            'data_abertura' => now(),
            'saldo_inicial' => 100,
            'status' => 'aberto'
        ]);
        echo "✅ Caixa aberto com ID: {$caixaAberto->id}\n";
    } else {
        echo "✅ Caixa aberto encontrado - ID: {$caixaAberto->id}\n";
    }

    // 2. Verificar se existe um pedido em aberto ou criar um
    $pedido = Pedido::where('status', 'finalizado')
        ->whereDoesntHave('pagamentos', function($query) {
            $query->where('status', 'confirmado');
        })
        ->first();
    
    if (!$pedido) {
        echo "Criando pedido de teste...\n";
        
        // Buscar mesa
        $mesa = Mesa::first();
        if (!$mesa) {
            throw new Exception("Nenhuma mesa encontrada");
        }
        
        // Buscar produto
        $produto = Produto::first();
        if (!$produto) {
            throw new Exception("Nenhum produto encontrado");
        }
        
        $usuario = Usuario::first();
        
        // Criar pedido
        $pedido = Pedido::create([
            'mesa_id' => $mesa->id,
            'usuario_id' => $usuario->id,
            'total' => 45.50,
            'status' => 'finalizado'
        ]);
        
        // Adicionar item ao pedido
        ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'preco_unitario' => 22.75
        ]);
        
        echo "✅ Pedido criado - ID: {$pedido->id}, Total: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
    } else {
        echo "✅ Pedido encontrado - ID: {$pedido->id}, Total: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
    }

    // 3. Testar múltiplos pagamentos
    echo "\n--- TESTANDO MÚLTIPLOS PAGAMENTOS ---\n";
    
    $totalPedido = $pedido->total;
    $pagamento1 = $totalPedido * 0.6; // 60% em dinheiro
    $pagamento2 = $totalPedido * 0.4; // 40% em cartão
    
    echo "Total do pedido: R$ " . number_format($totalPedido, 2, ',', '.') . "\n";
    echo "Pagamento 1 (Dinheiro): R$ " . number_format($pagamento1, 2, ',', '.') . "\n";
    echo "Pagamento 2 (Cartão): R$ " . number_format($pagamento2, 2, ',', '.') . "\n";
    
    // Verificar se os modelos têm os campos necessários
    echo "\n--- VERIFICANDO ESTRUTURA DA TABELA ---\n";
    
    // Testar criação de pagamento individual
    $testePagamento = new Pagamento();
    $fillableFields = $testePagamento->getFillable();
    echo "Campos fillable em Pagamento: " . implode(', ', $fillableFields) . "\n";
    
    if (in_array('caixa_id', $fillableFields)) {
        echo "✅ Campo caixa_id está no fillable\n";
    } else {
        echo "❌ Campo caixa_id NÃO está no fillable\n";
    }
    
    // Verificar se a coluna existe na tabela
    try {
        \DB::select('DESCRIBE pagamentos');
        $columns = \DB::select('SHOW COLUMNS FROM pagamentos');
        $columnNames = array_column($columns, 'Field');
        
        if (in_array('caixa_id', $columnNames)) {
            echo "✅ Coluna caixa_id existe na tabela pagamentos\n";
        } else {
            echo "❌ Coluna caixa_id NÃO existe na tabela pagamentos\n";
            echo "Colunas existentes: " . implode(', ', $columnNames) . "\n";
        }
    } catch (Exception $e) {
        echo "❌ Erro ao verificar estrutura da tabela: " . $e->getMessage() . "\n";
    }
    
    // 4. Simular criação de múltiplos pagamentos
    echo "\n--- SIMULANDO CRIAÇÃO DE PAGAMENTOS ---\n";
    
    $usuario = Usuario::first();
    
    try {
        \DB::beginTransaction();
        
        // Primeiro pagamento - Dinheiro
        $pag1 = Pagamento::create([
            'pedido_id' => $pedido->id,
            'caixa_id' => $caixaAberto->id,
            'usuario_id' => $usuario->id,
            'forma_pagamento' => 'dinheiro',
            'valor' => $pagamento1,
            'valor_recebido' => $pagamento1,
            'troco' => 0,
            'status' => 'confirmado',
            'data_pagamento' => now()
        ]);
        
        echo "✅ Primeiro pagamento criado - ID: {$pag1->id}\n";
        
        // Segundo pagamento - Cartão
        $pag2 = Pagamento::create([
            'pedido_id' => $pedido->id,
            'caixa_id' => $caixaAberto->id,
            'usuario_id' => $usuario->id,
            'forma_pagamento' => 'cartao_credito',
            'valor' => $pagamento2,
            'valor_recebido' => $pagamento2,
            'troco' => 0,
            'status' => 'confirmado',
            'data_pagamento' => now()
        ]);
        
        echo "✅ Segundo pagamento criado - ID: {$pag2->id}\n";
        
        // Verificar se o pedido está totalmente pago
        $pedido->refresh();
        $totalPago = $pedido->pagamentos()->where('status', 'confirmado')->sum('valor');
        $saldoRestante = $pedido->total - $totalPago;
        
        echo "Total pago: R$ " . number_format($totalPago, 2, ',', '.') . "\n";
        echo "Saldo restante: R$ " . number_format($saldoRestante, 2, ',', '.') . "\n";
        
        if (abs($saldoRestante) < 0.01) {
            echo "✅ Pedido totalmente pago!\n";
            $pedido->update(['status' => 'pago']);
        } else {
            echo "⚠️ Pedido ainda tem saldo restante\n";
        }
        
        \DB::commit();
        
    } catch (Exception $e) {
        \DB::rollback();
        echo "❌ Erro ao criar pagamentos: " . $e->getMessage() . "\n";
    }
    
    // 5. Verificar totalizações do caixa
    echo "\n--- VERIFICANDO TOTALIZAÇÕES ---\n";
    
    if (method_exists($caixaAberto, 'getTotalizacoesPorPeriodo')) {
        $totalizacoes = $caixaAberto->getTotalizacoesPorPeriodo();
        echo "✅ Método getTotalizacoesPorPeriodo existe\n";
        echo "Total de vendas: R$ " . number_format($totalizacoes['total_vendas'] ?? 0, 2, ',', '.') . "\n";
        echo "Quantidade de vendas: " . ($totalizacoes['quantidade_vendas'] ?? 0) . "\n";
    } else {
        echo "❌ Método getTotalizacoesPorPeriodo não existe no modelo Caixa\n";
    }
    
    echo "\n✅ TESTE CONCLUÍDO COM SUCESSO!\n";

} catch (Exception $e) {
    echo "❌ ERRO DURANTE O TESTE: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
