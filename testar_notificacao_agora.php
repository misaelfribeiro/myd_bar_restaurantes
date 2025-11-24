<?php
// Testar notificação FCM
$token = "flaMbmrzS5yAkbD2WxBRR9:APA91bGATbyFBoz77GMNclAbGaAqr-JLFhczFBUlZr-jY737jrkMwfQNhTYPEa4DxXCpykjrfL4c-JvcJa2zm4aYK_dF@#TT-SG1B6CR4MvCZb10MoCEEsjw#TO";

$data = [
    'token' => $token,
    'titulo' => 'Teste de Notificação',
    'mensagem' => 'Se você viu isso, está funcionando! 🎉'
];

$url = 'http://192.168.15.9/api/notificacao/testar';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: $httpCode\n";
echo "Response:\n";
echo $response;
