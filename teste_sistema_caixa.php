<?php

// Teste simples para verificar o funcionamento do sistema de caixa

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🚀 Testando Sistema de Caixa\n";
echo "============================\n\n";

try {
    // Teste 1: Verificar se não há caixa aberto
    $caixaAberto = App\Models\Caixa::where('status', 'aberto')->first();
    echo "📦 Caixa atualmente aberto: " . ($caixaAberto ? "SIM (ID: {$caixaAberto->id})" : "NÃO") . "\n";

    // Teste 2: Verificar pedidos finalizados pendentes de pagamento
    $pedidosFinalizados = App\Models\Pedido::where('status', 'finalizado')->get();
    echo "📋 Pedidos finalizados: " . $pedidosFinalizados->count() . "\n";
    
    if ($pedidosFinalizados->count() > 0) {
        $totalPendente = $pedidosFinalizados->sum('total');
        echo "💰 Total pendente de pagamento: R$ " . number_format($totalPendente, 2, ',', '.') . "\n";
    }

    // Teste 3: Verificar usuários disponíveis
    $usuarios = App\Models\Usuario::all();
    echo "👥 Usuários no sistema: " . $usuarios->count() . "\n";

    // Teste 4: Testar abertura de caixa
    if (!$caixaAberto && $usuarios->count() > 0) {
        echo "\n🔄 Testando abertura de caixa...\n";
        
        $usuario = $usuarios->first();
        $caixa = App\Models\Caixa::create([
            'usuario_id' => $usuario->id,
            'saldo_inicial' => 100.00,
            'status' => 'aberto',
            'data_abertura' => now(),
            'observacoes_abertura' => 'Caixa aberto para teste do sistema'
        ]);
        
        echo "✅ Caixa aberto com sucesso! ID: {$caixa->id}\n";
        echo "👤 Operador: {$usuario->nome}\n";
        echo "💵 Saldo inicial: R$ 100,00\n";
    }

    // Teste 5: Simular um pagamento
    if ($pedidosFinalizados->count() > 0 && App\Models\Caixa::where('status', 'aberto')->exists()) {
        echo "\n💳 Testando processamento de pagamento...\n";
        
        $pedido = $pedidosFinalizados->first();
        $caixa = App\Models\Caixa::where('status', 'aberto')->first();
        
        $pagamento = App\Models\Pagamento::create([
            'pedido_id' => $pedido->id,
            'forma_pagamento' => 'dinheiro',
            'valor' => $pedido->total,
            'valor_recebido' => $pedido->total + 10, // Simula recebimento com troco
            'troco' => 10.00,
            'status' => 'confirmado',
            'usuario_id' => $usuario->id,
            'data_pagamento' => now(),
            'observacoes' => 'Pagamento de teste - Sistema funcionando!'
        ]);
        
        // Atualiza status do pedido
        $pedido->update(['status' => 'pago']);
        
        // Atualiza totais do caixa
        $caixa->increment('total_vendas', $pagamento->valor);
        $caixa->increment('total_dinheiro', $pagamento->valor);
        
        echo "✅ Pagamento processado com sucesso!\n";
        echo "🆔 ID do pagamento: {$pagamento->id}\n";
        echo "💰 Valor: R$ " . number_format($pagamento->valor, 2, ',', '.') . "\n";
        echo "💵 Recebido: R$ " . number_format($pagamento->valor_recebido, 2, ',', '.') . "\n";
        echo "🔄 Troco: R$ " . number_format($pagamento->troco, 2, ',', '.') . "\n";
        echo "📄 Pedido #{$pedido->id} marcado como pago\n";
    }

    echo "\n🎯 TESTE CONCLUÍDO COM SUCESSO!\n";
    echo "🌐 Acesse: http://127.0.0.1:8000/caixa\n";
    echo "📊 Para visualizar o dashboard do caixa\n";

} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "📍 Arquivo: " . $e->getFile() . " (linha " . $e->getLine() . ")\n";
}
