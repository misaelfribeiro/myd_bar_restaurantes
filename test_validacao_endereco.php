<?php

/**
 * TESTE: Validação de Endereço Aprimorada
 * 
 * Este teste verifica se a validação de endereço rejeita corretamente:
 * 1. Endereços vazios
 * 2. Endereços muito curtos (menos de 5 caracteres)
 * 3. Bairros muito curtos (menos de 3 caracteres)
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "==========================================\n";
echo "TESTE: VALIDAÇÃO DE ENDEREÇO APRIMORADA\n";
echo "==========================================\n\n";

// Função auxiliar para fazer requisições
function makeRequest($method, $uri, $data = [], $token = null) {
    global $kernel;
    
    $request = Illuminate\Http\Request::create(
        $uri, 
        $method, 
        [], // parameters
        [], // cookies
        [], // files
        [], // server
        json_encode($data) // content
    );
    
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('Content-Type', 'application/json');
    
    if ($token) {
        $request->headers->set('Authorization', 'Bearer ' . $token);
    }
    
    $response = $kernel->handle($request);
    
    return [
        'status' => $response->getStatusCode(),
        'body' => json_decode($response->getContent(), true)
    ];
}

// ETAPA 1: Login
echo "📱 ETAPA 1: Fazendo login/registro...\n";
$loginResponse = makeRequest('POST', '/api/app/auth/login', [
    'telefone' => '11999990001',
    'nome' => 'Cliente Teste Validação'
]);

if (($loginResponse['status'] === 200 || $loginResponse['status'] === 201) && isset($loginResponse['body']['token'])) {
    $token = $loginResponse['body']['token'];
    echo "✓ Login realizado com sucesso\n";
    echo "  Token: " . substr($token, 0, 20) . "...\n\n";
} else {
    echo "✗ Erro no login\n";
    print_r($loginResponse);
    exit(1);
}

// ETAPA 2: Testar criação de pedido SEM endereço
echo "🚫 ETAPA 2: Tentando criar pedido SEM endereço (deve falhar)...\n";
$pedidoSemEndereco = makeRequest('POST', '/api/app/pedidos', [
    'tipo_pedido' => 'delivery',
    'cliente_nome' => 'Cliente Teste',
    'cliente_telefone' => '11999990001',
    'cliente_endereco' => '',  // VAZIO - deve ser rejeitado
    'cliente_bairro' => 'Centro',
    'cliente_cidade' => 'São Paulo',
    'itens' => [
        [
            'produto_id' => 1,
            'quantidade' => 1,
            'preco_unitario' => 3.00
        ]
    ]
], $token);

if ($pedidoSemEndereco['status'] === 422 || $pedidoSemEndereco['status'] === 400) {
    echo "✓ Pedido rejeitado corretamente (Status: {$pedidoSemEndereco['status']})\n";
    if (isset($pedidoSemEndereco['body']['errors'])) {
        echo "  Erros: " . json_encode($pedidoSemEndereco['body']['errors'], JSON_UNESCAPED_UNICODE) . "\n\n";
    } elseif (isset($pedidoSemEndereco['body']['message'])) {
        echo "  Mensagem: {$pedidoSemEndereco['body']['message']}\n\n";
    }
} else {
    echo "✗ FALHOU: Pedido foi aceito, mas não deveria!\n";
    echo "  Status: {$pedidoSemEndereco['status']}\n";
    print_r($pedidoSemEndereco['body']);
    echo "\n";
}

// ETAPA 3: Testar criação de pedido com endereço MUITO CURTO
echo "🚫 ETAPA 3: Tentando criar pedido com endereço muito curto (deve falhar)...\n";
$pedidoEndCurto = makeRequest('POST', '/api/app/pedidos', [
    'tipo_pedido' => 'delivery',
    'cliente_nome' => 'Cliente Teste',
    'cliente_telefone' => '11999990001',
    'cliente_endereco' => 'Rua',  // Apenas 3 caracteres - deve ser rejeitado (min: 5)
    'cliente_bairro' => 'Centro',
    'cliente_cidade' => 'São Paulo',
    'itens' => [
        [
            'produto_id' => 1,
            'quantidade' => 1,
            'preco_unitario' => 3.00
        ]
    ]
], $token);

if ($pedidoEndCurto['status'] === 422 || $pedidoEndCurto['status'] === 400) {
    echo "✓ Pedido rejeitado corretamente (Status: {$pedidoEndCurto['status']})\n";
    if (isset($pedidoEndCurto['body']['errors'])) {
        echo "  Erros: " . json_encode($pedidoEndCurto['body']['errors'], JSON_UNESCAPED_UNICODE) . "\n\n";
    }
} else {
    echo "✗ FALHOU: Pedido foi aceito, mas não deveria!\n";
    echo "  Status: {$pedidoEndCurto['status']}\n\n";
}

// ETAPA 4: Testar criação de pedido com bairro MUITO CURTO
echo "🚫 ETAPA 4: Tentando criar pedido com bairro muito curto (deve falhar)...\n";
$pedidoBairroCurto = makeRequest('POST', '/api/app/pedidos', [
    'tipo_pedido' => 'delivery',
    'cliente_nome' => 'Cliente Teste',
    'cliente_telefone' => '11999990001',
    'cliente_endereco' => 'Rua das Flores',
    'cliente_bairro' => 'AB',  // Apenas 2 caracteres - deve ser rejeitado (min: 3)
    'cliente_cidade' => 'São Paulo',
    'itens' => [
        [
            'produto_id' => 1,
            'quantidade' => 1,
            'preco_unitario' => 3.00
        ]
    ]
], $token);

if ($pedidoBairroCurto['status'] === 422 || $pedidoBairroCurto['status'] === 400) {
    echo "✓ Pedido rejeitado corretamente (Status: {$pedidoBairroCurto['status']})\n";
    if (isset($pedidoBairroCurto['body']['errors'])) {
        echo "  Erros: " . json_encode($pedidoBairroCurto['body']['errors'], JSON_UNESCAPED_UNICODE) . "\n\n";
    }
} else {
    echo "✗ FALHOU: Pedido foi aceito, mas não deveria!\n";
    echo "  Status: {$pedidoBairroCurto['status']}\n\n";
}

// ETAPA 5: Criar pedido com endereço VÁLIDO
echo "✅ ETAPA 5: Criando pedido com endereço VÁLIDO...\n";
$pedidoValido = makeRequest('POST', '/api/app/pedidos', [
    'tipo_pedido' => 'delivery',
    'cliente_nome' => 'Cliente Teste',
    'cliente_telefone' => '11999990001',
    'cliente_endereco' => 'Rua das Flores',  // 15 caracteres - válido
    'endereco_numero' => '123',
    'cliente_bairro' => 'Centro',  // 6 caracteres - válido
    'cliente_cidade' => 'São Paulo',
    'cliente_cep' => '01234-567',
    'taxa_entrega' => 5.00,
    'itens' => [
        [
            'produto_id' => 1,
            'quantidade' => 2,
            'preco_unitario' => 3.00
        ]
    ]
], $token);

if ($pedidoValido['status'] === 201 && isset($pedidoValido['body']['pedido'])) {
    $pedidoId = $pedidoValido['body']['pedido']['id'];
    echo "✓ Pedido criado com sucesso! ID: {$pedidoId}\n";
    echo "  Total: R$ " . number_format($pedidoValido['body']['pedido']['total'], 2, ',', '.') . "\n";
    if (isset($pedidoValido['body']['delivery'])) {
        echo "  Endereço: {$pedidoValido['body']['delivery']['endereco_rua']}, ";
        echo "{$pedidoValido['body']['delivery']['endereco_numero']} - ";
        echo "{$pedidoValido['body']['delivery']['endereco_bairro']}\n\n";
    }
} else {
    echo "✗ Erro ao criar pedido válido\n";
    print_r($pedidoValido);
    echo "\n";
}

// RESUMO FINAL
echo "==========================================\n";
echo "RESUMO DOS TESTES\n";
echo "==========================================\n";
echo "Teste 1 - Endereço vazio: " . ($pedidoSemEndereco['status'] >= 400 ? "✓ PASSOU" : "✗ FALHOU") . "\n";
echo "Teste 2 - Endereço curto: " . ($pedidoEndCurto['status'] >= 400 ? "✓ PASSOU" : "✗ FALHOU") . "\n";
echo "Teste 3 - Bairro curto: " . ($pedidoBairroCurto['status'] >= 400 ? "✓ PASSOU" : "✗ FALHOU") . "\n";
echo "Teste 4 - Endereço válido: " . ($pedidoValido['status'] === 201 ? "✓ PASSOU" : "✗ FALHOU") . "\n";

$todosPassaram = 
    ($pedidoSemEndereco['status'] >= 400) &&
    ($pedidoEndCurto['status'] >= 400) &&
    ($pedidoBairroCurto['status'] >= 400) &&
    ($pedidoValido['status'] === 201);

echo "\n";
if ($todosPassaram) {
    echo "🎉 TODOS OS TESTES DE VALIDAÇÃO PASSARAM!\n";
    echo "✓ A validação de endereço está funcionando corretamente\n";
} else {
    echo "⚠️ ALGUNS TESTES FALHARAM\n";
    echo "✗ Verifique a validação de endereço no backend\n";
}
echo "==========================================\n";
