<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pedido;
use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Http\Controllers\PedidoController;

echo "=== TESTE FINAL DA API DE EXCLUSÃO ===\n";

// Buscar pedido de teste
$pedido = Pedido::with('itens')->find(50);
if (!$pedido || $pedido->itens->count() < 2) {
    echo "❌ Pedido de teste não encontrado ou não tem itens suficientes\n";
    exit;
}

$admin = Usuario::where('role', 'admin')->first();
if (!$admin) {
    echo "❌ Usuário admin não encontrado\n";
    exit;
}

echo "✅ Teste com pedido ID {$pedido->id} e usuário {$admin->nome}\n";
echo "Itens antes da exclusão: {$pedido->itens->count()}\n";

// Selecionar um item para excluir
$itemParaExcluir = $pedido->itens->first();
echo "Item a excluir: {$itemParaExcluir->id} - {$itemParaExcluir->produto->nome}\n";

// Simular login do usuário admin
auth()->login($admin);

// Criar uma requisição simulada
$request = new Request();
$request->headers->set('Accept', 'application/json');

// Instanciar controller
$controller = new PedidoController();

echo "\n=== EXECUTANDO EXCLUSÃO ===\n";

try {
    // Chamar método de exclusão
    $response = $controller->removeItem($request, $pedido, $itemParaExcluir->id);
    
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        
        if ($data['success']) {
            echo "✅ Exclusão bem-sucedida!\n";
            echo "Mensagem: {$data['message']}\n";
            echo "Novo total: R$ " . number_format($data['novo_total'], 2, ',', '.') . "\n";
            
            // Verificar se o item foi realmente excluído
            $pedido->refresh();
            $pedido->load('itens');
            
            echo "Itens restantes: {$pedido->itens->count()}\n";
            
            foreach ($pedido->itens as $item) {
                echo "- {$item->produto->nome} (ID: {$item->id})\n";
            }
            
        } else {
            echo "❌ Falha na exclusão: {$data['message']}\n";
        }
    } else {
        echo "❌ Resposta inesperada do controller\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro durante a execução: {$e->getMessage()}\n";
}

// Teste com usuário sem permissão (criar um garçom)
echo "\n=== TESTE COM USUÁRIO SEM PERMISSÃO ===\n";

$garcom = Usuario::where('role', 'garcom')->first();
if ($garcom) {
    auth()->login($garcom);
    
    try {
        $response = $controller->removeItem($request, $pedido, $pedido->itens->first()->id);
        $data = $response->getData(true);
        
        if (!$data['success'] && str_contains($data['message'], 'Acesso negado')) {
            echo "✅ Controle de acesso funcionando: {$data['message']}\n";
        } else {
            echo "❌ Falha no controle de acesso\n";
        }
    } catch (Exception $e) {
        echo "Erro: {$e->getMessage()}\n";
    }
} else {
    echo "⚠️ Nenhum usuário garçom encontrado para testar controle de acesso\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";