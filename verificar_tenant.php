<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "Tenants disponíveis:\n";
$empresas = \App\Models\Empresa::select('id','tenant_code','nome_fantasia')->take(5)->get();
foreach($empresas as $e) {
    echo "  {$e->tenant_code} - {$e->nome_fantasia}\n";
}

echo "\nProdutos do primeiro tenant:\n";
$primeiro = $empresas->first();
if ($primeiro) {
    $produtos = \App\Models\Produto::where('tenant_code', $primeiro->tenant_code)
        ->where('ativo', true)
        ->take(3)
        ->get();
    
    foreach($produtos as $p) {
        echo "  {$p->nome} - R$ {$p->preco}\n";
    }
}
