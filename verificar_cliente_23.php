<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== VERIFICANDO CLIENTE ID 23 ===\n\n";

$cliente = DB::table('clientes')->where('id', 23)->first();

if ($cliente) {
    echo "✅ Cliente encontrado:\n";
    echo "  ID: {$cliente->id}\n";
    echo "  Nome: {$cliente->nome}\n";
    echo "  Telefone: {$cliente->telefone}\n";
    echo "  Email: " . ($cliente->email ?? 'N/A') . "\n\n";
    
    // Verificar se cliente tem user_id
    if (isset($cliente->user_id) && $cliente->user_id) {
        echo "✅ Cliente tem user_id: {$cliente->user_id}\n";
        
        // Verificar se esse user existe
        $user = DB::table('users')->where('id', $cliente->user_id)->first();
        if ($user) {
            echo "✅ User encontrado: {$user->name} ({$user->email})\n\n";
            
            // Salvar token para esse user_id
            DB::table('fcm_tokens')->where('user_id', 2)->delete();
            DB::table('fcm_tokens')->insert([
                'user_id' => $cliente->user_id,
                'token' => 'czN2MULAQCmwKqSoCvuE_B:APA91bETP48PSgeHkM8lt4fvWZhIXxewGYs4-2rDDQ_YtxYtnwnrfMw-nxEy8ktK72NXYWk1xVqHJU8bTHss5qUYdvRI7OgPnu1-Qkpbmztg5L0XFq4rjw8',
                'device_type' => 'android',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✅ Token FCM salvo para user_id = {$cliente->user_id}\n\n";
        } else {
            echo "❌ User ID {$cliente->user_id} não existe!\n\n";
        }
    } else {
        echo "❌ Cliente não tem user_id associado!\n";
        echo "O app precisa criar um usuário primeiro ou associar o cliente a um user existente.\n\n";
    }
} else {
    echo "❌ Cliente com ID 23 não existe!\n\n";
}
