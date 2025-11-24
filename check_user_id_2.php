<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFICANDO USER ID 2 ===\n\n";

$user = DB::table('users')->where('id', 2)->first();

if ($user) {
    echo "✅ User ID 2 EXISTE!\n";
    echo "   Nome: {$user->name}\n";
    echo "   Email: {$user->email}\n";
} else {
    echo "❌ User ID 2 NÃO EXISTE\n";
    echo "\n📋 Usuários disponíveis:\n";
    $users = DB::table('users')->select('id', 'name', 'email')->get();
    foreach ($users as $u) {
        echo "   - ID {$u->id}: {$u->name} ({$u->email})\n";
    }
}
