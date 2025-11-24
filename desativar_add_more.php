<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Desativando add_more_context temporariamente\n\n";

// Desativar o contexto
DB::table('ai_contexts')
    ->where('key', 'add_more_context')
    ->update([
        'active' => 0,
        'updated_at' => now()
    ]);

echo "✓ add_more_context desativado (active = 0)\n\n";

echo "Agora teste novamente: 'mostrar restaurantes'\n";
echo "Se funcionar, vamos criar um pattern melhor para add_more_context.\n";
