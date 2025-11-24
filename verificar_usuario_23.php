<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== VERIFICANDO USUÁRIO ID 23 ===\n\n";

$user = DB::table('users')->where('id', 23)->first();

if ($user) {
    echo "✅ Usuário encontrado:\n";
    echo "  ID: {$user->id}\n";
    echo "  Nome: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Role: " . ($user->role ?? 'N/A') . "\n\n";
    
    // Deletar token antigo e criar novo
    DB::table('fcm_tokens')->where('user_id', 2)->delete();
    DB::table('fcm_tokens')->insert([
        'user_id' => 23,
        'token' => 'czN2MULAQCmwKqSoCvuE_B:APA91bETP48PSgeHkM8lt4fvWZhIXxewGYs4-2rDDQ_YtxYtnwnrfMw-nxEy8ktK72NXYWk1xVqHJU8bTHss5qUYdvRI7OgPnu1-Qkpbmztg5L0XFq4rjw8',
        'device_type' => 'android',
        'ativo' => true,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "✅ Token FCM salvo para user_id = 23\n\n";
} else {
    echo "❌ Usuário com ID 23 não existe!\n\n";
    echo "Usuários cadastrados:\n";
    $users = DB::table('users')->select('id', 'name', 'email')->get();
    foreach($users as $u) {
        echo "  ID: {$u->id} | Nome: {$u->name} | Email: {$u->email}\n";
    }
    echo "\n";
}
