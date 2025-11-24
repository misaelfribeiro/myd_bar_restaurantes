<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CONTEXTOS DE CHECKOUT E CONFIRM ===\n\n";

$checkoutCtx = DB::table('ai_contexts')->where('action', 'checkoutApp')->first();
if ($checkoutCtx) {
    echo "checkoutApp:\n";
    echo "  Key: $checkoutCtx->key\n";
    echo "  Pattern: $checkoutCtx->pattern\n";
    echo "  Threshold: $checkoutCtx->confidence_threshold\n\n";
}

$confirmCtx = DB::table('ai_contexts')->where('action', 'confirmOrder')->first();
if ($confirmCtx) {
    echo "confirmOrder:\n";
    echo "  Key: $confirmCtx->key\n";
    echo "  Pattern: $confirmCtx->pattern\n";
    echo "  Threshold: $confirmCtx->confidence_threshold\n\n";
} else {
    echo "❌ confirmOrder NÃO ENCONTRADO\n";
}
