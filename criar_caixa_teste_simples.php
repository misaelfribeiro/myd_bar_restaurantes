<?php

use Illuminate\Support\Facades\DB;

// Criar caixa básico para teste
try {
    DB::table('caixa')->insert([
        'usuario_id' => 1,
        'data_abertura' => '2025-11-11 08:00:00',
        'saldo_inicial' => 100.00,
        'status' => 'fechado',
        'data_fechamento' => '2025-11-11 18:00:00',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "✅ Caixa de teste criado!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
