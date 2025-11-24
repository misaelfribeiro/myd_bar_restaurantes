<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "========================================\n";
echo "TESTE: API DE PRODUTOS\n";
echo "========================================\n\n";

// Testar GET /api/app/produtos
echo "1. Testando GET /api/app/produtos\n";
echo "-----------------------------------\n";

$request = Illuminate\Http\Request::create('/api/app/produtos', 'GET');
$request->headers->set('Accept', 'application/json');

$response = $kernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Content-Type: " . $response->headers->get('Content-Type') . "\n\n";

$content = $response->getContent();
echo "Resposta:\n";
echo $content . "\n\n";

$data = json_decode($content, true);

if ($data) {
    echo "Estrutura da resposta:\n";
    echo "- success: " . (isset($data['success']) ? ($data['success'] ? 'true' : 'false') : 'não definido') . "\n";
    echo "- produtos: " . (isset($data['produtos']) ? 'existe' : 'não existe') . "\n";
    echo "- data: " . (isset($data['data']) ? 'existe' : 'não existe') . "\n";
    
    if (isset($data['produtos'])) {
        echo "- Total de produtos: " . count($data['produtos']) . "\n";
        if (count($data['produtos']) > 0) {
            echo "\nPrimeiro produto:\n";
            print_r($data['produtos'][0]);
        }
    } else if (is_array($data) && !isset($data['success'])) {
        echo "- É um array direto com " . count($data) . " produtos\n";
        if (count($data) > 0) {
            echo "\nPrimeiro produto:\n";
            print_r($data[0]);
        }
    }
}

echo "\n========================================\n";
echo "2. Testando GET /api/app/categorias\n";
echo "-----------------------------------\n";

$request2 = Illuminate\Http\Request::create('/api/app/categorias', 'GET');
$request2->headers->set('Accept', 'application/json');

$response2 = $kernel->handle($request2);

echo "Status: " . $response2->getStatusCode() . "\n";
$content2 = $response2->getContent();
echo "Resposta:\n";
echo $content2 . "\n\n";

$data2 = json_decode($content2, true);
if ($data2) {
    if (is_array($data2) && isset($data2[0])) {
        echo "Total de categorias: " . count($data2) . "\n";
    }
}

echo "========================================\n";
