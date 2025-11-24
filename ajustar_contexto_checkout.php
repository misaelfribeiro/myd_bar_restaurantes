<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== AJUSTANDO CONTEXTOS DE CHECKOUT ===\n\n";

// Desativar checkout_backend (conflita com confirm_order)
DB::table('ai_contexts')
    ->where('key', 'checkout_backend')
    ->update(['active' => 0]);

echo "✅ checkout_backend DESATIVADO\n";

// Atualizar confirm_order para ter threshold mais baixo (maior prioridade)
DB::table('ai_contexts')
    ->where('key', 'confirm_order')
    ->update(['confidence_threshold' => 0.70]);

echo "✅ confirm_order threshold ajustado para 0.70\n";

echo "\n=== NOVOS VALORES ===\n";
$ctx = DB::table('ai_contexts')->where('key', 'confirm_order')->first();
echo "confirm_order: threshold=$ctx->confidence_threshold, ativo=" . ($ctx->active ? 'SIM' : 'NÃO') . "\n";

$ctx2 = DB::table('ai_contexts')->where('key', 'checkout_backend')->first();
echo "checkout_backend: threshold=$ctx2->confidence_threshold, ativo=" . ($ctx2->active ? 'SIM' : 'NÃO') . "\n";
