<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AILearningService;
use App\Models\AITrainingData;

echo "🎓 Treinamento da Carla com Dados Históricos...\n\n";

$aiService = new AILearningService();

// Ver quantos dados temos
$totalDados = AITrainingData::count();
$dadosTreinados = AITrainingData::where('used_for_training', true)->count();
$dadosPendentes = AITrainingData::where('used_for_training', false)
    ->where('correct', true)
    ->count();

echo "📊 Estatísticas:\n";
echo "   Total de interações: $totalDados\n";
echo "   Já treinados: $dadosTreinados\n";
echo "   Pendentes (corretos): $dadosPendentes\n\n";

if ($dadosPendentes > 0) {
    echo "🔄 Iniciando treinamento...\n";
    
    $resultado = $aiService->batchTrain(100); // Treina até 100 interações
    
    echo "\n✅ Treinamento concluído!\n";
    echo "   Dados processados: {$resultado['trained_count']}\n";
    
    // Ver estatísticas atualizadas
    echo "\n📈 Estatísticas após treinamento:\n";
    $sinapses = \App\Models\AISynapse::selectRaw('
        AVG(weight) as avg_weight,
        SUM(updates) as total_updates,
        MAX(updates) as max_updates
    ')->first();
    
    echo "   Peso médio das sinapses: " . round($sinapses->avg_weight, 4) . "\n";
    echo "   Total de atualizações: {$sinapses->total_updates}\n";
    echo "   Máximo de updates em uma sinapse: {$sinapses->max_updates}\n";
    
} else {
    echo "⚠️ Nenhum dado pendente para treinar.\n";
    echo "💡 A Carla precisa interagir com usuários primeiro!\n";
}

echo "\n";
