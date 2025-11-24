<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$ctx = DB::table('ai_contexts')->where('key', 'confirm_order')->first();
if ($ctx) {
    echo "✅ Contexto confirm_order encontrado:\n";
    echo "Pattern: $ctx->pattern\n";
    echo "Action: $ctx->action\n";
    echo "Ativo: " . ($ctx->active ? 'Sim' : 'Não') . "\n";
} else {
    echo "❌ Contexto confirm_order NÃO encontrado\n";
}
