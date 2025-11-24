<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AILearningService;

echo "=== TESTE COMPLETO: FLUXO DE CHECKOUT POR VOZ ===\n\n";

$ai = new AILearningService();
$userId = 3; // Admin EatsFood
$sessionToken = 'test-session-' . time();

$tests = [
    ['comando' => 'confirma meu endereço', 'esperado' => 'address_data'],
    ['comando' => 'quais as formas de pagamento', 'esperado' => 'payment_methods'],
    ['comando' => 'pagar via pix', 'esperado' => 'payment_selected'],
    ['comando' => 'mudar endereço', 'esperado' => 'navigate_to:address_form'],
    ['comando' => 'finalizar pedido', 'esperado' => 'navigate_to:confirm_order'],
];

$passed = 0;
$failed = 0;

foreach ($tests as $i => $test) {
    $num = $i + 1;
    echo "{$num}️⃣ Testando: \"{$test['comando']}\"\n";
    
    try {
        $result = $ai->processMessage($test['comando'], $sessionToken, $userId);
        
        echo "   🤖 Intent: {$result['intent']}\n";
        echo "   🤖 Action: {$result['action']}\n";
        echo "   🤖 Resposta: " . substr($result['response'], 0, 60) . "...\n";
        
        // Verificar resultado esperado
        $success = false;
        if (strpos($test['esperado'], ':') !== false) {
            // Verificar navigate_to
            list($field, $value) = explode(':', $test['esperado']);
            if (isset($result[$field]) && $result[$field] === $value) {
                echo "   ✅ Navegação: {$result[$field]}\n";
                $success = true;
            }
        } else {
            // Verificar campo exists
            if (isset($result[$test['esperado']])) {
                echo "   ✅ Campo '{$test['esperado']}' retornado\n";
                $success = true;
            }
        }
        
        if ($success) {
            $passed++;
        } else {
            $failed++;
            echo "   ❌ Falhou: esperado '{$test['esperado']}'\n";
        }
        
    } catch (\Exception $e) {
        $failed++;
        echo "   ❌ ERRO: {$e->getMessage()}\n";
    }
    
    echo "\n";
}

echo "=== RESUMO ===\n";
echo "✅ Testes passados: {$passed}/" . count($tests) . "\n";
echo "❌ Testes falhos: {$failed}/" . count($tests) . "\n";
