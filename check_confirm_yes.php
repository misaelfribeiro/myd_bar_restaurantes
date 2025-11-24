<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$ctx = DB::table('ai_contexts')->where('key', 'confirm_yes')->first();
if ($ctx) {
    echo "confirm_yes:\n";
    echo "  Action: $ctx->action\n";
    echo "  Pattern: $ctx->pattern\n";
    echo "  Threshold: $ctx->confidence_threshold\n";
}
