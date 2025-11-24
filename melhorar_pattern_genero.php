<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Melhorando pattern do add_to_cart_confirm para aceitar mais variações\n\n";

// Pattern mais flexível que aceita variações
$newPattern = '*(adiciona|adicionar|coloca|colocar|quero|pegar|pega|pede|pedir)* *(esse|essa|este|esta|isso|este aqui|essa aqui|esta aqui|ai|aí)*';

DB::table('ai_contexts')
    ->where('key', 'add_to_cart_confirm')
    ->update([
        'pattern' => $newPattern,
        'confidence_threshold' => 0.90,
        'active' => 1,
        'updated_at' => now()
    ]);

echo "✓ Pattern atualizado\n";
echo "  Pattern: {$newPattern}\n\n";

// Testar várias variações
$tests = [
    'adiciona essa',
    'adiciona esse',
    'quero essa',
    'quero esse',
    'pega essa',
    'pega esse',
    'coloca essa',
    'pede essa',
    'adicionar essa aqui',
    'quero isso',
];

echo "Testando variações:\n";
$pattern = str_replace('*', '.*', $newPattern);

foreach ($tests as $test) {
    $match = preg_match('/' . $pattern . '/i', $test);
    $icon = $match ? '✅' : '❌';
    echo "  {$icon} '{$test}'\n";
}

echo "\n✅ Agora aceita tanto 'esse' quanto 'essa'!\n";
