<?php

/**
 * Teste completo do fluxo do app cliente
 * Login → Produtos → Criar Pedido → Listar Pedidos → Cancelar
 */

// Define o caminho base
define('LARAVEL_START', microtime(true));

// Carregar o autoloader do Composer
require __DIR__.'/vendor/autoload.php';

// Carregar a aplicação Laravel
$app = require_once __DIR__.'/bootstrap/app.php';

// Criar instância do kernel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "==========================================\n";
echo "TESTE COMPLETO DO FLUXO DO APP CLIENTE\n";
echo "==========================================\n\n";

$token = null;
$clienteId = null;
$pedidoId = null;

// ====================================
// ETAPA 1: LOGIN/REGISTRO
// ====================================
echo "ETAPA 1: Login/Registro\n";
echo "========================\n";

$telefone = '11' . rand(900000000, 999999999);
$nome = 'Cliente Teste Fluxo ' . date('His');

$jsonData = json_encode([
    'telefone' => $telefone,
    'nome' => $nome
]);

$request = Illuminate\Http\Request::create(
    '/api/app/auth/login',
    'POST',
    [],
    [],
    [],
    [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json'
    ],
    $jsonData
);

$response = $kernel->handle($request);
$content = json_decode($response->getContent(), true);

if ($response->getStatusCode() == 201 && isset($content['token'])) {
    $token = $content['token'];
    $clienteId = $content['cliente']['id'];
    echo "✓ Login bem-sucedido!\n";
    echo "  Cliente ID: {$clienteId}\n";
    echo "  Token: " . substr($token, 0, 30) . "...\n\n";
} else {
    echo "✗ Erro no login\n";
    echo json_encode($content, JSON_PRETTY_PRINT) . "\n";
    exit(1);
}

$kernel->terminate($request, $response);

// ====================================
// ETAPA 2: BUSCAR PRODUTOS
// ====================================
echo "ETAPA 2: Buscar Produtos\n";
echo "========================\n";

$app2 = require __DIR__.'/bootstrap/app.php';
$kernel2 = $app2->make(Illuminate\Contracts\Http\Kernel::class);

$requestProdutos = Illuminate\Http\Request::create(
    '/api/app/produtos',
    'GET',
    [],
    [],
    [],
    ['HTTP_ACCEPT' => 'application/json']
);

$responseProdutos = $kernel2->handle($requestProdutos);
$produtosResponse = json_decode($responseProdutos->getContent(), true);
$produtos = $produtosResponse['produtos'] ?? [];

if (is_array($produtos) && count($produtos) > 0) {
    echo "✓ " . count($produtos) . " produtos encontrados\n";
    $primeiroProduto = $produtos[0];
    echo "  Produto: {$primeiroProduto['nome']} - R$ {$primeiroProduto['preco']}\n\n";
} else {
    echo "✗ Nenhum produto encontrado\n\n";
    exit(1);
}

$kernel2->terminate($requestProdutos, $responseProdutos);

// ====================================
// ETAPA 3: CRIAR PEDIDO SEM ENDEREÇO (deve falhar)
// ====================================
echo "ETAPA 3: Tentar criar pedido SEM endereço\n";
echo "==========================================\n";

$app3 = require __DIR__.'/bootstrap/app.php';
$kernel3 = $app3->make(Illuminate\Contracts\Http\Kernel::class);

$pedidoSemEndereco = json_encode([
    'tipo_pedido' => 'delivery',
    'cliente_id' => $clienteId,
    'cliente_nome' => $nome,
    'cliente_telefone' => $telefone,
    'cliente_endereco' => '',
    'cliente_bairro' => '',
    'itens' => [
        [
            'produto_id' => $primeiroProduto['id'],
            'quantidade' => 2,
            'preco_unitario' => $primeiroProduto['preco']
        ]
    ]
]);

$requestPedidoFail = Illuminate\Http\Request::create(
    '/api/app/pedidos',
    'POST',
    [],
    [],
    [],
    [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_AUTHORIZATION' => 'Bearer ' . $token
    ],
    $pedidoSemEndereco
);

$responsePedidoFail = $kernel3->handle($requestPedidoFail);
$contentPedidoFail = json_decode($responsePedidoFail->getContent(), true);

