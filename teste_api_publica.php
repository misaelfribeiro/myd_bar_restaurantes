<?php
echo "Teste da nova API pública para finalizar pedidos\n";
echo "===============================================\n";

// Teste simples com cURL
$pedidoId = 420; // ID de exemplo
$url = "http://localhost:8000/api/pedidos-public/{$pedidoId}";
$data = json_encode(['status' => 'finalizado']);

echo "URL: {$url}\n";
echo "Dados: {$data}\n\n";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_CUSTOMREQUEST => 'PATCH',
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "Código HTTP: {$httpCode}\n";

if ($error) {
    echo "Erro cURL: {$error}\n";
} else {
    echo "Resposta: " . ($response ?: '(vazia)') . "\n";
    
    if ($response) {
        $decoded = json_decode($response, true);
        if ($decoded) {
            echo "\nDados decodificados:\n";
            print_r($decoded);
        }
    }
}

echo "\nTeste concluído!\n";
?>
