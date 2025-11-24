<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AILearningService;
use App\Models\AIContext;

echo "🧪 Testando IA...\n\n";

// Ver contextos ativos
$contexts = AIContext::where('active', true)->get();
echo "📚 Contextos ativos: " . $contexts->count() . "\n";
foreach ($contexts->take(3) as $ctx) {
    echo "  - {$ctx->key}: {$ctx->pattern}\n";
}
echo "\n";

try {
    $aiService = new AILearningService();
    $result = $aiService->processMessage('oi', null, null);
    
    echo "✅ Resposta:\n";
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n";
    
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
