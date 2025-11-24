<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$msg = 'pagar com dinheiro';

echo "Mensagem: '$msg'\n\n";

$contexts = DB::table('ai_contexts')
    ->where('active', 1)
    ->where(function($q) use ($msg) {
        $words = explode(' ', strtolower($msg));
        foreach($words as $word) {
            $q->orWhere('pattern', 'like', "%$word%");
        }
    })
    ->get(['key', 'pattern', 'confidence_threshold']);

echo "Contextos que podem dar match:\n\n";
foreach($contexts as $ctx) {
    echo "Key: {$ctx->key}\n";
    echo "Pattern: {$ctx->pattern}\n";
    echo "Threshold: {$ctx->confidence_threshold}\n\n";
}
