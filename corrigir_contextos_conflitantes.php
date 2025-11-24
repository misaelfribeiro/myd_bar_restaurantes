<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Iniciando correcao...\n";

// 1. Desativar reference_that (pattern muito genérico)
DB::table('ai_contexts')
    ->where('key', 'reference_that')
    ->update([
        'active' => 0,
        'updated_at' => now()
    ]);

echo "✓ reference_that desativado\n";

// 2. Reativar add_more_context com pattern específico
DB::table('ai_contexts')
    ->where('key', 'add_more_context')
    ->update([
        'pattern' => '.*(^| )(e |também |mais um|outro |outra ).*',
        'active' => 1,
        'confidence_threshold' => 0.50,
        'updated_at' => now()
    ]);

echo "✓ add_more_context reativado com pattern específico\n";

// 3. Garantir que list_restaurants está ativo
DB::table('ai_contexts')
    ->where('key', 'list_restaurants')
    ->update([
        'active' => 1,
        'confidence_threshold' => 0.95,
        'updated_at' => now()
    ]);

echo "✓ list_restaurants garantido ativo (confidence 0.95)\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Resumo:\n";
echo "  ✅ list_restaurants (0.95) - ativo\n";
echo "  ✅ add_more_context (0.50) - ativo com pattern específico\n";
echo "  ❌ reference_that - desativado\n";
echo "  ❌ Restaurante genérico - desativado\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "✅ Teste agora: 'listar restaurantes'\n";
