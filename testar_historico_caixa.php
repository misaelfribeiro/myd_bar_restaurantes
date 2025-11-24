<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Caixa;
use App\Models\Pagamento;

echo "=== TESTE DO HISTÓRICO DE CAIXA ===\n\n";

// Buscar todos os caixas
$caixas = Caixa::orderBy('data_abertura', 'desc')->get();

echo "Total de caixas encontrados: " . $caixas->count() . "\n\n";

foreach($caixas as $caixa) {
    echo "--- Caixa #{$caixa->id} ---\n";
    echo "Status: {$caixa->status}\n";
    echo "Data Abertura: " . $caixa->data_abertura->format('d/m/Y H:i') . "\n";
    
    if($caixa->data_fechamento) {
        echo "Data Fechamento: " . $caixa->data_fechamento->format('d/m/Y H:i') . "\n";
    }
    
    // Calcular totalizações
    $totalizacoes = $caixa->getTotalizacoes();
    
    echo "Saldo Inicial: R$ " . number_format($caixa->saldo_inicial ?? 0, 2, ',', '.') . "\n";
    echo "Total Vendas: R$ " . number_format($totalizacoes['total_vendas'], 2, ',', '.') . "\n";
    echo "Total Dinheiro: R$ " . number_format($totalizacoes['por_forma_pagamento']['dinheiro']['total'] ?? 0, 2, ',', '.') . "\n";
    echo "Total Cartão Crédito: R$ " . number_format($totalizacoes['por_forma_pagamento']['cartao_credito']['total'] ?? 0, 2, ',', '.') . "\n";
    echo "Total Cartão Débito: R$ " . number_format($totalizacoes['por_forma_pagamento']['cartao_debito']['total'] ?? 0, 2, ',', '.') . "\n";
    echo "Total PIX: R$ " . number_format($totalizacoes['por_forma_pagamento']['pix']['total'] ?? 0, 2, ',', '.') . "\n";
    echo "Total Vale: R$ " . number_format($totalizacoes['por_forma_pagamento']['vale_refeicao']['total'] ?? 0, 2, ',', '.') . "\n";
    echo "Quantidade Vendas: " . $totalizacoes['quantidade_vendas'] . "\n";
    
    // Calcular saldo final
    $totalDinheiro = $totalizacoes['por_forma_pagamento']['dinheiro']['total'] ?? 0;
    $saldoFinal = ($caixa->saldo_inicial ?? 0) + $totalDinheiro;
    $diferenca = $saldoFinal - ($caixa->saldo_inicial ?? 0);
    
    echo "Saldo Final: R$ " . number_format($saldoFinal, 2, ',', '.') . "\n";
    echo "Diferença: R$ " . number_format($diferenca, 2, ',', '.') . "\n";
    
    echo "\n";
}

echo "=== FIM DO TESTE ===\n";
