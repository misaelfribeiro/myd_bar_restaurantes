<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICANDO PRODUTOS EM DESTAQUE ===\n\n";

$total = DB::table('produtos')->count();
$ativos = DB::table('produtos')->where('ativo', 1)->count();
$destaques = DB::table('produtos')->where('ativo', 1)->where('destaque', 1)->count();

echo "Total de produtos: $total\n";
echo "Produtos ativos: $ativos\n";
echo "Produtos em destaque (ativo + destaque): $destaques\n\n";

if ($destaques > 0) {
    echo "Produtos em destaque:\n";
    $produtos = DB::table('produtos')
        ->where('ativo', 1)
        ->where('destaque', 1)
        ->select('id', 'nome', 'preco', 'tenant_code', 'destaque', 'ativo')
        ->get();
    
    foreach($produtos as $p) {
        echo "- #{$p->id}: {$p->nome} (R$ {$p->preco}) - Tenant: {$p->tenant_code}\n";
    }
} else {
    echo "⚠️ NENHUM produto marcado como destaque!\n";
    echo "Para resolver:\n";
    echo "1. Acesse o sistema administrativo\n";
    echo "2. Vá em Produtos\n";
    echo "3. Marque alguns produtos como 'Destaque'\n";
}

// Verificar se a coluna destaque existe
$columns = DB::select("SHOW COLUMNS FROM produtos LIKE 'destaque'");
if (empty($columns)) {
    echo "\n❌ ERRO: Coluna 'destaque' não existe na tabela produtos!\n";
} else {
    echo "\n✅ Coluna 'destaque' existe\n";
}
