<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ATUALIZANDO CONTEXTOS DE PAGAMENTO ===\n\n";

// 1. Desativar o select_payment_card genérico
DB::table('ai_contexts')
    ->where('key', 'select_payment_card')
    ->update(['active' => 0]);
echo "✅ select_payment_card genérico desativado\n";

// 2. Criar contextos específicos para crédito e débito
$contexts = [
    [
        'category' => 'checkout',
        'key' => 'select_payment_credit',
        'pattern' => '*(pagar|pagamento|quero pagar)*(cartão de crédito|cartao de credito|crédito|credito|credit)*',
        'response_template' => 'Pagamento no cartão de crédito selecionado!',
        'parameters' => json_encode(['payment_method' => 'cartao_credito']),
        'action' => 'selectPayment',
        'confidence_threshold' => 0.75,
        'active' => 1
    ],
    [
        'category' => 'checkout',
        'key' => 'select_payment_debit',
        'pattern' => '*(pagar|pagamento|quero pagar)*(cartão de débito|cartao de debito|débito|debito|debit)*',
        'response_template' => 'Pagamento no cartão de débito selecionado!',
        'parameters' => json_encode(['payment_method' => 'cartao_debito']),
        'action' => 'selectPayment',
        'confidence_threshold' => 0.75,
        'active' => 1
    ]
];

foreach($contexts as $ctx) {
    $existing = DB::table('ai_contexts')->where('key', $ctx['key'])->first();
    if($existing) {
        DB::table('ai_contexts')->where('key', $ctx['key'])->update($ctx);
        echo "✅ {$ctx['key']} atualizado\n";
    } else {
        $ctx['created_at'] = now();
        $ctx['updated_at'] = now();
        DB::table('ai_contexts')->insert($ctx);
        echo "✅ {$ctx['key']} criado\n";
    }
}

// 3. Atualizar select_payment_money para incluir handler de troco
DB::table('ai_contexts')
    ->where('key', 'select_payment_money')
    ->update([
        'parameters' => json_encode(['payment_method' => 'dinheiro', 'needs_change' => true]),
        'response_template' => 'Pagamento em dinheiro selecionado! Precisa de troco para quanto?'
    ]);
echo "✅ select_payment_money atualizado com troco\n";

// 4. Criar contexto para informar troco
$trocoCtx = [
    'category' => 'checkout',
    'key' => 'inform_change_amount',
    'pattern' => '*(troco|preciso de troco)*(para|pra)*(\\d+)*',
    'response_template' => 'Troco para {amount} anotado!',
    'parameters' => json_encode(['action' => 'set_change']),
    'action' => 'setChangeAmount',
    'confidence_threshold' => 0.7,
    'active' => 1,
    'created_at' => now(),
    'updated_at' => now()
];

$existingTroco = DB::table('ai_contexts')->where('key', 'inform_change_amount')->first();
if($existingTroco) {
    DB::table('ai_contexts')->where('key', 'inform_change_amount')->update($trocoCtx);
    echo "✅ inform_change_amount atualizado\n";
} else {
    DB::table('ai_contexts')->insert($trocoCtx);
    echo "✅ inform_change_amount criado\n";
}

echo "\n=== CONTEXTOS DE PAGAMENTO ===";

// Ver colunas
$cols = DB::select('SHOW COLUMNS FROM ai_contexts');
echo "Colunas: " . implode(', ', array_map(fn($c) => $c->Field, $cols)) . "\n\n";

$payments = DB::table('ai_contexts')
    ->where('key', 'like', '%payment%')
    ->get();

foreach($payments as $p) {
    echo "Key: {$p->key}\n";
    print_r($p);
    echo "---\n\n";
}
