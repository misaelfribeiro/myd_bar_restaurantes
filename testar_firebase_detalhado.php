<?php
require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\Cache;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Token FCM do dispositivo (substitua pelo token real)
$fcmToken = 'czN2MULAQCmwKqSoCvuE_B:APA91bETP48PSgeHkM8lt4fvWZhIXxewGYs4-2rDDQ_YtxYtnwnrfMw-nxEy8ktK72NXYWk1xVqHJU8bTHss5qUYdvRI7OgPnu1-Qkpbmztg5L0XFq4rjw8';

echo "=== TESTE DETALHADO DE NOTIFICAÇÃO FIREBASE ===\n\n";

// Ler firebase-config.json
$configPath = __DIR__ . '/firebase-config.json';
if (!file_exists($configPath)) {
    die("Arquivo firebase-config.json não encontrado!\n");
}

$credentials = json_decode(file_get_contents($configPath), true);
echo "1. Credenciais carregadas ✓\n";
echo "   - Project: " . $credentials['project_id'] . "\n";
echo "   - Email: " . $credentials['client_email'] . "\n\n";

// Gerar JWT
echo "2. Gerando JWT...\n";
$now = time();
$header = [
    'typ' => 'JWT',
    'alg' => 'RS256',
];
$payload = [
    'iss' => $credentials['client_email'],
    'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
    'aud' => 'https://oauth2.googleapis.com/token',
    'iat' => $now,
    'exp' => $now + 3600,
];

$headerEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($header)));
$payloadEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
$signatureInput = $headerEncoded . '.' . $payloadEncoded;

openssl_sign($signatureInput, $signature, $credentials['private_key'], 'sha256');
$signatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
$jwt = $signatureInput . '.' . $signatureEncoded;
echo "   JWT gerado ✓\n\n";

// Obter Access Token
echo "3. Obtendo Access Token do Google...\n";
$ch = curl_init('https://oauth2.googleapis.com/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
    'assertion' => $jwt,
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    die("Erro ao obter Access Token! HTTP $httpCode\nResposta: $response\n");
}

$tokenData = json_decode($response, true);
$accessToken = $tokenData['access_token'];
echo "   Access Token obtido ✓\n";
echo "   Token: " . substr($accessToken, 0, 50) . "...\n\n";

// Enviar notificação
echo "4. Enviando notificação para o Firebase...\n";
echo "   Token FCM: " . substr($fcmToken, 0, 50) . "...\n\n";

$fcmUrl = 'https://fcm.googleapis.com/v1/projects/' . $credentials['project_id'] . '/messages:send';
$payload = [
    'message' => [
        'token' => $fcmToken,
        'notification' => [
            'title' => 'Teste Detalhado',
            'body' => 'Testando notificação com detalhes completos!',
        ],
        'android' => [
            'priority' => 'high',
            'notification' => [
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ],
    ],
];

$ch = curl_init($fcmUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "=== RESULTADO ===\n";
echo "HTTP Status: $httpCode\n";
echo "Resposta do Firebase:\n";
echo json_encode(json_decode($response), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
echo "\n\n";

if ($httpCode === 200) {
    echo "✅ SUCESSO! Notificação enviada!\n";
} else {
    echo "❌ ERRO! Verifique a resposta acima.\n";
}
