<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Desativar search_continuation_yes para evitar conflito
DB::table('ai_contexts')
    ->where('key', 'search_continuation_yes')
    ->update(['active' => 0]);

echo "✅ search_continuation_yes DESATIVADO\n";

// Diminuir threshold do confirm_yes para maior prioridade
DB::table('ai_contexts')
    ->where('key', 'confirm_yes')
    ->update(['confidence_threshold' => 0.60]);

echo "✅ confirm_yes threshold ajustado para 0.60\n";
