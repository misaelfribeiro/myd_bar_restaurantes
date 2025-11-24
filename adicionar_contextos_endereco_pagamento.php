<?php
/**
 * Adiciona contextos de confirmação de endereço e seleção de pagamento
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ADICIONANDO CONTEXTOS DE ENDEREÇO E PAGAMENTO ===\n\n";

// Contextos a serem criados
$contextos = [
    [
        'key' => 'confirm_address',
        'pattern' => '*(confirma|confere|verifica|checa|ver|mostra|qual|como)*(endereço|endereco|local|localizaçao|localizacao|entrega)*',
        'response_template' => 'Vou verificar seu endereço de entrega!',
        'action' => 'confirmAddress',
        'confidence_threshold' => 0.70,
        'active' => true,
        'category' => 'checkout',
        'description' => 'Confirma endereço de entrega'
    ],
    [
        'key' => 'change_address',
        'pattern' => '*(mudar|alterar|trocar|modificar|atualizar|corrigir)*(endereço|endereco|local|localizaçao|localizacao)*',
        'response_template' => 'Vou abrir a tela para você alterar o endereço!',
        'action' => 'changeAddress',
        'confidence_threshold' => 0.70,
        'active' => true,
        'category' => 'checkout',
        'description' => 'Altera endereço de entrega'
    ],
    [
        'key' => 'show_payment_methods',
        'pattern' => '*(mostra|ver|quais|exibe|lista)*(forma|formas|opção|opções|opcao|opcoes|método|metodo|metodos|métodos)*(pagamento|pagar)*',
        'response_template' => 'Vou mostrar as formas de pagamento disponíveis!',
        'action' => 'showPaymentMethods',
        'confidence_threshold' => 0.70,
        'active' => true,
        'category' => 'checkout',
        'description' => 'Mostra formas de pagamento'
    ],
    [
        'key' => 'select_payment_money',
        'pattern' => '*(pagar|pagamento|quero pagar)*(dinheiro|espécie|especie|cash)*',
        'response_template' => 'Pagamento em dinheiro selecionado!',
        'action' => 'selectPayment',
        'confidence_threshold' => 0.75,
        'active' => true,
        'category' => 'checkout',
        'parameters' => json_encode(['payment_method' => 'money']),
        'description' => 'Seleciona pagamento em dinheiro'
    ],
    [
        'key' => 'select_payment_card',
        'pattern' => '*(pagar|pagamento|quero pagar)*(cartão|cartao|card|débito|debito|crédito|credito)*',
        'response_template' => 'Pagamento no cartão selecionado!',
        'action' => 'selectPayment',
        'confidence_threshold' => 0.75,
        'active' => true,
        'category' => 'checkout',
        'parameters' => json_encode(['payment_method' => 'card']),
        'description' => 'Seleciona pagamento no cartão'
    ],
    [
        'key' => 'select_payment_pix',
        'pattern' => '*(pagar|pagamento|quero pagar)*(pix)*',
        'response_template' => 'Pagamento via PIX selecionado!',
        'action' => 'selectPayment',
        'confidence_threshold' => 0.75,
        'active' => true,
        'category' => 'checkout',
        'parameters' => json_encode(['payment_method' => 'pix']),
        'description' => 'Seleciona pagamento via PIX'
    ]
];

foreach ($contextos as $contexto) {
    // Verificar se já existe
    $existing = DB::table('ai_contexts')
        ->where('key', $contexto['key'])
        ->first();
    
    if ($existing) {
        // Atualizar
        DB::table('ai_contexts')
            ->where('key', $contexto['key'])
            ->update([
                'pattern' => $contexto['pattern'],
                'response_template' => $contexto['response_template'],
                'action' => $contexto['action'],
                'confidence_threshold' => $contexto['confidence_threshold'],
                'active' => $contexto['active'],
                'category' => $contexto['category'],
                'parameters' => $contexto['parameters'] ?? null,
                'updated_at' => now()
            ]);
        echo "✅ Atualizado: {$contexto['key']}\n";
    } else {
        // Criar novo
        DB::table('ai_contexts')->insert([
            'key' => $contexto['key'],
            'pattern' => $contexto['pattern'],
            'response_template' => $contexto['response_template'],
            'action' => $contexto['action'],
            'confidence_threshold' => $contexto['confidence_threshold'],
            'active' => $contexto['active'],
            'category' => $contexto['category'],
            'parameters' => $contexto['parameters'] ?? null,
            'usage_count' => 0,
            'success_rate' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "✅ Criado: {$contexto['key']}\n";
    }
}

echo "\n✅ Contextos de endereço e pagamento configurados!\n";
echo "\nComandos disponíveis:\n";
echo "  - 'confirma meu endereço' / 'qual meu endereço'\n";
echo "  - 'mudar endereço' / 'alterar endereço'\n";
echo "  - 'mostra formas de pagamento' / 'quais as opções de pagamento'\n";
echo "  - 'pagar em dinheiro' / 'pagamento em dinheiro'\n";
echo "  - 'pagar no cartão' / 'pagamento no cartão'\n";
echo "  - 'pagar via pix' / 'pagamento via pix'\n";
