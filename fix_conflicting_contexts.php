<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DESATIVANDO CONTEXTOS CONFLITANTES ===\n\n";

// Contextos velhos que conflitam
$toDeactivate = [
    'pay_with_card',
    'pay_with_money',
    'pay_with_cash',
    'pay_with_pix',
    'payment_card',
    'payment_money',
    'payment_cash'
];

foreach($toDeactivate as $key) {
    $updated = DB::table('ai_contexts')
        ->where('key', $key)
        ->update(['active' => 0]);
    
    if($updated > 0) {
        echo "✅ Desativado: $key\n";
    }
}

// Verificar se há contextos com patterns similares ativos
echo "\n=== CONTEXTOS ATIVOS COM 'PAGAR' ou 'DINHEIRO' ===\n";
$contexts = DB::table('ai_contexts')
    ->where('active', 1)
    ->where(function($q) {
        $q->where('pattern', 'like', '%pagar%')
          ->orWhere('pattern', 'like', '%dinheiro%')
          ->orWhere('pattern', 'like', '%cartão%')
          ->orWhere('pattern', 'like', '%cartao%');
    })
    ->get(['key', 'pattern', 'confidence_threshold']);

foreach($contexts as $ctx) {
    echo "\nKey: {$ctx->key}\n";
    echo "Pattern: {$ctx->pattern}\n";
    echo "Threshold: {$ctx->confidence_threshold}\n";
}
