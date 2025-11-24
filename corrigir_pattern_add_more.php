<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Corrigindo pattern do add_more_context\n\n";

// Verificar pattern atual
$context = DB::table('ai_contexts')
    ->where('key', 'add_more_context')
    ->first();

echo "Pattern atual:\n";
echo "  {$context->pattern}\n\n";

// Atualizar para pattern mais específico que só pega palavras isoladas
$newPattern = '*(^| )(e|também|mais|outro|outra)( |$)*';

DB::table('ai_contexts')
    ->where('key', 'add_more_context')
    ->update([
        'pattern' => $newPattern,
        'updated_at' => now()
    ]);

echo "Novo pattern:\n";
echo "  {$newPattern}\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Agora o pattern só pega palavras isoladas:\n";
echo "  ✅ 'e' (sozinho)\n";
echo "  ✅ 'mais' (sozinho)\n";
echo "  ✅ 'também' (sozinho)\n";
echo "  ❌ 'mostrar' (contém 'mais' mas não isolado)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "✅ Pattern corrigido!\n";
