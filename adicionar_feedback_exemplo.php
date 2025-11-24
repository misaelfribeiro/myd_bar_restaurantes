<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AITrainingData;
use Illuminate\Support\Facades\DB;

echo "🎯 Adicionando feedbacks de exemplo para teste...\n\n";

// Busca algumas interações existentes
$interactions = AITrainingData::limit(20)->get();

if ($interactions->isEmpty()) {
    echo "❌ Nenhuma interação encontrada. Execute o sistema primeiro.\n";
    exit;
}

$updated = 0;

foreach ($interactions as $interaction) {
    // Gera feedback aleatório baseado na confiança
    $confidence = $interaction->confidence ?? 0.5;
    
    if ($confidence >= 0.8) {
        // Alta confiança = feedback positivo
        $feedback_score = rand(4, 5);
        $correct = true;
    } elseif ($confidence >= 0.5) {
        // Média confiança = feedback neutro/positivo
        $feedback_score = rand(3, 4);
        $correct = rand(0, 1) == 1;
    } else {
        // Baixa confiança = feedback negativo
        $feedback_score = rand(1, 2);
        $correct = false;
    }
    
    $interaction->update([
        'feedback_score' => $feedback_score,
        'correct' => $correct
    ]);
    
    $updated++;
    
    $icon = $feedback_score >= 4 ? '👍' : ($feedback_score >= 3 ? '😐' : '👎');
    echo "{$icon} Feedback {$feedback_score}/5 adicionado para: " . substr($interaction->input, 0, 50) . "...\n";
}

echo "\n✅ Total de {$updated} feedbacks adicionados!\n";
echo "📊 Acesse http://localhost/admin/carla para ver a análise de feedback\n";
