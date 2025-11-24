<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Caixa;
use Carbon\Carbon;

echo "=== DIAGNÓSTICO DO CAIXA ===\n\n";

// Verificar data/hora atual
echo "📅 Data/Hora Atual do Sistema:\n";
echo "now(): " . now()->format('Y-m-d H:i:s') . "\n";
echo "today(): " . today()->format('Y-m-d') . "\n";
echo "Carbon::today(): " . Carbon::today()->format('Y-m-d') . "\n\n";

// Buscar TODOS os caixas
echo "📦 Todos os Caixas no Sistema:\n";
$todosCaixas = Caixa::orderBy('data_abertura', 'desc')->get();

if ($todosCaixas->count() === 0) {
    echo "❌ Nenhum caixa encontrado no sistema!\n\n";
} else {
    echo "Total: {$todosCaixas->count()} caixas\n\n";
    
    foreach ($todosCaixas as $caixa) {
        echo "ID: {$caixa->id}\n";
        echo "Status: {$caixa->status}\n";
        echo "Data Abertura: " . ($caixa->data_abertura ? $caixa->data_abertura->format('Y-m-d H:i:s') : 'NULL') . "\n";
        echo "Data Fechamento: " . ($caixa->data_fechamento ? $caixa->data_fechamento->format('Y-m-d H:i:s') : 'NULL') . "\n";
        echo "Saldo Inicial: R$ " . number_format($caixa->saldo_inicial ?? 0, 2, ',', '.') . "\n";
        echo "---\n";
    }
    echo "\n";
}

// Buscar caixas ABERTOS
echo "🔓 Caixas com Status 'aberto':\n";
$caixasAbertos = Caixa::where('status', 'aberto')->get();

if ($caixasAbertos->count() === 0) {
    echo "❌ Nenhum caixa com status 'aberto'\n\n";
} else {
    echo "Total: {$caixasAbertos->count()}\n\n";
    
    foreach ($caixasAbertos as $caixa) {
        echo "ID: {$caixa->id}\n";
        echo "Data Abertura: " . $caixa->data_abertura->format('Y-m-d H:i:s') . "\n";
        echo "Dias desde abertura: " . $caixa->data_abertura->diffInDays(now()) . "\n";
        echo "---\n";
    }
    echo "\n";
}

// Buscar caixa aberto HOJE usando o método do model
echo "🔍 Busca usando Caixa::caixaAbertoHoje():\n";
$caixaHoje = Caixa::caixaAbertoHoje();

if ($caixaHoje) {
    echo "✅ Caixa encontrado!\n";
    echo "ID: {$caixaHoje->id}\n";
    echo "Data Abertura: " . $caixaHoje->data_abertura->format('Y-m-d H:i:s') . "\n";
} else {
    echo "❌ Nenhum caixa aberto HOJE encontrado\n";
}
echo "\n";

// Diagnóstico da Query
echo "🔬 Análise Detalhada da Query:\n";
echo "Query SQL:\n";
$query = Caixa::where('status', 'aberto')
              ->whereDate('data_abertura', today());
              
echo $query->toSql() . "\n";
echo "Bindings: " . json_encode($query->getBindings()) . "\n";
echo "Resultado: " . ($query->first() ? "ENCONTRADO" : "NÃO ENCONTRADO") . "\n\n";

// Sugestão de correção
echo "💡 DIAGNÓSTICO:\n";

if ($caixasAbertos->count() > 0) {
    $caixaMaisRecente = $caixasAbertos->first();
    $diasAberto = $caixaMaisRecente->data_abertura->diffInDays(now());
    
    if ($diasAberto > 0) {
        echo "⚠️  PROBLEMA IDENTIFICADO!\n";
        echo "Existe um caixa aberto há {$diasAberto} dia(s).\n";
        echo "O método caixaAbertoHoje() busca apenas caixas abertos HOJE.\n";
        echo "Caixa ID #{$caixaMaisRecente->id} foi aberto em: " . $caixaMaisRecente->data_abertura->format('d/m/Y') . "\n\n";
        
        echo "📋 SOLUÇÕES:\n";
        echo "1. Fechar o caixa antigo manualmente\n";
        echo "2. Modificar o método caixaAbertoHoje() para buscar qualquer caixa aberto\n";
        echo "3. Permitir apenas um caixa aberto por vez (independente da data)\n\n";
        
        echo "🔧 COMANDO PARA FECHAR O CAIXA ANTIGO:\n";
        echo "UPDATE caixa SET status = 'fechado', data_fechamento = NOW() WHERE id = {$caixaMaisRecente->id};\n";
    } else {
        echo "✅ Há um caixa aberto hoje, mas a query não está encontrando.\n";
        echo "Verifique o timezone ou formato da data.\n";
    }
} else {
    echo "✅ Nenhum caixa aberto no sistema. Tudo normal.\n";
}

echo "\n=== FIM DO DIAGNÓSTICO ===\n";
