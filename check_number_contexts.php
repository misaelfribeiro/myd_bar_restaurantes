<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICANDO CONFLITOS COM TROCO ===\n\n";

$msg = 'troco para 50';
$contexts = DB::table('ai_contexts')
    ->where('active', 1)
    ->where('pattern', 'like', '%\d%')
    ->get(['key', 'pattern', 'action', 'confidence_threshold']);

echo "Contextos com números no pattern:\n\n";
foreach($contexts as $ctx) {
    echo "Key: {$ctx->key}\n";
    echo "Pattern: {$ctx->pattern}\n";
    echo "Action: {$ctx->action}\n\n";
}
