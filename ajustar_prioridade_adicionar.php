<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 Verificando contextos que capturam 'adicionar'\n\n";

$contexts = DB::table('ai_contexts')
    ->where('active', 1)
    ->where(function($query) {
        $query->where('pattern', 'like', '%adiciona%')
              ->orWhere('pattern', 'like', '%esse%')
              ->orWhere('pattern', 'like', '%essa%')
              ->orWhere('key', 'like', '%add%');
    })
    ->orderBy('confidence_threshold', 'desc')
    ->get(['key', 'pattern', 'confidence_threshold', 'action']);

echo "Contextos relacionados a 'adicionar':\n\n";
foreach ($contexts as $ctx) {
    echo "{$ctx->key} (confidence: {$ctx->confidence_threshold})\n";
    echo "  Action: {$ctx->action}\n";
    echo "  Pattern: " . substr($ctx->pattern, 0, 80) . "\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Ajustar prioridades
echo "Ajustando prioridades...\n\n";

// 1. add_to_cart_confirm deve ter prioridade ALTA
DB::table('ai_contexts')
    ->where('key', 'add_to_cart_confirm')
    ->update([
        'pattern' => '.*(adiciona|adicionar|coloca|colocar|quero|pegar|pega).*(esse|essa|este|esta|isso|ai|aí).*',
        'confidence_threshold' => 0.90,
        'active' => 1,
        'updated_at' => now()
    ]);

echo "✓ add_to_cart_confirm: confidence 0.90\n";
echo "  Pattern específico: 'adiciona/coloca/quero + esse/essa'\n\n";

// 2. add_more deve ter prioridade mais baixa
DB::table('ai_contexts')
    ->where('key', 'add_more')
    ->update([
        'confidence_threshold' => 0.50,
        'active' => 1,
        'updated_at' => now()
    ]);

echo "✓ add_more: confidence 0.50\n\n";

// 3. Desativar add_to_cart_generic (muito genérico)
DB::table('ai_contexts')
    ->where('key', 'add_to_cart_generic')
    ->update([
        'active' => 0,
        'updated_at' => now()
    ]);

echo "✓ add_to_cart_generic: desativado\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Hierarquia final:\n";
echo "  1. add_to_cart_confirm (0.90) → addToCart\n";
echo "  2. add_more (0.50) → continueAdding\n";
echo "  ❌ add_to_cart_generic (desativado)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Testar patterns
$tests = [
    'adicionar essa',
    'adiciona esse',
    'quero esse',
    'coloca essa',
    'adicionar mais',
    'e também'
];

echo "Testando patterns:\n";
foreach ($tests as $test) {
    echo "\n'{$test}':\n";
    
    $p1 = '.*(adiciona|adicionar|coloca|colocar|quero|pegar|pega).*(esse|essa|este|esta|isso|ai|aí).*';
    $m1 = preg_match('/' . $p1 . '/i', $test);
    echo "  " . ($m1 ? '✅' : '❌') . " add_to_cart_confirm (0.90)\n";
    
    // Pattern do add_more (exemplo genérico)
    $p2 = '.*(e |também |mais |outro |outra ).*';
    $m2 = preg_match('/' . $p2 . '/i', $test);
    echo "  " . ($m2 ? '✅' : '❌') . " add_more (0.50)\n";
}

echo "\n✅ Ajustes concluídos! Teste: 'adicionar essa'\n";
