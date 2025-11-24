<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRODUTOS DISPONÍVEIS ===\n\n";

$produtos = DB::table('produtos')
    ->where('tenant_code', 'RESTAURA0003')
    ->where('ativo', true)
    ->orderBy('nome')
    ->get();

echo "Total: {$produtos->count()} produtos\n\n";

foreach ($produtos as $p) {
    echo "• {$p->nome} - R$ {$p->preco}\n";
    if ($p->descricao) {
        echo "  {$p->descricao}\n";
    }
}
