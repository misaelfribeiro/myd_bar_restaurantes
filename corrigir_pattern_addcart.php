<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Corrigindo pattern do add_to_cart_confirm\n\n";

// O método matches() substitui * por .*
// Então devemos usar * ao invés de .*
$newPattern = '*(adiciona|adicionar|coloca|colocar|quero|pegar|pega)*(esse|essa|este|esta|isso)*';

DB::table('ai_contexts')
    ->where('key', 'add_to_cart_confirm')
    ->update([
        'pattern' => $newPattern,
        'confidence_threshold' => 0.90,
        'active' => 1,
        'updated_at' => now()
    ]);

echo "✓ Pattern atualizado para: {$newPattern}\n\n";

// Testar
$testMessages = [
    'adiciona essa',
    'adiciona esse',
    'quero esse',
    'coloca essa',
    'pega esse',
    'adicionar isso'
];

echo "Testando pattern:\n";
$pattern = str_replace('*', '.*', $newPattern);

foreach ($testMessages as $msg) {
    $matches = @preg_match('/' . $pattern . '/i', $msg);
    $icon = $matches ? '✅' : '❌';
    echo "  {$icon} '{$msg}'\n";
}

echo "\n✅ Pattern corrigido! Agora teste: 'adiciona essa'\n";
