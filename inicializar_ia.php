<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AILearningService;

echo "🧠 Inicializando Rede Neural da IA...\n\n";

$aiService = new AILearningService();
$result = $aiService->initializeNetwork();

echo json_encode($result, JSON_PRETTY_PRINT);
echo "\n\n✅ Rede neural pronta para aprender!\n";
