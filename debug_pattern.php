<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AIContext;

$context = AIContext::where('key', 'greeting_hello')->first();

echo "Pattern: " . $context->pattern . "\n";
echo "Testing: 'oi'\n";
echo "Match: " . ($context->matches('oi') ? 'SIM' : 'NÃO') . "\n\n";

// Teste manual
$pattern = $context->pattern;
$pattern = str_replace('*', '.*', $pattern);
echo "Pattern transformado: $pattern\n";

$regex = '/' . $pattern . '/i';
echo "Regex final: $regex\n";

$match = @preg_match($regex, 'oi');
echo "Resultado: " . ($match === 1 ? 'MATCH!' : 'NO MATCH') . "\n";
