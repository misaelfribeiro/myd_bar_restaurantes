<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TOKENS RECENTES ===\n\n";

$tokens = DB::table('fcm_tokens')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($tokens as $t) {
    $userId = $t->user_id ?? 'NULL';
    $ativo = $t->ativo ? 'SIM' : 'NAO';
    echo "ID: {$t->id} | User: {$userId} | Ativo: {$ativo} | Token: " . substr($t->token, 0, 30) . "...\n";
}
