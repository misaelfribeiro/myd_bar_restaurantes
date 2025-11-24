<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== TESTANDO API DE DESTAQUES ===\n\n";

// Simular a chamada do controller
$controller = new \App\Http\Controllers\ProdutoController();
$response = $controller->destaques();
$data = json_decode($response->getContent(), true);

echo "Success: " . ($data['success'] ? 'SIM' : 'NÃO') . "\n";
echo "Quantidade de produtos: " . count($data['produtos'] ?? []) . "\n\n";

if (!empty($data['produtos'])) {
    echo "Produtos retornados:\n";
    foreach($data['produtos'] as $p) {
        echo "- {$p['nome']} (R$ {$p['preco']})\n";
        echo "  Tenant: " . ($p['tenant_code'] ?? 'N/A') . "\n";
        echo "  Ativo: " . ($p['ativo'] ? 'SIM' : 'NÃO') . "\n";
        echo "  Destaque: " . ($p['destaque'] ? 'SIM' : 'NÃO') . "\n";
        echo "  Empresa: " . ($p['empresa']['nome_fantasia'] ?? 'N/A') . "\n\n";
    }
} else {
    echo "⚠️ Nenhum produto retornado!\n";
}

echo "\nJSON completo:\n";
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
