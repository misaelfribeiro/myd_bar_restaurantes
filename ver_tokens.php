<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tokens = DB::table('fcm_tokens')->get();

echo "\n=== TOKENS FCM SALVOS ===\n\n";

foreach($tokens as $token) {
    echo "ID: {$token->id}\n";
    echo "User ID: {$token->user_id}\n";
    echo "Token: " . substr($token->token, 0, 50) . "...\n";
    echo "Device: {$token->device_type}\n";
    echo "Ativo: " . ($token->ativo ? 'Sim' : 'Não') . "\n";
    echo "---\n";
}

echo "\nTotal: " . count($tokens) . " token(s)\n\n";