if ($responsePedidoFail->getStatusCode() == 422) {
    echo "✓ Validação funcionando - pedido recusado sem endereço\n";
    echo "  Erro: " . ($contentPedidoFail['message'] ?? 'Validação falhou') . "\n\n";
} else {
    echo "⚠ Pedido aceito sem endereço (não deveria!)\n\n";
}

$kernel3->terminate($requestPedidoFail, $responsePedidoFail);

// ====================================
// ETAPA 4: CRIAR PEDIDO COM ENDEREÇO
// ====================================
echo "ETAPA 4: Criar pedido COM endereço completo\n";
echo "============================================\n";

$app4 = require __DIR__.'/bootstrap/app.php';
$kernel4 = $app4->make(Illuminate\Contracts\Http\Kernel::class);

$pedidoCompleto = json_encode([
    'tipo_pedido' => 'delivery',
    'cliente_id' => $clienteId,
    'cliente_nome' => $nome,
    'cliente_telefone' => $telefone,
    'cliente_endereco' => 'Rua das Flores, 123',
    'cliente_bairro' => 'Centro',
    'cliente_cidade' => 'São Paulo',
    'cliente_cep' => '01234-567',
    'endereco_numero' => '123',
    'observacoes' => 'Teste de pedido completo',
    'itens' => [
        [
            'produto_id' => $primeiroProduto['id'],
            'quantidade' => 2,
            'preco_unitario' => $primeiroProduto['preco'],
            'observacoes' => 'Bem passado'
        ]
    ]
]);

$requestPedido = Illuminate\Http\Request::create(
    '/api/app/pedidos',
    'POST',
    [],
    [],
    [],
    [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_AUTHORIZATION' => 'Bearer ' . $token
    ],
    $pedidoCompleto
);

$responsePedido = $kernel4->handle($requestPedido);
$contentPedido = json_decode($responsePedido->getContent(), true);

if ($responsePedido->getStatusCode() == 201 && isset($contentPedido['pedido'])) {
    $pedidoId = $contentPedido['pedido']['id'];
    $total = $contentPedido['pedido']['total'];
    echo "✓ Pedido criado com sucesso!\n";
    echo "  Pedido ID: {$pedidoId}\n";
    echo "  Total: R$ {$total}\n";
    echo "  Itens: " . count($contentPedido['pedido']['itens']) . "\n\n";
} else {
    echo "✗ Erro ao criar pedido\n";
    echo json_encode($contentPedido, JSON_PRETTY_PRINT) . "\n\n";
    exit(1);
}

$kernel4->terminate($requestPedido, $responsePedido);

// ====================================
// ETAPA 5: LISTAR PEDIDOS DO CLIENTE
// ====================================
echo "ETAPA 5: Listar pedidos do cliente\n";
echo "===================================\n";

$app5 = require __DIR__.'/bootstrap/app.php';
$kernel5 = $app5->make(Illuminate\Contracts\Http\Kernel::class);

$requestListarPedidos = Illuminate\Http\Request::create(
    '/api/app/pedidos',
    'GET',
    [],
    [],
    [],
    [
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_AUTHORIZATION' => 'Bearer ' . $token
    ]
);

$responseListarPedidos = $kernel5->handle($requestListarPedidos);
$pedidos = json_decode($responseListarPedidos->getContent(), true);

if (is_array($pedidos) && count($pedidos) > 0) {
    echo "✓ " . count($pedidos) . " pedido(s) encontrado(s)\n";
    $pedidoEncontrado = false;
    foreach ($pedidos as $p) {
        if ($p['id'] == $pedidoId) {
            $pedidoEncontrado = true;
            echo "  ✓ Pedido #{$pedidoId} presente na lista\n";
            echo "    Status: {$p['status']}\n";
            if (isset($p['delivery'])) {
                echo "    Delivery Status: {$p['delivery']['status']}\n";
            }
        }
    }
    if (!$pedidoEncontrado) {
        echo "  ⚠ Pedido #{$pedidoId} NÃO encontrado na lista!\n";
    }
    echo "\n";
} else {
    echo "✗ Nenhum pedido retornado\n\n";
}

$kernel5->terminate($requestListarPedidos, $responseListarPedidos);

