<?php
// Script para testar dados do dashboard

require_once 'bootstrap/app.php';

$app = \Illuminate\Foundation\Application::getInstance();
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pedido;
use App\Models\Mesa;
use Illuminate\Support\Facades\Schema;

echo "🔍 DIAGNÓSTICO DO DASHBOARD GARÇOM\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    // Verificar estrutura da tabela pedidos
    echo "📊 ESTRUTURA DA TABELA PEDIDOS:\n";
    $columns = Schema::getColumnListing('pedidos');
    foreach ($columns as $column) {
        echo "  - $column\n";
    }
    echo "\n";
    
    // Verificar dados dos pedidos
    echo "📋 DADOS DOS PEDIDOS:\n";
    $pedidos = Pedido::all();
    echo "  Total de pedidos: " . $pedidos->count() . "\n";
    
    if ($pedidos->count() > 0) {
        echo "  Últimos 3 pedidos:\n";
        foreach ($pedidos->take(3) as $pedido) {
            $total = $pedido->total ?? 'N/A';
            $valorTotal = $pedido->valor_total ?? 'N/A';
            echo "    - Pedido #{$pedido->id}: total=$total, valor_total=$valorTotal, status={$pedido->status}\n";
        }
    }
    echo "\n";
    
    // Verificar dados das mesas
    echo "🪑 DADOS DAS MESAS:\n";
    $mesas = Mesa::all();
    echo "  Total de mesas: " . $mesas->count() . "\n";
    
    // Verificar mesas ocupadas
    $mesasOcupadas = Mesa::whereHas('pedidos', function($query) { 
        $query->where('status', 'aberto'); 
    })->count();
    echo "  Mesas ocupadas: $mesasOcupadas\n";
    
    // Verificar estrutura da tabela mesas
    echo "\n📊 ESTRUTURA DA TABELA MESAS:\n";
    $columnsMesas = Schema::getColumnListing('mesas');
    foreach ($columnsMesas as $column) {
        echo "  - $column\n";
    }
    echo "\n";
    
    // Testar consultas do dashboard
    echo "🧮 SIMULANDO DADOS DO DASHBOARD:\n";
    $userId = 1;
    
    $meusPedidosHoje = Pedido::where('usuario_id', $userId)->whereDate('created_at', today())->count();
    $minhaVendaHoje = Pedido::where('usuario_id', $userId)->whereDate('created_at', today())->sum('total');
    $mesasDisponiveis = Mesa::count();
    $mesasOcupadasCount = Mesa::whereHas('pedidos', function($query) { 
        $query->where('status', 'aberto'); 
    })->count();
    
    echo "  Meus pedidos hoje: $meusPedidosHoje\n";
    echo "  Minha venda hoje: R$ " . number_format($minhaVendaHoje, 2, ',', '.') . "\n";
    echo "  Mesas disponíveis: $mesasDisponiveis\n";
    echo "  Mesas ocupadas: $mesasOcupadasCount\n";
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "📁 Arquivo: " . $e->getFile() . "\n";
    echo "📍 Linha: " . $e->getLine() . "\n";
}
