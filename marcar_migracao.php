<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

echo "=== MARCAR MIGRAÇÃO COMO EXECUTADA ===\n";

// Verificar se as colunas existem
$columns = collect(DB::select("DESCRIBE mesas"));
$columnNames = $columns->pluck('Field')->toArray();

echo "Colunas existentes na tabela mesas:\n";
foreach ($columnNames as $col) {
    echo "- {$col}\n";
}

if (in_array('identificador', $columnNames)) {
    // Marcar migração como executada
    DB::table('migrations')->insert([
        'migration' => '2025_11_13_015509_add_columns_to_mesas_table',
        'batch' => 5
    ]);
    echo "\nMigração marcada como executada!\n";
} else {
    echo "\nColuna identificador não existe. Não vou marcar a migração.\n";
}

echo "Concluído!\n";