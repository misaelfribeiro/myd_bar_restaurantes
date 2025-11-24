<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Simplificando pattern do list_restaurants\n\n";

// Pattern mais simples e direto que vai funcionar
$newPattern = '*(mostra|lista|ver|veja|quais)*(restaurante|loja)*';

DB::table('ai_contexts')
    ->where('key', 'list_restaurants')
    ->update([
        'pattern' => $newPattern,
        'updated_at' => now()
    ]);

echo "✓ Pattern atualizado para: {$newPattern}\n\n";

// Testar o pattern manualmente
$context = DB::table('ai_contexts')->where('key', 'list_restaurants')->first();

echo "Testando matches:\n";
$testCases = [
    'lista restaurantes',
    'mostra restaurantes',
    'mostra os restaurantes',
    'ver restaurantes',
    'quais restaurantes',
    'restaurante' // Este NÃO deve bater
];

foreach ($testCases as $test) {
    $pattern = str_replace('*', '.*', $context->pattern);
    $matches = @preg_match('/' . $pattern . '/i', $test) === 1;
    $icon = $matches ? '✅' : '❌';
    echo "  {$icon} '{$test}'\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Pattern simplificado e testado!\n";
