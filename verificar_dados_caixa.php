<?php

// Script para verificar dados problemáticos no banco

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Caixa;
use App\Models\Pagamento;
use Carbon\Carbon;

echo "🔍 Verificando dados problemáticos...\n\n";

try {
    // 1. Verificar caixas com data_abertura nula
    $caixasProblematicos = Caixa::whereNull('data_abertura')->get();
    
    echo "📋 Caixas com data_abertura nula: " . $caixasProblematicos->count() . "\n";
    
    if ($caixasProblematicos->count() > 0) {
        echo "⚠️ Corrigindo caixas problemáticos...\n";
        
        foreach ($caixasProblematicos as $caixa) {
            $caixa->data_abertura = Carbon::now()->subHours(8);
            $caixa->save();
            echo "  ✅ Caixa #{$caixa->id} - data_abertura corrigida\n";
        }
    }
    
    // 2. Verificar total de caixas
    $totalCaixas = Caixa::count();
    echo "\n📊 Total de caixas no sistema: {$totalCaixas}\n";
    
    // 3. Verificar pagamentos
    $totalPagamentos = Pagamento::count();
    echo "💰 Total de pagamentos no sistema: {$totalPagamentos}\n";
    
    // 4. Mostrar alguns exemplos de dados
    echo "\n📝 Últimos 3 caixas:\n";
    $ultimosCaixas = Caixa::orderBy('id', 'desc')->take(3)->get();
    
    foreach ($ultimosCaixas as $caixa) {
        $dataAbertura = $caixa->data_abertura ? $caixa->data_abertura->format('d/m/Y H:i') : 'NULL';
        $dataFechamento = $caixa->data_fechamento ? $caixa->data_fechamento->format('d/m/Y H:i') : 'NULL';
        $status = $caixa->data_fechamento ? 'FECHADO' : 'ABERTO';
        
        echo "  🎯 Caixa #{$caixa->id} - Abertura: {$dataAbertura} - Fechamento: {$dataFechamento} - Status: {$status}\n";
    }
    
    echo "\n✅ Verificação concluída!\n";
    echo "🔗 Tente acessar: http://localhost:8000/caixa/historico\n\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📍 Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
}
