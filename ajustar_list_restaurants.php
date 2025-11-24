<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Ajustando contexto list_restaurants para maior especificidade\n\n";

// Verificar contexto atual
$context = DB::table('ai_contexts')->where('key', 'list_restaurants')->first();
echo "Pattern atual: {$context->pattern}\n";
echo "Confidence atual: {$context->confidence_threshold}\n\n";

// Atualizar com pattern mais específico e confidence maior
$newPattern = '*(mostra|lista|quais|ver|veja) *(o|os|a|as)* *(restaurante|loja|estabelecimento)*';

DB::table('ai_contexts')
    ->where('key', 'list_restaurants')
    ->update([
        'pattern' => $newPattern,
        'confidence_threshold' => 0.95,
        'updated_at' => now()
    ]);

echo "✓ list_restaurants atualizado\n";
echo "  Novo pattern: {$newPattern}\n";
echo "  Nova confidence: 0.95\n\n";

// Verificar todos os contextos relacionados a restaurante
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Hierarquia de contextos de restaurante:\n\n";

$contexts = DB::table('ai_contexts')
    ->where(function($query) {
        $query->where('pattern', 'like', '%restaurante%')
              ->orWhere('key', 'like', '%restaurant%');
    })
    ->where('active', 1)
    ->orderBy('confidence_threshold', 'desc')
    ->get(['key', 'pattern', 'confidence_threshold', 'action']);

foreach ($contexts as $ctx) {
    echo "  • {$ctx->key} (confidence: {$ctx->confidence_threshold})\n";
    echo "    Action: {$ctx->action}\n";
    echo "    Pattern: " . substr($ctx->pattern, 0, 60) . "...\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Agora 'mostrar restaurantes' deve acionar list_restaurants!\n";
