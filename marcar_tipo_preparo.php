<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

echo "=== MARCAR MIGRAÇÃO TIPO_PREPARO ===\n";

// Marcar a migração como executada
try {
    DB::table('migrations')->insert([
        'migration' => '2025_11_11_163817_fix_tipo_preparo_enum_values',
        'batch' => 8
    ]);
    echo "Migração marcada como executada!\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "Concluído!\n";