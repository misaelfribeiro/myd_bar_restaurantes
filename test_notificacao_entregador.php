<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Delivery;
use App\Models\Empresa;
use App\Models\Entregador;

echo "=== TESTE DE NOTIFICAÇÃO DE ENTREGADORES ===\n\n";

// Buscar primeira delivery disponível na plataforma
$delivery = Delivery::where('disponivel_plataforma', true)
    ->whereNull('entregador_id')
    ->first();

if (!$delivery) {
    echo "❌ Nenhuma delivery disponível para teste\n";
    exit;
}

echo "📦 Delivery #" . $delivery->id . "\n";
echo "   Status: " . $delivery->status . "\n";
echo "   Origem: " . ($delivery->origem_latitude ? "Lat: {$delivery->origem_latitude}, Long: {$delivery->origem_longitude}" : "NÃO DEFINIDA") . "\n";
echo "   Destino: {$delivery->endereco_bairro}, {$delivery->endereco_cidade}\n";
echo "   Valor Entregador: R$ " . number_format($delivery->valor_entregador, 2, ',', '.') . "\n";
echo "   Raio de busca: " . ($delivery->raio_busca_km ?? '5') . "km\n\n";

// Verificar empresa
$empresa = Empresa::where('tenant_code', $delivery->tenant_code)->first();
if ($empresa) {
    echo "🏪 Empresa: " . $empresa->razao_social . "\n";
    echo "   Localização: " . ($empresa->latitude ? "Lat: {$empresa->latitude}, Long: {$empresa->longitude}" : "NÃO DEFINIDA") . "\n\n";
}

// Buscar entregadores disponíveis
echo "🔍 Buscando entregadores próximos...\n\n";
$entregadores = $delivery->buscarEntregadoresProximos();

echo "👥 Encontrados: " . $entregadores->count() . " entregador(es)\n\n";

foreach ($entregadores as $entregador) {
    $localizacao = $entregador->localizacao_atual;
    echo "   • " . $entregador->nome . " (ID: {$entregador->id})\n";
    echo "     Email: " . $entregador->email . "\n";
    echo "     Status: " . $entregador->status . " | Disponível: " . ($entregador->disponivel ? 'SIM' : 'NÃO') . "\n";
    
    if ($localizacao && isset($localizacao['latitude'])) {
        echo "     Localização: Lat: {$localizacao['latitude']}, Long: {$localizacao['longitude']}\n";
        
        if ($delivery->origem_latitude) {
            $distancia = $delivery->calcularDistancia(
                $delivery->origem_latitude,
                $delivery->origem_longitude,
                $localizacao['latitude'],
                $localizacao['longitude']
            );
            echo "     Distância do restaurante: " . number_format($distancia, 2) . "km\n";
        }
    } else {
        echo "     Localização: NÃO DISPONÍVEL\n";
    }
    
    // Verificar token FCM na tabela entregadores
    echo "     Token FCM: " . ($entregador->device_token ? "✅ REGISTRADO" : "❌ NÃO REGISTRADO") . "\n";
    echo "     Notificações Push: " . ($entregador->notificacoes_push ? "✅ Ativadas" : "❌ Desativadas") . "\n";
    echo "\n";
}

// Testar notificação
if ($entregadores->count() > 0) {
    echo "\n📲 Testando notificação...\n";
    $resultado = $delivery->notificarEntregadores();
    
    if ($resultado['success']) {
        echo "✅ " . $resultado['message'] . "\n";
    } else {
        echo "❌ " . $resultado['message'] . "\n";
    }
} else {
    echo "\n❌ Sem entregadores para notificar\n";
}

echo "\n=== FIM DO TESTE ===\n";
