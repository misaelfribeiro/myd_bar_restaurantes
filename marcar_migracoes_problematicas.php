<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

echo "=== MARCAR MIGRAÇÕES PROBLEMÁTICAS ===\n";

// Marcar a migração do mesa_id nulo como executada (já foi feita por outra)
DB::table('migrations')->insert([
    'migration' => '2025_11_13_014133_update_pedidos_table_allow_null_mesa_id',
    'batch' => 6
]);
echo "Migração mesa_id nulo marcada como executada!\n";

echo "Concluído!\n";