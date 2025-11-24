<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFICAÇÃO DIRETA NO BANCO DE DADOS ===\n\n";

// Verificar caixas
$caixas = DB::table('caixa')->orderBy('data_abertura', 'desc')->limit(3)->get();

echo "--- CAIXAS RECENTES ---\n";
foreach($caixas as $caixa) {
    echo "Caixa ID: {$caixa->id}\n";
    echo "Status: {$caixa->status}\n";
    echo "Data Abertura: {$caixa->data_abertura}\n";
    echo "Total Vendas: R$ " . number_format($caixa->total_vendas ?? 0, 2, ',', '.') . "\n";
    echo "Total Dinheiro: R$ " . number_format($caixa->total_dinheiro ?? 0, 2, ',', '.') . "\n";
    echo "Total Cartão: R$ " . number_format($caixa->total_cartao ?? 0, 2, ',', '.') . "\n";
    echo "Total Cartão Crédito: R$ " . number_format($caixa->total_cartao_credito ?? 0, 2, ',', '.') . "\n";
    echo "Total Cartão Débito: R$ " . number_format($caixa->total_cartao_debito ?? 0, 2, ',', '.') . "\n";
    echo "Saldo Inicial: R$ " . number_format($caixa->saldo_inicial ?? 0, 2, ',', '.') . "\n\n";
}

// Verificar pagamentos recentes
$pagamentos = DB::table('pagamentos')
    ->where('status', 'confirmado')
    ->orderBy('data_pagamento', 'desc')
    ->limit(10)
    ->get();

echo "--- PAGAMENTOS RECENTES ---\n";
foreach($pagamentos as $pagamento) {
    echo "ID: {$pagamento->id} | Pedido: {$pagamento->pedido_id} | Caixa: {$pagamento->caixa_id}\n";
    echo "Forma: {$pagamento->forma_pagamento} | Valor: R$ " . number_format($pagamento->valor, 2, ',', '.') . "\n";
    echo "Data: {$pagamento->data_pagamento}\n\n";
}

// Verificar se há pagamentos com caixa_id NULL
$pagamentosSemCaixa = DB::table('pagamentos')
    ->where('status', 'confirmado')
    ->whereNull('caixa_id')
    ->count();

echo "Pagamentos sem caixa_id: {$pagamentosSemCaixa}\n\n";

// Verificar total de pagamentos por caixa
$totaisPorCaixa = DB::table('pagamentos')
    ->select('caixa_id', DB::raw('SUM(valor) as total'), DB::raw('COUNT(*) as quantidade'))
    ->where('status', 'confirmado')
    ->whereNotNull('caixa_id')
    ->groupBy('caixa_id')
    ->orderBy('caixa_id', 'desc')
    ->limit(5)
    ->get();

echo "--- TOTAIS POR CAIXA (CALCULADOS) ---\n";
foreach($totaisPorCaixa as $total) {
    echo "Caixa {$total->caixa_id}: R$ " . number_format($total->total, 2, ',', '.') . " ({$total->quantidade} pagamentos)\n";
}

echo "\n=== FIM DA VERIFICAÇÃO ===\n";
