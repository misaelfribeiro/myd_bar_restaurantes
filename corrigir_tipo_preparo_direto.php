<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

echo "=== CORRIGIR TIPO PREPARO DIRETO ===\n";

// Usar SQL direto
$affected = DB::statement("UPDATE produtos SET tipo_preparo = 'nao_precisa' WHERE tipo_preparo = 'pronto'");
echo "Linhas afetadas: " . ($affected ? 'Sucesso' : 'Falhou') . "\n";

$affected2 = DB::statement("UPDATE produtos SET tipo_preparo = 'nao_precisa' WHERE tipo_preparo NOT IN ('nao_precisa', 'preparo_rapido', 'preparo_cozinha')");
echo "Outras correções: " . ($affected2 ? 'Sucesso' : 'Falhou') . "\n";

echo "Verificando valores atuais:\n";
$valores = DB::select("SELECT DISTINCT tipo_preparo FROM produtos");
foreach ($valores as $valor) {
    echo "- {$valor->tipo_preparo}\n";
}

echo "Correção concluída!\n";