<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\AILearningService;

$ai = new AILearningService();

// Habilitar reflection para ver o matching
$reflection = new ReflectionClass($ai);
$method = $reflection->getMethod('matchPattern');
$method->setAccessible(true);

$messages = [
    'pagar com cartão de crédito',
    'pagar com dinheiro'
];

$allContexts = DB::table('ai_contexts')->where('active', 1)->get();

foreach($messages as $msg) {
    echo "\n=== Mensagem: '$msg' ===\n";
    
    $matches = [];
    foreach($allContexts as $ctx) {
        $confidence = $method->invoke($ai, $msg, $ctx->pattern);
        if ($confidence > 0.6) {
            $matches[] = [
                'key' => $ctx->key,
                'confidence' => $confidence,
                'threshold' => $ctx->confidence_threshold,
                'pattern' => $ctx->pattern
            ];
        }
    }
    
    // Ordenar por confiança
    usort($matches, fn($a, $b) => $b['confidence'] <=> $a['confidence']);
    
    echo "Matches encontrados:\n";
    foreach(array_slice($matches, 0, 5) as $m) {
        $pass = $m['confidence'] >= $m['threshold'] ? '✅' : '❌';
        echo "$pass {$m['key']} - Conf: {$m['confidence']} (threshold: {$m['threshold']})\n";
    }
}
