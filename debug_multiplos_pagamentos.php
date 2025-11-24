<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Caixa;
use App\Models\Pedido;
use App\Models\Usuario;
use App\Models\Mesa;
use App\Models\Produto;
use App\Models\ItemPedido;
use App\Models\Pagamento;

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE DE DEBUG MÚLTIPLOS PAGAMENTOS ===\n\n";

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

    // 2. Limpar pagamentos anteriores de teste
    Pagamento::whereHas('pedido', function($query) {
        $query->where('status', '!=', 'pago');
    })->delete();

    // 3. Buscar ou criar um pedido finalizado sem pagamentos
    $pedido = Pedido::where('status', 'finalizado')
        ->whereDoesntHave('pagamentos')
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
            'total' => 150.00,
            'status' => 'finalizado'
        ]);
        
        // Adicionar item ao pedido
        ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 3,
            'preco_unitario' => 50.00
        ]);
        
        echo "✅ Pedido criado - ID: {$pedido->id}, Total: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
    } else {
        echo "✅ Pedido encontrado - ID: {$pedido->id}, Total: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
    }

    // 4. Verificar se o pedido precisa de pagamento
    $pedido->refresh();
    $totalPago = $pedido->pagamentos()->where('status', 'confirmado')->sum('valor');
    $saldoRestante = $pedido->total - $totalPago;

    echo "Total do pedido: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
    echo "Total pago: R$ " . number_format($totalPago, 2, ',', '.') . "\n";
    echo "Saldo restante: R$ " . number_format($saldoRestante, 2, ',', '.') . "\n";

    if ($saldoRestante <= 0) {
        echo "⚠️ Pedido já está pago. Removendo pagamentos para teste...\n";
        $pedido->pagamentos()->delete();
        $pedido->update(['status' => 'finalizado']);
        echo "✅ Pagamentos removidos, pedido pronto para teste\n";
    }

    // 5. Mostrar URL para teste
    echo "\n--- INFORMAÇÕES PARA TESTE ---\n";
    echo "URL para testar múltiplos pagamentos:\n";
    echo "http://localhost:8000/caixa/recebimento/{$pedido->id}\n";
    echo "\nDados do pedido:\n";
    echo "- ID: {$pedido->id}\n";
    echo "- Mesa: " . ($pedido->mesa->numero ?? 'N/A') . "\n";
    echo "- Total: R$ " . number_format($pedido->total, 2, ',', '.') . "\n";
    echo "- Status: {$pedido->status}\n";

    // 6. Verificar se método isPago() funciona
    echo "\nVerificação isPago():\n";
    echo "- isPago(): " . ($pedido->isPago() ? 'SIM' : 'NÃO') . "\n";
    echo "- total_pago: R$ " . number_format($pedido->total_pago, 2, ',', '.') . "\n";
    echo "- saldo_restante: R$ " . number_format($pedido->saldo_restante, 2, ',', '.') . "\n";

    // 7. Testar simulação de múltiplos pagamentos
    echo "\n--- SIMULAÇÃO DE MÚLTIPLOS PAGAMENTOS ---\n";
    
    $multiplosPagamentos = [
        ['forma_pagamento' => 'dinheiro', 'valor' => 80.00],
        ['forma_pagamento' => 'cartao_credito', 'valor' => 50.00],
        ['forma_pagamento' => 'pix', 'valor' => 20.00]
    ];

    echo "Testando dados que seriam enviados via AJAX:\n";
    echo "JSON: " . json_encode($multiplosPagamentos) . "\n";

    $totalMultiplos = array_sum(array_column($multiplosPagamentos, 'valor'));
    echo "Total múltiplos: R$ " . number_format($totalMultiplos, 2, ',', '.') . "\n";
    echo "Diferença: R$ " . number_format(abs($pedido->total - $totalMultiplos), 2, ',', '.') . "\n";

    if (abs($pedido->total - $totalMultiplos) < 0.01) {
        echo "✅ Valores conferem!\n";
    } else {
        echo "❌ Valores não conferem!\n";
    }

    echo "\n✅ TESTE DE DEBUG CONCLUÍDO!\n";
    echo "Acesse a URL acima e teste os múltiplos pagamentos.\n";

} catch (Exception $e) {
    echo "❌ ERRO DURANTE O TESTE: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
