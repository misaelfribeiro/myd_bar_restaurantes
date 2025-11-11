<?php
require_once 'bootstrap/app.php';

// Bootstrap Laravel
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Buscar uma categoria
$categoria = App\Models\Categoria::first();

if ($categoria) {
    // Verificar se já existe um produto com esse nome
    $existente = App\Models\Produto::where('nome', 'Hambúrguer Artesanal')->first();
    
    if (!$existente) {
        // Criar produto de teste
        $produto = App\Models\Produto::create([
            'nome' => 'Hambúrguer Artesanal',
            'descricao' => 'Delicioso hambúrguer com pão brioche, carne bovina 180g, queijo cheddar, alface americana, tomate, cebola roxa e molho especial da casa. Acompanha batata rústica.',
            'preco' => 28.90,
            'categoria_id' => $categoria->id,
            'ativo' => true
        ]);
        
        echo "✅ Produto criado com sucesso!\n";
        echo "ID: {$produto->id}\n";
        echo "Nome: {$produto->nome}\n";
        echo "Preço: R$ " . number_format($produto->preco, 2, ',', '.') . "\n";
        echo "Categoria: {$categoria->nome}\n";
        echo "Status: " . ($produto->ativo ? 'Ativo' : 'Inativo') . "\n\n";
        echo "🔗 URL para visualizar: http://localhost:8000/produtos/{$produto->id}\n";
    } else {
        echo "⚠️ Produto 'Hambúrguer Artesanal' já existe!\n";
        echo "ID: {$existente->id}\n";
        echo "🔗 URL para visualizar: http://localhost:8000/produtos/{$existente->id}\n";
    }
} else {
    echo "❌ Erro: Nenhuma categoria encontrada!\n";
    echo "Execute primeiro: php artisan db:seed --class=CategoriaSeeder\n";
}

// Listar todos os produtos existentes
echo "\n📋 Produtos cadastrados:\n";
$produtos = App\Models\Produto::with('categoria')->get();

if ($produtos->count() > 0) {
    foreach ($produtos as $p) {
        echo "- #{$p->id}: {$p->nome} (R$ " . number_format($p->preco, 2, ',', '.') . ") - {$p->categoria->nome}\n";
    }
} else {
    echo "Nenhum produto cadastrado.\n";
}
