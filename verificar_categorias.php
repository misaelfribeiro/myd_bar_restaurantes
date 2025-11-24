<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tenant = 'RESTAURANTE0001';

echo "Categorias do tenant {$tenant}:\n\n";
$categorias = \App\Models\Categoria::where('tenant_code', $tenant)->get(['id', 'nome']);

foreach($categorias as $cat) {
    $count = \App\Models\Produto::where('categoria_id', $cat->id)
        ->where('ativo', true)
        ->count();
    echo "  {$cat->id}: {$cat->nome} ({$count} produtos)\n";
}

echo "\nProdutos com 'cerveja' no nome:\n";
$produtos = \App\Models\Produto::where('tenant_code', $tenant)
    ->where('nome', 'LIKE', '%cerveja%')
    ->get(['id', 'nome', 'categoria_id', 'preco']);
    
foreach($produtos as $p) {
    $cat = \App\Models\Categoria::find($p->categoria_id);
    echo "  {$p->nome} - Cat: {$cat->nome} - R$ {$p->preco}\n";
}
