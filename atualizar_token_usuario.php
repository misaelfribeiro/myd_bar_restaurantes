<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Atualizar user_id do token de 2 para 23
DB::table('fcm_tokens')->where('user_id', 2)->update(['user_id' => 23]);

echo "\n✅ Token FCM atualizado de user_id 2 para user_id 23\n\n";

// Verificar
$token = DB::table('fcm_tokens')->where('user_id', 23)->first();
if ($token) {
    echo "Token encontrado:\n";
    echo "  User ID: {$token->user_id}\n";
    echo "  Token: " . substr($token->token, 0, 50) . "...\n";
    echo "  Device: {$token->device_type}\n";
    echo "  Ativo: " . ($token->ativo ? 'Sim' : 'Não') . "\n\n";
}
