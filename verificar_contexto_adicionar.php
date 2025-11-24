<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICANDO CONTEXTO ADD_PRODUCT_TO_CART ===\n\n";

$ctx = DB::table('ai_contexts')->where('key', 'add_product_to_cart')->first();

if (!$ctx) {
    echo "❌ Contexto não encontrado\n";
    exit;
}

echo "✓ Contexto encontrado\n";
echo "Ativo: " . ($ctx->active ? 'SIM' : 'NAO') . "\n";
echo "Pattern: {$ctx->pattern}\n";
echo "Action: {$ctx->action}\n";
echo "Confidence: {$ctx->confidence_threshold}\n";
echo "\n";

// Testar match
$testMessages = [
    'quero essa',
    'quero esse',
    'adiciona essa',
    'pega esse',
    'coloca esse no carrinho'
];

echo "--- TESTES DE MATCH ---\n\n";

$pattern = str_replace('*', '.*', $ctx->pattern);
$pattern = '/^' . $pattern . '$/i';

echo "Pattern regex: {$pattern}\n\n";

foreach ($testMessages as $msg) {
    $match = preg_match($pattern, $msg);
    $status = $match ? '✓' : '✗';
    echo "{$status} \"{$msg}\"\n";
}
