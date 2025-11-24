<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\DeliveryController;

echo "=== TESTE DE CRIAÇÃO DE DELIVERY ===\n\n";

// Simular dados de um formulário
$dadosSimulados = [
    'cliente_id' => 4, // Maria Santos
    'cliente_nome' => 'Maria Santos',
    'cliente_telefone' => '(11) 98888-2222',
    'cliente_email' => 'maria@teste.com',
    'endereco_rua' => 'Av. Paulista',
    'endereco_numero' => '1000',
    'endereco_complemento' => 'Apt 101',
    'endereco_bairro' => 'Bela Vista',
    'endereco_cidade' => 'São Paulo',
    'endereco_cep' => '01310-100',
    'taxa_entrega' => 5.00,
    'tempo_estimado' => 30,
    'observacoes' => 'Teste de delivery'
];

echo "📋 Dados que serão enviados:\n";
print_r($dadosSimulados);

try {
    // Criar request simulado
    $request = Request::create('/deliveries', 'POST', $dadosSimulados);
    
    // Autenticar usuário (pegar primeiro usuário)
    $usuario = \App\Models\Usuario::first();
    if ($usuario) {
        \Auth::login($usuario);
        echo "\n✅ Usuário autenticado: {$usuario->nome}\n";
    } else {
        echo "\n⚠️ Nenhum usuário encontrado\n";
    }
    
    // Testar validação
    $validator = \Validator::make($dadosSimulados, [
        'cliente_id' => 'required|integer|exists:clientes,id',
        'cliente_nome' => 'required|string|max:255',
        'cliente_telefone' => 'required|string|max:20',
        'cliente_email' => 'nullable|email|max:255',
        'endereco_rua' => 'required|string|max:255',
        'endereco_numero' => 'required|string|max:20',
        'endereco_complemento' => 'nullable|string|max:255',
        'endereco_bairro' => 'required|string|max:100',
        'endereco_cidade' => 'required|string|max:100',
        'endereco_cep' => 'required|string|max:9',
        'endereco_referencia' => 'nullable|string',
        'taxa_entrega' => 'required|numeric|min:0',
        'tempo_estimado' => 'required|integer|min:10',
        'distancia_km' => 'nullable|numeric|min:0',
        'pedido_id' => 'nullable|exists:pedidos,id',
        'observacoes' => 'nullable|string',
        'observacoes_internas' => 'nullable|string',
    ]);
    
    if ($validator->fails()) {
        echo "\n❌ ERRO DE VALIDAÇÃO:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "  - $error\n";
        }
    } else {
        echo "\n✅ VALIDAÇÃO PASSOU!\n";
        
        // Tentar criar delivery
        $controller = new DeliveryController();
        $response = $controller->store($request);
        
        echo "\n✅ DELIVERY CRIADO COM SUCESSO!\n";
        echo "Response: " . $response->getContent() . "\n";
    }
    
} catch (\Exception $e) {
    echo "\n💥 ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . " linha " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIM DO TESTE ===\n";
