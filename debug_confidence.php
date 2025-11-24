<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\AILearningService;
use App\Models\AIContext;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG CONFIDENCE CALCULATION ===\n\n";

$context = AIContext::where('key', 'add_product_to_cart')->first();

echo "Contexto: {$context->key}\n";
echo "ID: {$context->id}\n";
echo "Success Rate: {$context->success_rate}\n";
echo "Threshold: {$context->confidence_threshold}\n\n";

// Simular cálculo de confiança
$confidence = 0;

// 50% baseado no match do padrão
$confidence += 0.5;
echo "1. Pattern match: +0.5 = {$confidence}\n";

// 25% baseado na taxa de sucesso histórica
$successRate = $context->success_rate > 0 ? $context->success_rate : 0.8;
$confidence += $successRate * 0.25;
echo "2. Success rate ({$successRate}): +" . ($successRate * 0.25) . " = {$confidence}\n";

// 25% baseado na rede neural (simulado)
// OutputSize padrão parece ser 100
$outputSize = 100;
$neuralIndex = $context->id % $outputSize;
$neuralConfidence = 0.5; // Valor padrão
$confidence += $neuralConfidence * 0.25;
echo "3. Neural output (index {$neuralIndex}, valor {$neuralConfidence}): +" . ($neuralConfidence * 0.25) . " = {$confidence}\n\n";

$confidence = min($confidence, 1);

echo "Confiança final: {$confidence}\n";
echo "Threshold requerido: {$context->confidence_threshold}\n";
echo "Passa? " . ($confidence >= $context->confidence_threshold ? 'SIM' : 'NAO') . "\n";
