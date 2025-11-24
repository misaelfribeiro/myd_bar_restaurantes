<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$contexts = $app->make('db')->table('ai_contexts')
    ->where('active', 1)
    ->where('pattern', 'like', '%lista%')
    ->orWhere('pattern', 'like', '%restaurante%')
    ->get(['key', 'pattern', 'confidence_threshold', 'action', 'active']);

foreach ($contexts as $ctx) {
    echo "{$ctx->key} | confidence:{$ctx->confidence_threshold} | active:{$ctx->active}\n";
    echo "  Pattern: {$ctx->pattern}\n";
    echo "  Action: {$ctx->action}\n\n";
}
