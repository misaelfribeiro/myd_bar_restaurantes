<?php
echo "Teste simples de finalização de pedido\n";
echo "======================================\n";

try {
    // Teste direto com cURL para a API
    $pedidoId = 420; // ID de um pedido existente (baseado na imagem que vi)
    
    $url = "http://localhost:8000/api/pedidos/{$pedidoId}";
    
    $data = json_encode(['status' => 'finalizado']);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "URL testada: {$url}\n";
    echo "Dados enviados: {$data}\n";
    echo "Código HTTP: {$httpCode}\n";
    
    if ($error) {
        echo "Erro cURL: {$error}\n";
    } else {
        echo "Resposta: {$response}\n";
    }
    
    // Decodificar resposta JSON se possível
    $responseData = json_decode($response, true);
    if ($responseData) {
        echo "\nResposta decodificada:\n";
        print_r($responseData);
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\nTeste finalizado!\n";
?>
