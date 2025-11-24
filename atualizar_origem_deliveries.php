<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Delivery;
use App\Models\Empresa;

echo "=== ATUALIZAR ORIGEM DAS DELIVERIES ===\n\n";

$deliveries = Delivery::whereNull('origem_latitude')
    ->orWhereNull('origem_longitude')
    ->get();

echo "📦 Encontradas " . $deliveries->count() . " deliveries sem origem\n\n";

foreach ($deliveries as $delivery) {
    $empresa = Empresa::where('tenant_code', $delivery->tenant_code)->first();
    
    if ($empresa && $empresa->latitude && $empresa->longitude) {
        $delivery->update([
            'origem_latitude' => $empresa->latitude,
            'origem_longitude' => $empresa->longitude
        ]);
        
        echo "✅ Delivery #{$delivery->id}: Origem atualizada ({$empresa->razao_social})\n";
    } else {
        echo "⚠️  Delivery #{$delivery->id}: Empresa sem coordenadas\n";
    }
}

echo "\n=== CONCLUÍDO ===\n";
