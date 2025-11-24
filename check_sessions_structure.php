<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ESTRUTURA ai_conversation_sessions ===\n";
$cols = DB::select('SHOW COLUMNS FROM ai_conversation_sessions');
foreach($cols as $col) {
    echo "{$col->Field} ({$col->Type}) - Null: {$col->Null} - Key: {$col->Key}\n";
}

echo "\n=== FOREIGN KEYS ===\n";
$fks = DB::select("
    SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ai_conversation_sessions'
    AND REFERENCED_TABLE_NAME IS NOT NULL
");
foreach($fks as $fk) {
    echo "{$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
}
