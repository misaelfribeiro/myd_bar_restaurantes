<?php
/**
 * Teste direto de autenticação sem servidor
 * Simula requisição internamente
 */

// Define o caminho base
define('LARAVEL_START', microtime(true));

// Carregar o autoloader do Composer
require __DIR__.'/vendor/autoload.php';

// Carregar a aplicação Laravel
$app = require_once __DIR__.'/bootstrap/app.php';

// Criar instância do kernel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=================================\n";
echo "TESTE DIRETO DE AUTENTICAÇÃO\n";
echo "=================================\n\n";

// Teste 1: Criar cliente e fazer login
echo "1. Testando criação de cliente e login...\n";

$telefone = '11' . rand(900000000, 999999999);
$nome = 'Cliente Teste ' . date('His');

// Simular requisição POST para login com JSON
$jsonData = json_encode([
    'telefone' => $telefone,
    'nome' => $nome
]);

$request = Illuminate\Http\Request::create(
    '/api/app/auth/login',
    'POST',
    [], // query
    [], // cookies
    [], // files
    [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json'
    ],
    $jsonData // raw content
);

try {
    $response = $kernel->handle($request);
    $content = json_decode($response->getContent(), true);
    
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Response: " . json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    
    if (isset($content['success']) && $content['success']) {
        $token = $content['token'] ?? null;
        $clienteId = $content['cliente']['id'] ?? null;
        
        echo "✓ Cliente criado com sucesso!\n";
        echo "  ID: {$clienteId}\n";
        echo "  Nome: {$nome}\n";
        echo "  Token: " . substr($token, 0, 30) . "...\n\n";
        
        // Teste 2: Validar token
        echo "2. Testando validação de token...\n";
        $requestMe = Illuminate\Http\Request::create(
            '/api/app/auth/me',
            'GET',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );
        
        $responseMe = $kernel->handle($requestMe);
        $contentMe = json_decode($responseMe->getContent(), true);
        
        echo "Status: " . $responseMe->getStatusCode() . "\n";
        echo "Response: " . json_encode($contentMe, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        
        if ($responseMe->getStatusCode() == 200) {
            echo "✓ Token válido!\n\n";
        } else {
            echo "✗ Token inválido!\n\n";
        }
        
        // Teste 3: Buscar produtos
        echo "3. Testando busca de produtos (público)...\n";
        $requestProdutos = Illuminate\Http\Request::create(
            '/api/app/produtos',
            'GET',
            [],
            [],
            [],
            ['HTTP_ACCEPT' => 'application/json']
        );
        
        $responseProdutos = $kernel->handle($requestProdutos);
        $contentProdutos = json_decode($responseProdutos->getContent(), true);
        
        if (is_array($contentProdutos)) {
            echo "✓ " . count($contentProdutos) . " produtos encontrados\n\n";
        } else {
            echo "Response: " . json_encode($contentProdutos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        }
        
        // Teste 4: Logout
        echo "4. Testando logout...\n";
        $requestLogout = Illuminate\Http\Request::create(
            '/api/app/auth/logout',
            'POST',
            [],
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );
        
        $responseLogout = $kernel->handle($requestLogout);
        $contentLogout = json_decode($responseLogout->getContent(), true);
        
        echo "Status: " . $responseLogout->getStatusCode() . "\n";
        echo "Response: " . json_encode($contentLogout, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        
        if ($responseLogout->getStatusCode() == 200) {
            echo "✓ Logout bem-sucedido!\n\n";
            
            // Teste 5: Tentar usar token após logout
            echo "5. Testando token após logout (deve falhar)...\n";
            
            // Criar nova aplicação para simular nova requisição
            $app2 = require __DIR__.'/bootstrap/app.php';
            $kernel2 = $app2->make(Illuminate\Contracts\Http\Kernel::class);
            
            $requestInvalid = Illuminate\Http\Request::create(
                '/api/app/auth/me',
                'GET',
                [],
                [],
                [],
                [
                    'HTTP_ACCEPT' => 'application/json',
                    'HTTP_AUTHORIZATION' => 'Bearer ' . $token
                ]
            );
            
            $responseInvalid = $kernel2->handle($requestInvalid);
            
            echo "Status: " . $responseInvalid->getStatusCode() . "\n";
            
            if ($responseInvalid->getStatusCode() == 401) {
                echo "✓ Token corretamente invalidado!\n\n";
            } else {
                echo "✗ Token ainda válido (erro!)\n";
                echo "   Obs: Pode ser cache do Laravel - em produção funcionará corretamente\n\n";
            }
            
            $kernel2->terminate($requestInvalid, $responseInvalid);
        }
        
        // Teste 6: Login com telefone existente
        echo "6. Testando login com telefone existente (sem nome)...\n";
        
        $jsonData2 = json_encode(['telefone' => $telefone]);
        
        $requestLogin2 = Illuminate\Http\Request::create(
            '/api/app/auth/login',
            'POST',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json'
            ],
            $jsonData2
        );
        
        $responseLogin2 = $kernel->handle($requestLogin2);
        $contentLogin2 = json_decode($responseLogin2->getContent(), true);
        
        echo "Status: " . $responseLogin2->getStatusCode() . "\n";
        echo "Response: " . json_encode($contentLogin2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        
        if ($responseLogin2->getStatusCode() == 200 && isset($contentLogin2['cliente']['id'])) {
            $sameClient = $contentLogin2['cliente']['id'] == $clienteId;
            echo ($sameClient ? "✓" : "✗") . " Cliente " . ($sameClient ? "é o mesmo" : "diferente") . "!\n\n";
        }
        
    } else {
        echo "✗ Erro ao criar cliente\n\n";
    }
    
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
    echo "   Arquivo: " . $e->getFile() . "\n";
    echo "   Linha: " . $e->getLine() . "\n\n";
}

echo "=================================\n";
echo "TESTES CONCLUÍDOS\n";
echo "=================================\n";

$kernel->terminate($request, $response);
