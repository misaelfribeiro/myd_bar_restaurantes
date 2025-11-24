<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Ajustando patterns...\n\n";

// Ajustar confirm_address para ser mais específico
DB::table('ai_contexts')
    ->where('key', 'confirm_address')
    ->update([
        'pattern' => '*(confirma|confere|verifica|checa|ver|mostra|qual|como|esta|está)*(meu|minha)*(endereço|endereco|local|localizaçao|localizacao|entrega|endereço de entrega)*',
        'confidence_threshold' => 0.80
    ]);
echo "✅ confirm_address atualizado\n";

// Ajustar select_payment_pix
$params = json_encode(['payment_method' => 'pix']);
DB::table('ai_contexts')
    ->where('key', 'select_payment_pix')
    ->update([
        'parameters' => $params
    ]);
echo "✅ select_payment_pix parâmetros atualizados: {$params}\n";

// Fazer o mesmo para os outros
$paramsCard = json_encode(['payment_method' => 'card']);
DB::table('ai_contexts')
    ->where('key', 'select_payment_card')
    ->update([
        'parameters' => $paramsCard
    ]);
echo "✅ select_payment_card parâmetros atualizados\n";

$paramsMoney = json_encode(['payment_method' => 'money']);
DB::table('ai_contexts')
    ->where('key', 'select_payment_money')
    ->update([
        'parameters' => $paramsMoney
    ]);
echo "✅ select_payment_money parâmetros atualizados\n";

echo "\n✅ Patterns e parâmetros ajustados!\n";
