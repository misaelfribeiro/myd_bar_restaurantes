<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Caixa;
use App\Models\Pagamento;

echo "=== DEBUG DO PROBLEMA DE CÁLCULO NO HISTÓRICO ===\n\n";

// Buscar o caixa mais recente
$caixa = Caixa::orderBy('data_abertura', 'desc')->first();

if (!$caixa) {
    echo "Nenhum caixa encontrado!\n";
    exit;
}

echo "--- Caixa #{$caixa->id} ---\n";
echo "Status: {$caixa->status}\n";
echo "Data Abertura: " . $caixa->data_abertura->format('d/m/Y H:i') . "\n";
echo "Saldo Inicial: R$ " . number_format($caixa->saldo_inicial ?? 0, 2, ',', '.') . "\n\n";

// Verificar totais salvos no banco
echo "=== TOTAIS SALVOS NO BANCO ===\n";
echo "total_vendas: R$ " . number_format($caixa->total_vendas ?? 0, 2, ',', '.') . "\n";
echo "total_dinheiro: R$ " . number_format($caixa->total_dinheiro ?? 0, 2, ',', '.') . "\n";
echo "total_cartao: R$ " . number_format($caixa->total_cartao ?? 0, 2, ',', '.') . "\n";
echo "total_cartao_credito: R$ " . number_format($caixa->total_cartao_credito ?? 0, 2, ',', '.') . "\n";
echo "total_cartao_debito: R$ " . number_format($caixa->total_cartao_debito ?? 0, 2, ',', '.') . "\n";
echo "total_pix: R$ " . number_format($caixa->total_pix ?? 0, 2, ',', '.') . "\n";
echo "total_vale: R$ " . number_format($caixa->total_vale ?? 0, 2, ',', '.') . "\n\n";

// Buscar pagamentos específicos deste caixa
$pagamentos = Pagamento::where('status', 'confirmado')
    ->where('data_pagamento', '>=', $caixa->data_abertura)
    ->when($caixa->data_fechamento, function ($query) use ($caixa) {
        return $query->where('data_pagamento', '<=', $caixa->data_fechamento);
    })
    ->get();

echo "=== PAGAMENTOS REAIS ENCONTRADOS ===\n";
echo "Total de pagamentos: " . $pagamentos->count() . "\n\n";

foreach($pagamentos as $pagamento) {
    echo "- Pagamento ID: {$pagamento->id}\n";
    echo "  Pedido ID: {$pagamento->pedido_id}\n";
    echo "  Forma: {$pagamento->forma_pagamento}\n";
    echo "  Valor: R$ " . number_format($pagamento->valor, 2, ',', '.') . "\n";
    echo "  Data: " . $pagamento->data_pagamento . "\n";
    echo "  Caixa ID: {$pagamento->caixa_id}\n\n";
}

// Calcular totais por forma de pagamento
$totaisPorForma = $pagamentos->groupBy('forma_pagamento')->map(function ($pagamentosForma) {
    return $pagamentosForma->sum('valor');
});

echo "=== TOTAIS CALCULADOS POR FORMA ===\n";
foreach($totaisPorForma as $forma => $total) {
    echo "{$forma}: R$ " . number_format($total, 2, ',', '.') . "\n";
}

echo "\nTotal Geral: R$ " . number_format($pagamentos->sum('valor'), 2, ',', '.') . "\n\n";

// Testar o método getTotalizacoes()
echo "=== RESULTADO DO getTotalizacoes() ===\n";
$totalizacoes = $caixa->getTotalizacoes();

echo "total_vendas: R$ " . number_format($totalizacoes['total_vendas'], 2, ',', '.') . "\n";
echo "quantidade_vendas: " . $totalizacoes['quantidade_vendas'] . "\n\n";

echo "Por forma de pagamento:\n";
foreach($totalizacoes['por_forma_pagamento'] as $forma => $dados) {
    echo "- {$forma}: R$ " . number_format($dados['total'], 2, ',', '.') . "\n";
}

echo "\n=== ANÁLISE ===\n";
$totalCalculado = $pagamentos->sum('valor');
$totalSalvo = $caixa->total_vendas ?? 0;
$diferenca = abs($totalCalculado - $totalSalvo);

echo "Total calculado: R$ " . number_format($totalCalculado, 2, ',', '.') . "\n";
echo "Total salvo no banco: R$ " . number_format($totalSalvo, 2, ',', '.') . "\n";
echo "Diferença: R$ " . number_format($diferenca, 2, ',', '.') . "\n";

if ($diferenca > 0.01) {
    echo "⚠️  PROBLEMA DETECTADO: Há diferença nos valores!\n";
} else {
    echo "✅ Valores conferem!\n";
}

echo "\n=== FIM DO DEBUG ===\n";
