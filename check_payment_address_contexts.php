<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$contexts = DB::table('ai_contexts')
    ->where(function($q) {
        $q->where('key', 'like', '%address%')
          ->orWhere('key', 'like', '%payment%');
    })
    ->get(['id', 'key', 'action', 'active']);

echo "Contextos relacionados a endereço e pagamento:\n\n";
foreach ($contexts as $c) {
    $status = $c->active ? '🟢 ATIVO' : '🔴 INATIVO';
    echo "{$status} | ID: {$c->id} | Key: {$c->key} | Action: {$c->action}\n";
}
