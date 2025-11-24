<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔧 Ajustando patterns para aceitar variações verbais\n\n";

// 1. list_restaurants - aceitar várias formas
DB::table('ai_contexts')
    ->where('key', 'list_restaurants')
    ->update([
        'pattern' => '.*(mostr|list|ver|vej|quais).*(restaurante|loja).*',
        'active' => 1,
        'confidence_threshold' => 0.95,
        'updated_at' => now()
    ]);

echo "✓ list_restaurants atualizado\n";
echo "  Pattern: .*(mostr|list|ver|vej|quais).*(restaurante|loja).*\n";
echo "  Aceita: mostra, mostrar, lista, listar, ver, veja, quais\n\n";

// 2. select_restaurant - aceitar várias formas
DB::table('ai_contexts')
    ->where('key', 'select_restaurant')
    ->update([
        'pattern' => '.*(selecio|escolh|abr|entr|vai).*(restaurante|loja).*',
        'active' => 1,
        'confidence_threshold' => 0.85,
        'updated_at' => now()
    ]);

echo "✓ select_restaurant atualizado\n";
echo "  Pattern: .*(selecio|escolh|abr|entr|vai).*(restaurante|loja).*\n";
echo "  Aceita: seleciona, selecionar, escolhe, escolher, abre, abrir, entra, entrar, vai\n\n";

// 3. select_restaurant_direct - nomes específicos
DB::table('ai_contexts')
    ->where('key', 'select_restaurant_direct')
    ->update([
        'pattern' => '.*(restaurante|loja).*(teste|claudia|dona).*',
        'active' => 1,
        'confidence_threshold' => 0.90,
        'updated_at' => now()
    ]);

echo "✓ select_restaurant_direct atualizado\n";
echo "  Pattern: .*(restaurante|loja).*(teste|claudia|dona).*\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Hierarquia final:\n";
echo "  1. list_restaurants (0.95) - listar/mostrar restaurantes\n";
echo "  2. select_restaurant_direct (0.90) - restaurante teste/claudia\n";
echo "  3. select_restaurant (0.85) - selecionar/abrir restaurante X\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Testar patterns
$tests = [
    'listar restaurantes',
    'mostra restaurantes', 
    'selecionar restaurante',
    'abre restaurante',
    'restaurante teste',
];

echo "Testando patterns:\n";
foreach ($tests as $test) {
    echo "\n'{$test}':\n";
    
    // list_restaurants
    $p1 = '.*(mostr|list|ver|vej|quais).*(restaurante|loja).*';
    $m1 = preg_match('/' . $p1 . '/i', $test);
    echo "  " . ($m1 ? '✅' : '❌') . " list_restaurants\n";
    
    // select_restaurant
    $p2 = '.*(selecio|escolh|abr|entr|vai).*(restaurante|loja).*';
    $m2 = preg_match('/' . $p2 . '/i', $test);
    echo "  " . ($m2 ? '✅' : '❌') . " select_restaurant\n";
    
    // select_restaurant_direct
    $p3 = '.*(restaurante|loja).*(teste|claudia|dona).*';
    $m3 = preg_match('/' . $p3 . '/i', $test);
    echo "  " . ($m3 ? '✅' : '❌') . " select_restaurant_direct\n";
}

echo "\n✅ Patterns atualizados! Teste novamente.\n";
