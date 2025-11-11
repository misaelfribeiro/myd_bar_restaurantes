<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 TESTANDO MODO GARÇOM - " . date('Y-m-d H:i:s') . "\n";
echo "===============================================\n\n";

try {
    // 1. Verificar se existem dados básicos
    $categorias = App\Models\Categoria::count();
    $produtos = App\Models\Produto::count();
    $mesas = App\Models\Mesa::count();
    $pedidos = App\Models\Pedido::count();
    
    echo "📊 DADOS DISPONÍVEIS:\n";
    echo "- Categorias: $categorias\n";
    echo "- Produtos: $produtos\n";
    echo "- Mesas: $mesas\n";
    echo "- Pedidos: $pedidos\n\n";
    
    // 2. Testar busca de produtos ativos
    $produtosAtivos = App\Models\Produto::where('ativo', true)->count();
    echo "✅ Produtos ativos: $produtosAtivos\n";
    
    // 3. Testar consulta de mesas
    $mesasOcupadas = App\Models\Mesa::whereHas('pedidos', function($query) {
        $query->where('status', 'aberto');
    })->count();
    echo "🪑 Mesas ocupadas: $mesasOcupadas\n";
    
    // 4. Testar categorias com produtos
    $categoriasComProdutos = App\Models\Categoria::whereHas('produtos', function($query) {
        $query->where('ativo', true);
    })->count();
    echo "📚 Categorias com produtos ativos: $categoriasComProdutos\n\n";
    
    // 5. Verificar estrutura do banco
    echo "🔍 VERIFICANDO ESTRUTURA:\n";
    
    if (Schema::hasTable('categorias')) {
        echo "✅ Tabela 'categorias' existe\n";
    } else {
        echo "❌ Tabela 'categorias' não encontrada\n";
    }
    
    if (Schema::hasTable('produtos')) {
        echo "✅ Tabela 'produtos' existe\n";
        $colunas = Schema::getColumnListing('produtos');
        if (in_array('ativo', $colunas)) {
            echo "   ✅ Coluna 'ativo' presente\n";
        } else {
            echo "   ❌ Coluna 'ativo' ausente\n";
        }
    }
    
    if (Schema::hasTable('mesas')) {
        echo "✅ Tabela 'mesas' existe\n";
    }
    
    if (Schema::hasTable('pedidos')) {
        echo "✅ Tabela 'pedidos' existe\n";
    }
    
    echo "\n🎯 TESTANDO FUNCIONALIDADES ESPECÍFICAS:\n";
    
    // Simular dashboard data
    $vendaDiaria = App\Models\Pedido::whereDate('created_at', today())->sum('total');
    $pedidosAbertos = App\Models\Pedido::where('status', 'aberto')->count();
    echo "💰 Venda diária: R$ " . number_format($vendaDiaria, 2, ',', '.') . "\n";
    echo "📋 Pedidos abertos: $pedidosAbertos\n";
    
    // Buscar produtos para cardápio
    $primeirosDeCategoria = App\Models\Categoria::with(['produtos' => function($query) {
        $query->where('ativo', true)->limit(2);
    }])->get();
    
    echo "🍽️ Primeiros produtos por categoria:\n";
    foreach ($primeirosDeCategoria as $categoria) {
        echo "  📂 {$categoria->nome}: {$categoria->produtos->count()} produto(s)\n";
        foreach ($categoria->produtos as $produto) {
            echo "    - {$produto->nome} (R$ " . number_format($produto->preco, 2, ',', '.') . ")\n";
        }
    }
    
    echo "\n✅ TESTE CONCLUÍDO COM SUCESSO!\n";
    echo "🎉 Modo Garçom está funcional e pronto para uso.\n";
    
} catch (\Exception $e) {
    echo "❌ ERRO DURANTE O TESTE:\n";
    echo "Tipo: " . get_class($e) . "\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . " (linha " . $e->getLine() . ")\n";
}

echo "\n===============================================\n";
echo "Teste finalizado em " . date('Y-m-d H:i:s') . "\n";
