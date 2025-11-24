<?php

/**
 * Script para testar autenticação do app cliente
 * Executa teste de login/registro via API
 */

$baseUrl = 'http://127.0.0.1:8000/api';

echo "=================================\n";
echo "TESTE DE AUTENTICAÇÃO APP CLIENTE\n";
echo "=================================\n\n";

// Teste 1: Login/Registro com telefone novo
echo "1. Testando login/registro com novo telefone...\n";
$telefone = '11' . rand(900000000, 999999999);
$dados = [
    'telefone' => $telefone,
    'nome' => 'Cliente Teste ' . date('His')
];

$response = fazerRequisicao("$baseUrl/app/auth/login", 'POST', $dados);
echo "Response: " . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

if (isset($response['success']) && $response['success']) {
    $token = $response['token'] ?? null;
    $cliente = $response['cliente'] ?? null;
    
    if ($token && $cliente) {
        echo "✓ Login bem-sucedido!\n";
        echo "  Token: " . substr($token, 0, 20) . "...\n";
        echo "  Cliente ID: {$cliente['id']}\n";
        echo "  Nome: {$cliente['nome']}\n\n";
        
        // Teste 2: Validar token
        echo "2. Testando validação de token...\n";
        $meResponse = fazerRequisicao("$baseUrl/app/auth/me", 'GET', null, [
            'Authorization: Bearer ' . $token
        ]);
        echo "Response: " . json_encode($meResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        
        if (isset($meResponse['success']) && $meResponse['success']) {
            echo "✓ Token válido!\n\n";
            
            // Teste 3: Buscar produtos (rota pública)
            echo "3. Testando busca de produtos (público)...\n";
            $produtosResponse = fazerRequisicao("$baseUrl/app/produtos", 'GET');
            if (is_array($produtosResponse)) {
                echo "✓ " . count($produtosResponse) . " produtos encontrados\n\n";
            }
            
            // Teste 4: Buscar categorias (rota pública)
            echo "4. Testando busca de categorias (público)...\n";
            $categoriasResponse = fazerRequisicao("$baseUrl/app/categorias", 'GET');
            if (is_array($categoriasResponse)) {
                echo "✓ " . count($categoriasResponse) . " categorias encontradas\n\n";
            }
            
            // Teste 5: Buscar pedidos do cliente (rota protegida)
            echo "5. Testando busca de pedidos (protegido)...\n";
            $pedidosResponse = fazerRequisicao("$baseUrl/app/pedidos", 'GET', null, [
                'Authorization: Bearer ' . $token
            ]);
            echo "Response: " . json_encode($pedidosResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
            
            // Teste 6: Logout
            echo "6. Testando logout...\n";
            $logoutResponse = fazerRequisicao("$baseUrl/app/auth/logout", 'POST', null, [
                'Authorization: Bearer ' . $token
            ]);
            echo "Response: " . json_encode($logoutResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
            
            if (isset($logoutResponse['success']) && $logoutResponse['success']) {
                echo "✓ Logout bem-sucedido!\n\n";
                
                // Teste 7: Tentar usar token após logout (deve falhar)
                echo "7. Testando token após logout (deve falhar)...\n";
                $invalidResponse = fazerRequisicao("$baseUrl/app/auth/me", 'GET', null, [
                    'Authorization: Bearer ' . $token
                ]);
                echo "Response: " . json_encode($invalidResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
                
                if (isset($invalidResponse['message']) && stripos($invalidResponse['message'], 'unauthenticated') !== false) {
                    echo "✓ Token corretamente invalidado!\n\n";
                } else {
                    echo "✗ Erro: Token ainda válido após logout\n\n";
                }
            }
            
        } else {
            echo "✗ Erro ao validar token\n\n";
        }
        
    } else {
        echo "✗ Erro: Token ou dados do cliente não retornados\n\n";
    }
} else {
    echo "✗ Erro no login/registro\n\n";
}

// Teste 8: Login com telefone existente
echo "8. Testando login com telefone existente...\n";
$loginResponse = fazerRequisicao("$baseUrl/app/auth/login", 'POST', [
    'telefone' => $telefone
]);
echo "Response: " . json_encode($loginResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

if (isset($loginResponse['success']) && $loginResponse['success']) {
    echo "✓ Login com telefone existente funcionando!\n";
    echo "  Cliente mantém o mesmo: " . ($loginResponse['cliente']['nome'] ?? 'N/A') . "\n\n";
} else {
    echo "✗ Erro ao fazer login com telefone existente\n\n";
}

echo "=================================\n";
echo "TESTES CONCLUÍDOS\n";
echo "=================================\n";

/**
 * Função auxiliar para fazer requisições HTTP
 */
function fazerRequisicao($url, $method = 'GET', $dados = null, $headers = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $defaultHeaders = [
        'Accept: application/json',
        'Content-Type: application/json'
    ];
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($defaultHeaders, $headers));
    
    if ($dados && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['error' => $error, 'http_code' => $httpCode];
    }
    
    curl_close($ch);
    
    $decoded = json_decode($response, true);
    return $decoded ?: ['raw_response' => $response, 'http_code' => $httpCode];
}
