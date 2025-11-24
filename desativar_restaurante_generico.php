<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Desativando contexto genérico 'Restaurante'\n\n";

// Desativar o contexto genérico que está causando conflito
DB::table('ai_contexts')
    ->where('key', 'Restaurante')
    ->update([
        'active' => 0,
        'updated_at' => now()
    ]);

echo "✓ Contexto 'Restaurante' desativado\n\n";

echo "Agora vamos usar apenas os contextos específicos:\n";
echo "  • list_restaurants (confidence: 0.95) → showRestaurants\n";
echo "  • select_restaurant_direct (confidence: 0.80) → selectRestaurant\n";
echo "  • select_restaurant (confidence: 0.75) → selectRestaurant\n";
echo "\n✅ Teste novamente: 'lista restaurantes'\n";
