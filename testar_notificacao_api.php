<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Request::create('/api/notificacao/testar', 'POST', [
    'token' => 'czN2MULAQCmwKqSoCvuE_B:APA91bETP48PSgeHkM8lt4fvWZhIXxewGYs4-2rDDQ_YtxYtnwnrfMw-nxEy8ktK72NXYWk1xVqHJU8bTHss5qUYdvRI7OgPnu1-Qkpbmztg5L0XFq4rjw8',
    'titulo' => 'Teste via Script',
    'mensagem' => 'Notificação de teste enviada diretamente!'
]);

$response = $kernel->handle($request);

echo "\n=== TESTE DE NOTIFICAÇÃO VIA API ===\n\n";
echo "Status HTTP: " . $response->getStatusCode() . "\n";
echo "Resposta:\n";
echo $response->getContent() . "\n\n";

$kernel->terminate($request, $response);
