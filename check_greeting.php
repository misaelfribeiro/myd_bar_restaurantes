<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$greetings = DB::table('ai_contexts')
    ->where('key', 'like', '%greeting%')
    ->orWhere('key', 'like', '%hello%')
    ->get(['key', 'pattern', 'active', 'confidence_threshold']);

echo "=== CONTEXTOS DE SAUDAÇÃO ===\n\n";
foreach($greetings as $g) {
    echo "Key: {$g->key}\n";
    echo "Active: {$g->active}\n";
    echo "Pattern: {$g->pattern}\n";
    echo "Threshold: {$g->confidence_threshold}\n\n";
}
