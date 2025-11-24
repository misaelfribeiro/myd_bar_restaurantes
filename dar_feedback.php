<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AILearningService;
use App\Models\AITrainingData;

echo "📝 Feedback Manual de Treinamento\n\n";

// Listar últimas 10 interações
$interacoes = AITrainingData::orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

if ($interacoes->isEmpty()) {
    echo "⚠️ Ainda não há interações registradas.\n";
    echo "💡 Use a Carla primeiro para gerar dados!\n";
    exit;
}

echo "📊 Últimas 10 interações:\n\n";

foreach ($interacoes as $i => $interacao) {
    $numero = $i + 1;
    $id = $interacao->id;
    $input = $interacao->input;
    $output = substr($interacao->actual_output ?? 'sem resposta', 0, 60);
    $intent = $interacao->intent;
    $confidence = round($interacao->confidence * 100, 1);
    $correct = $interacao->correct ? '✅' : '❌';
    $treinado = $interacao->used_for_training ? '[TREINADO]' : '[PENDENTE]';
    
    echo "$numero. ID: $id $treinado $correct\n";
    echo "   Entrada: \"$input\"\n";
    echo "   Resposta: \"$output...\"\n";
    echo "   Intent: $intent | Confiança: $confidence%\n\n";
}

echo "\n🎯 Para dar feedback:\n";
echo "Edite o script e use:\n\n";
echo "   \$aiService = new AILearningService();\n";
echo "   \$aiService->learnFromFeedback(\$trainingDataId, \$correct, \$feedbackScore);\n\n";
echo "Exemplo:\n";
echo "   // Marcar interação ID 5 como incorreta\n";
echo "   \$aiService->learnFromFeedback(5, false, 2);\n";
echo "   // Isso fará backpropagation e ajustará os pesos!\n\n";

// Exemplo de uso (descomente para usar):
/*
echo "🔧 Aplicando feedback de exemplo...\n";
$aiService = new AILearningService();

// Marcar primeira interação como correta com score 5
if ($interacoes->count() > 0) {
    $primeiraId = $interacoes->first()->id;
    $resultado = $aiService->learnFromFeedback($primeiraId, true, 5);
    echo "✅ Feedback aplicado na interação ID $primeiraId\n";
}
*/
