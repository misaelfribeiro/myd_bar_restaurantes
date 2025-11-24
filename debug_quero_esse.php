<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Testando pattern 'quero esse'\n\n";

$context = DB::table('ai_contexts')
    ->where('key', 'add_to_cart_confirm')
    ->first();

if ($context) {
    echo "Pattern: {$context->pattern}\n";
    echo "Active: " . ($context->active ? 'SIM' : 'NÃO') . "\n";
    echo "Confidence: {$context->confidence_threshold}\n\n";
    
    // Testar o pattern
    $pattern = str_replace('*', '.*', $context->pattern);
    $test = 'quero esse';
    
    echo "Regex: /{$pattern}/i\n";
    echo "Teste: '{$test}'\n";
    $match = preg_match('/' . $pattern . '/i', $test);
    echo "Match: " . ($match ? 'SIM ✅' : 'NÃO ❌') . "\n\n";
    
    // Decompor o pattern
    echo "Decomposição do pattern:\n";
    echo "  Parte 1 (verbos): (adiciona|adicionar|coloca|colocar|quero|pegar|pega)\n";
    echo "  Parte 2 (pronomes): (esse|essa|este|esta|isso)\n\n";
    
    // Verificar cada parte
    echo "Verificações:\n";
    $parte1 = preg_match('/(adiciona|adicionar|coloca|colocar|quero|pegar|pega)/i', $test);
    echo "  Parte 1 (verbos): " . ($parte1 ? 'MATCH ✅' : 'NO MATCH ❌') . "\n";
    
    $parte2 = preg_match('/(esse|essa|este|esta|isso)/i', $test);
    echo "  Parte 2 (pronomes): " . ($parte2 ? 'MATCH ✅' : 'NO MATCH ❌') . "\n\n";
    
    // O problema: falta espaço entre as partes
    echo "PROBLEMA IDENTIFICADO:\n";
    echo "  Pattern atual: *(verbos)*(pronomes)*\n";
    echo "  Precisa de espaço: *(verbos)* *(pronomes)*\n\n";
    
    echo "Corrigindo pattern...\n";
    $newPattern = '*(adiciona|adicionar|coloca|colocar|quero|pegar|pega)* *(esse|essa|este|esta|isso)*';
    
    DB::table('ai_contexts')
        ->where('key', 'add_to_cart_confirm')
        ->update([
            'pattern' => $newPattern,
            'updated_at' => now()
        ]);
    
    echo "✓ Pattern atualizado: {$newPattern}\n\n";
    
    // Testar novamente
    $pattern = str_replace('*', '.*', $newPattern);
    $match = preg_match('/' . $pattern . '/i', $test);
    echo "Teste novamente: " . ($match ? 'MATCH ✅' : 'NO MATCH ❌') . "\n";
}
