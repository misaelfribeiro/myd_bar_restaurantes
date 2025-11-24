<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

// Simular dados de teste
$dadosAPI = [
    'mesa_id' => 1,
    'observacoes' => 'Teste via API direta',
    'itens' => [
        [
            'produto_id' => 1,
            'quantidade' => 2,
            'preco_unitario' => 15.50,
            'observacoes' => null
        ]
    ]
];

echo "=== TESTE API PEDIDOS ===\n";
echo "Dados enviados:\n";
echo json_encode($dadosAPI, JSON_PRETTY_PRINT) . "\n\n";

try {
    // Criar request
    $request = new Request($dadosAPI);
    $request->setMethod('POST');
    
    // Autenticar um usuário (ID 1)
    $usuario = \App\Models\Usuario::find(1);
    if (!$usuario) {
        echo "ERRO: Usuário ID 1 não encontrado\n";
        exit;
    }
    
    \Auth::login($usuario);
    echo "Usuario autenticado: {$usuario->name} (ID: {$usuario->id})\n\n";
    
    // Chamar a API
    $controller = new \App\Http\Controllers\PedidoController();
    $response = $controller->syncOffline($request);
    
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Response:\n";
    echo json_encode($response->getData(true), JSON_PRETTY_PRINT) . "\n";
    
} catch (\Exception $e) {
    echo "ERRO CAPTURADO:\n";
    echo "Tipo: " . get_class($e) . "\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}