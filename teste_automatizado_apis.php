#!/usr/bin/env php
<?php
/**
 * Script de Teste Automatizado das APIs
 * Sistema MyD Bar & Restaurantes
 */

require_once __DIR__ . '/vendor/autoload.php';

class ApiTester
{
    private $baseUrl;
    private $token;
    private $results = [];

    public function __construct($baseUrl = 'http://localhost:8000')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function run()
    {
        echo "🧪 INICIANDO TESTES AUTOMATIZADOS DAS APIs\n";
        echo "Base URL: {$this->baseUrl}\n\n";

        try {
            // 1. Testes de autenticação
            $this->testAuth();
            
            // 2. Testes de produtos (público)
            $this->testProductsPublic();
            
            // 3. Testes de categorias (público)
            $this->testCategoriesPublic();
            
            // 4. Testes autenticados (se token obtido)
            if ($this->token) {
                $this->testProductsAuth();
                $this->testPedidos();
                $this->testDashboard();
                $this->testCaixa();
            }
            
            // Relatório final
            $this->printResults();
            
        } catch (Exception $e) {
            echo "❌ ERRO CRÍTICO: " . $e->getMessage() . "\n";
        }
    }

    private function testAuth()
    {
        echo "🔐 TESTANDO AUTENTICAÇÃO\n";
        
        // Teste de registro
        $registerData = [
            'nome' => 'Teste API ' . time(),
            'email' => 'teste' . time() . '@api.com',
            'password' => '123456',
            'tipo' => 'admin'
        ];
        
        $result = $this->makeRequest('POST', '/api/auth/register', $registerData);
        $this->logResult('Auth Register', $result['success'], $result['data'] ?? $result['error']);
        
        // Teste de login
        $loginData = [
            'email' => $registerData['email'],
            'password' => $registerData['password']
        ];
        
        $result = $this->makeRequest('POST', '/api/auth/login', $loginData);
        $this->logResult('Auth Login', $result['success'], $result['data'] ?? $result['error']);
        
        if ($result['success'] && isset($result['data']['token'])) {
            $this->token = $result['data']['token'];
            echo "✅ Token obtido com sucesso\n";
            
            // Teste de verificação do usuário
            $result = $this->makeRequest('GET', '/api/auth/me');
            $this->logResult('Auth Me', $result['success'], $result['data'] ?? $result['error']);
        }
        
        echo "\n";
    }

    private function testProductsPublic()
    {
        echo "📦 TESTANDO PRODUTOS (PÚBLICO)\n";
        
        // Listar produtos
        $result = $this->makeRequest('GET', '/api/produtos-public');
        $this->logResult('Products List Public', $result['success'], 
            $result['success'] ? count($result['data']) . ' produtos encontrados' : $result['error']);
        
        // Buscar produtos
        $result = $this->makeRequest('GET', '/api/produtos-public?search=test');
        $this->logResult('Products Search', $result['success'], 
            $result['success'] ? 'Busca executada' : $result['error']);
        
        echo "\n";
    }

    private function testCategoriesPublic()
    {
        echo "🏷️ TESTANDO CATEGORIAS (PÚBLICO)\n";
        
        $result = $this->makeRequest('GET', '/api/categorias-public');
        $this->logResult('Categories List Public', $result['success'], 
            $result['success'] ? count($result['data']) . ' categorias encontradas' : $result['error']);
        
        echo "\n";
    }

    private function testProductsAuth()
    {
        echo "🔒 TESTANDO PRODUTOS (AUTENTICADO)\n";
        
        // Criar categoria primeiro
        $categoryData = [
            'nome' => 'Categoria Teste API',
            'descricao' => 'Categoria criada para teste automatizado'
        ];
        
        $result = $this->makeRequest('POST', '/api/categorias', $categoryData);
        $this->logResult('Category Create', $result['success'], $result['data'] ?? $result['error']);
        
        $categoryId = $result['success'] ? $result['data']['id'] : 1;
        
        // Criar produto
        $productData = [
            'nome' => 'Produto Teste API ' . time(),
            'descricao' => 'Produto criado via teste automatizado',
            'preco' => 15.99,
            'categoria_id' => $categoryId,
            'disponivel' => true
        ];
        
        $result = $this->makeRequest('POST', '/api/produtos', $productData);
        $this->logResult('Product Create', $result['success'], $result['data'] ?? $result['error']);
        
        if ($result['success']) {
            $productId = $result['data']['id'];
            
            // Toggle status
            $result = $this->makeRequest('PATCH', "/api/produtos/{$productId}/toggle-status");
            $this->logResult('Product Toggle Status', $result['success'], $result['data'] ?? $result['error']);
        }
        
        echo "\n";
    }

