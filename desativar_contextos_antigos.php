<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Desativando contextos antigos...\n\n";

$updated = DB::table('ai_contexts')
    ->whereIn('id', [15, 23, 46])
    ->update(['active' => 0]); // Usar 0 em vez de false

echo "✅ {$updated} contextos desativados (IDs: 15, 23, 46)\n";

// Verificar
$contexts = DB::table('ai_contexts')
    ->whereIn('id', [15, 23, 46])
    ->get(['id', 'key', 'active']);

echo "\nVerificação:\n";
foreach ($contexts as $c) {
    $status = $c->active ? '🟢 ATIVO' : '🔴 INATIVO';
    echo "  {$status} | ID: {$c->id} | Key: {$c->key}\n";
}
