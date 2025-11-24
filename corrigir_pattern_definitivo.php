<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Corrigindo pattern para formato mais robusto\n\n";

// Pattern sem asteriscos duplos
$newPattern = '.*(mostra|lista|ver|veja|quais).*(restaurante|loja).*';

DB::table('ai_contexts')
    ->where('key', 'list_restaurants')
    ->update([
        'pattern' => $newPattern,
        'active' => 1, // Garantir que está ativo
        'updated_at' => now()
    ]);

echo "✓ Pattern atualizado para: {$newPattern}\n\n";

// Testar manualmente
$testCases = ['lista restaurantes', 'mostra restaurantes', 'ver restaurantes', 'quais restaurantes'];

echo "Testando regex:\n";
foreach ($testCases as $test) {
    $matches = @preg_match('/' . $newPattern . '/i', $test) === 1;
    $icon = $matches ? '✅' : '❌';
    echo "  {$icon} '{$test}'\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Pattern corrigido!\n";