    private function testPedidos()
    {
        echo "📝 TESTANDO PEDIDOS\n";
        
        // Listar mesas
        $result = $this->makeRequest('GET', '/api/mesas');
        if (!$result['success'] || empty($result['data'])) {
            echo "⚠️ Criando mesa de teste...\n";
            $mesaData = ['numero' => 999, 'capacidade' => 4, 'status' => 'livre'];
            $this->makeRequest('POST', '/api/mesas', $mesaData);
        }
        
        // Criar pedido
        $pedidoData = [
            'mesa_id' => 1,
            'observacoes' => 'Pedido de teste automatizado'
        ];
        
        $result = $this->makeRequest('POST', '/api/pedidos', $pedidoData);
        $this->logResult('Pedido Create', $result['success'], $result['data'] ?? $result['error']);
        
        if ($result['success']) {
            $pedidoId = $result['data']['id'];
            
            // Adicionar item (se existe produto)
            $produtos = $this->makeRequest('GET', '/api/produtos');
            if ($produtos['success'] && !empty($produtos['data'])) {
                $produtoId = $produtos['data'][0]['id'];
                
                $itemData = [
                    'pedido_id' => $pedidoId,
                    'produto_id' => $produtoId,
                    'quantidade' => 2,
                    'observacoes' => 'Teste automatizado'
                ];
                
                $result = $this->makeRequest('POST', '/api/item-pedidos', $itemData);
                $this->logResult('Item Add to Pedido', $result['success'], $result['data'] ?? $result['error']);
            }
            
            // Finalizar pedido
            $result = $this->makeRequest('POST', "/api/pedidos/{$pedidoId}/finalizar");
            $this->logResult('Pedido Finalizar', $result['success'], $result['data'] ?? $result['error']);
        }
        
        echo "\n";
    }

    private function testDashboard()
    {
        echo "📊 TESTANDO DASHBOARD\n";
        
        $endpoints = [
            '/api/dashboard/stats' => 'Dashboard Stats',
            '/api/dashboard/pedidos-status' => 'Pedidos Status',
            '/api/dashboard/produtos-vendidos' => 'Produtos Vendidos'
        ];
        
        foreach ($endpoints as $endpoint => $name) {
            $result = $this->makeRequest('GET', $endpoint);
            $this->logResult($name, $result['success'], $result['data'] ?? $result['error']);
        }
        
        echo "\n";
    }

    private function testCaixa()
    {
        echo "💰 TESTANDO CAIXA\n";
        
        // Totais em tempo real
        $result = $this->makeRequest('GET', '/caixa/api/totais-tempo-real');
        $this->logResult('Caixa Totais', $result['success'], $result['data'] ?? $result['error']);
        
        echo "\n";
    }

    private function makeRequest($method, $endpoint, $data = null)
    {
        $url = $this->baseUrl . $endpoint;
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->getHeaders(),
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return ['success' => false, 'error' => "CURL Error: $error"];
        }
        
        $decodedResponse = json_decode($response, true);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'data' => $decodedResponse, 'http_code' => $httpCode];
        } else {
            return [
                'success' => false, 
                'error' => $decodedResponse['message'] ?? $response,
                'http_code' => $httpCode
            ];
        }
    }

    private function getHeaders()
    {
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        if ($this->token) {
            $headers[] = "Authorization: Bearer {$this->token}";
        }
        
        return $headers;
    }

    private function logResult($test, $success, $details)
    {
        $this->results[] = [
            'test' => $test,
            'success' => $success,
            'details' => $details
        ];
        
        $icon = $success ? '✅' : '❌';
        $status = $success ? 'PASS' : 'FAIL';
        
        echo sprintf("%-30s %s %s\n", $test, $icon, $status);
        
        if (!$success && $details) {
            echo "    Erro: " . (is_array($details) ? json_encode($details) : $details) . "\n";
        }
    }

    private function printResults()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📋 RESUMO DOS TESTES\n";
        echo str_repeat("=", 60) . "\n";
        
        $total = count($this->results);
        $passed = count(array_filter($this->results, fn($r) => $r['success']));
        $failed = $total - $passed;
        
        echo "Total de testes: $total\n";
        echo "✅ Passaram: $passed\n";
        echo "❌ Falharam: $failed\n";
        echo "📊 Taxa de sucesso: " . round(($passed / $total) * 100, 1) . "%\n";
        
        if ($failed > 0) {
            echo "\n🔍 TESTES QUE FALHARAM:\n";
            foreach ($this->results as $result) {
                if (!$result['success']) {
                    echo "- {$result['test']}: {$result['details']}\n";
                }
            }
        }
        
        echo "\n✨ Teste finalizado!\n";
    }
}

// Executar testes
if (php_sapi_name() === 'cli') {
    $baseUrl = $argv[1] ?? 'http://localhost:8000';
    $tester = new ApiTester($baseUrl);
    $tester->run();
} else {
    echo "Este script deve ser executado via linha de comando.\n";
    echo "Uso: php teste_automatizado_apis.php [base_url]\n";
}
