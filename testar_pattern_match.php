<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AIContext;

echo "🧪 Testando match direto do pattern\n\n";

$context = AIContext::where('key', 'list_restaurants')->first();

echo "Pattern: {$context->pattern}\n";
echo "Active: " . ($context->active ? 'SIM' : 'NÃO') . "\n\n";

$testCases = [
    'lista restaurantes',
    'mostra restaurantes',
    'lista os restaurantes',
    'ver restaurantes',
];

foreach ($testCases as $test) {
    $matches = $context->matches($test);
    $icon = $matches ? '✅' : '❌';
    echo "{$icon} '{$test}' -> " . ($matches ? 'MATCH' : 'NO MATCH') . "\n";
    
    // Debug do regex
    $pattern = str_replace('*', '.*', $context->pattern);
    echo "    Regex testado: /{$pattern}/i\n";
    $result = @preg_match('/' . $pattern . '/i', $test);
    echo "    preg_match result: {$result}\n\n";
}
