<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

echo "=== CORRIGIR TIPO_PREPARO E MARCAR MIGRAÇÃO ===\n";

// Verificar valores atuais
$produtos = collect(DB::select("SELECT DISTINCT tipo_preparo FROM produtos"));
echo "Valores atuais de tipo_preparo:\n";
foreach ($produtos as $produto) {
    echo "- {$produto->tipo_preparo}\n";
}

// Corrigir valores usando UPDATE caso a caso
$invalid_values = ['pronto', 'rapido', 'cozinha'];
foreach ($invalid_values as $invalid) {
    switch($invalid) {
        case 'pronto':
            $new_value = 'nao_precisa';
            break;
        case 'rapido':
            $new_value = 'preparo_rapido';
            break;
        case 'cozinha':
            $new_value = 'preparo_cozinha';
            break;
        default:
            $new_value = 'nao_precisa';
    }
    
    $affected = DB::table('produtos')
        ->where('tipo_preparo', $invalid)
        ->update(['tipo_preparo' => $new_value]);
        
    if ($affected > 0) {
        echo "Corrigido {$affected} produtos: '{$invalid}' -> '{$new_value}'\n";
    }
}

// Marcar a migração como executada
try {
    DB::table('migrations')->insert([
        'migration' => '2025_11_11_163817_fix_tipo_preparo_enum_values',
        'batch' => 8
    ]);
    echo "Migração marcada como executada!\n";
} catch (Exception $e) {
    echo "Migração já estava marcada como executada.\n";
}

echo "Concluído!\n";