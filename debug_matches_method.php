<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AIContext;

echo "🔍 Debugando método matches() do AIContext\n\n";

$context = AIContext::where('key', 'add_to_cart_confirm')->first();

echo "Pattern original: {$context->pattern}\n\n";

$input = 'quero esse';
echo "Input: '{$input}'\n\n";

// Simular o método matches() passo a passo
$pattern = $context->pattern;
echo "1. Pattern inicial: {$pattern}\n";

$pattern = str_replace('*', '.*', $pattern);
echo "2. Após substituir * por .*: {$pattern}\n";

$hasRegex = preg_match('/[\(\)\|]/', $pattern);
echo "3. Tem regex especial? " . ($hasRegex ? 'SIM' : 'NÃO') . "\n";

if (!$hasRegex) {
    echo "4. Aplicando preg_quote...\n";
    $pattern = preg_quote($pattern, '/');
    echo "   Após preg_quote: {$pattern}\n";
    $pattern = str_replace('\.\*', '.*', $pattern);
    echo "   Após restaurar .*: {$pattern}\n";
}

echo "\n5. Regex final: /{$pattern}/i\n\n";

$match = @preg_match('/' . $pattern . '/i', $input);
echo "6. Match? " . ($match ? 'SIM ✅' : 'NÃO ❌') . "\n\n";

// Testar usando o método do modelo
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Testando método matches() do modelo:\n";
$result = $context->matches($input);
echo "Resultado: " . ($result ? 'MATCH ✅' : 'NO MATCH ❌') . "\n";
