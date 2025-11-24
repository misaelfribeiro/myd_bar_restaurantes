<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICANDO CHECKOUT_BACKEND ===\n\n";

$ctx = DB::table('ai_contexts')->where('key', 'checkout_backend')->first();

if (!$ctx) {
    echo "❌ Contexto não encontrado!\n";
    exit;
}

echo "✓ Encontrado\n";
echo "Ativo: " . ($ctx->active ? 'SIM' : 'NAO') . "\n";
echo "Pattern: {$ctx->pattern}\n";
echo "Action: {$ctx->action}\n";
echo "Threshold: {$ctx->confidence_threshold}\n\n";

// Testar match
$pattern = str_replace('*', '.*', $ctx->pattern);
$pattern = '/^' . $pattern . '$/i';

echo "Pattern regex: {$pattern}\n\n";

$tests = ['finalizar pedido', 'finalizar', 'concluir pedido', 'fechar pedido'];

foreach ($tests as $msg) {
    $match = preg_match($pattern, $msg);
    echo ($match ? '✓' : '✗') . " \"{$msg}\"\n";
}
