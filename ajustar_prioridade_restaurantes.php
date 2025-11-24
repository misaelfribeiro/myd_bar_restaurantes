<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Ajustando prioridade dos contextos de restaurante\n\n";

// Aumentar confidence do list_restaurants para 0.85 (maior que add_more_context 0.5)
DB::table('ai_contexts')
    ->where('key', 'list_restaurants')
    ->update([
        'confidence_threshold' => 0.85,
        'updated_at' => now()
    ]);

echo "✓ list_restaurants agora tem confidence_threshold: 0.85\n";

// Aumentar confidence do select_restaurant para 0.75
DB::table('ai_contexts')
    ->where('key', 'select_restaurant')
    ->update([
        'confidence_threshold' => 0.75,
        'updated_at' => now()
    ]);

echo "✓ select_restaurant agora tem confidence_threshold: 0.75\n";

// Aumentar confidence do select_restaurant_direct para 0.80
DB::table('ai_contexts')
    ->where('key', 'select_restaurant_direct')
    ->update([
        'confidence_threshold' => 0.80,
        'updated_at' => now()
    ]);

echo "✓ select_restaurant_direct agora tem confidence_threshold: 0.80\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Hierarquia de confidence:\n";
echo "  1. list_restaurants: 0.85 ⬆️\n";
echo "  2. select_restaurant_direct: 0.80 ⬆️\n";
echo "  3. select_restaurant: 0.75 ⬆️\n";
echo "  4. Restaurante (menu): 0.70\n";
echo "  5. add_more_context: 0.50\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "✅ Ajuste concluído! Agora 'mostrar restaurantes' vai acionar list_restaurants.\n";
