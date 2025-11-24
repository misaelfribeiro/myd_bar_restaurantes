<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

echo "=== CORRIGIR TIPO PREPARO ===\n";

// Verificar valores atuais
$produtos = DB::table('produtos')->get();
echo "Total de produtos: " . $produtos->count() . "\n";

foreach ($produtos as $produto) {
    if (!in_array($produto->tipo_preparo, ['nao_precisa', 'preparo_rapido', 'preparo_cozinha'])) {
        echo "Produto ID {$produto->id}: '{$produto->tipo_preparo}' -> 'nao_precisa'\n";
        DB::table('produtos')->where('id', $produto->id)->update(['tipo_preparo' => 'nao_precisa']);
    }
}

echo "Correção concluída!\n";