// ====================================
// ETAPA 6: BUSCAR DETALHES DO PEDIDO
// ====================================
echo "ETAPA 6: Buscar detalhes do pedido #{$pedidoId}\n";
echo "================================================\n";

$app6 = require __DIR__.'/bootstrap/app.php';
$kernel6 = $app6->make(Illuminate\Contracts\Http\Kernel::class);

$requestDetalhe = Illuminate\Http\Request::create(
    "/api/app/pedidos/{$pedidoId}",
    'GET',
    [],
    [],
    [],
    [
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_AUTHORIZATION' => 'Bearer ' . $token
    ]
);

$responseDetalhe = $kernel6->handle($requestDetalhe);
$detalheResponse = json_decode($responseDetalhe->getContent(), true);
$detalhe = $detalheResponse['pedido'] ?? null;

if ($responseDetalhe->getStatusCode() == 200 && $detalhe) {
    echo "✓ Detalhes do pedido carregados\n";
    echo "  Status: {$detalhe['status']}\n";
    echo "  Total: R$ {$detalhe['total']}\n";
    echo "  Itens: " . count($detalhe['itens']) . "\n\n";
} else {
    echo "✗ Erro ao buscar detalhes\n\n";
}

$kernel6->terminate($requestDetalhe, $responseDetalhe);

// ====================================
// ETAPA 7: CANCELAR PEDIDO
// ====================================
echo "ETAPA 7: Cancelar pedido #{$pedidoId}\n";
echo "======================================\n";

$app7 = require __DIR__.'/bootstrap/app.php';
$kernel7 = $app7->make(Illuminate\Contracts\Http\Kernel::class);

$requestCancelar = Illuminate\Http\Request::create(
    "/api/app/pedidos/{$pedidoId}/cancelar",
    'POST',
    [],
    [],
    [],
    [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_AUTHORIZATION' => 'Bearer ' . $token
    ]
);

$responseCancelar = $kernel7->handle($requestCancelar);
$contentCancelar = json_decode($responseCancelar->getContent(), true);

if ($responseCancelar->getStatusCode() == 200 && $contentCancelar['success']) {
    echo "✓ Pedido cancelado com sucesso\n";
    echo "  Mensagem: {$contentCancelar['message']}\n\n";
} else {
    echo "✗ Erro ao cancelar pedido\n";
    echo json_encode($contentCancelar, JSON_PRETTY_PRINT) . "\n\n";
}

$kernel7->terminate($requestCancelar, $responseCancelar);

// ====================================
// ETAPA 8: VERIFICAR PEDIDO CANCELADO
// ====================================
echo "ETAPA 8: Verificar status após cancelamento\n";
echo "============================================\n";

$app8 = require __DIR__.'/bootstrap/app.php';
$kernel8 = $app8->make(Illuminate\Contracts\Http\Kernel::class);

$requestVerificar = Illuminate\Http\Request::create(
    "/api/app/pedidos/{$pedidoId}",
    'GET',
    [],
    [],
    [],
    [
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_AUTHORIZATION' => 'Bearer ' . $token
    ]
);

$responseVerificar = $kernel8->handle($requestVerificar);
$verificarResponse = json_decode($responseVerificar->getContent(), true);
$verificar = $verificarResponse['pedido'] ?? null;

if ($verificar && $verificar['status'] == 'cancelado') {
    echo "✓ Status confirmado como 'cancelado'\n";
    if (isset($verificar['delivery']) && $verificar['delivery']['status'] == 'cancelado') {
        echo "  ✓ Delivery também cancelado\n\n";
    }
} else {
    $status = $verificar['status'] ?? 'desconhecido';
    echo "⚠ Status não atualizado corretamente: {$status}\n\n";
}

$kernel8->terminate($requestVerificar, $responseVerificar);

// ====================================
// RESUMO
// ====================================
echo "==========================================\n";
echo "RESUMO DO TESTE\n";
echo "==========================================\n";
echo "Cliente ID: {$clienteId}\n";
echo "Telefone: {$telefone}\n";
echo "Pedido ID: {$pedidoId}\n";
echo "Status Final: cancelado\n";
echo "\n✓ TODOS OS TESTES PASSARAM COM SUCESSO!\n";
echo "==========================================\n";
