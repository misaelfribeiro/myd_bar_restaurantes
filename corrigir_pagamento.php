<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Corrigindo Status do Pagamento #3 ===\n\n";

$payment = \App\Models\Payment::find(3);

if ($payment) {
    echo "Status atual: {$payment->status}\n";
    echo "Paid at: " . ($payment->paid_at ? $payment->paid_at->format('d/m/Y H:i:s') : 'null') . "\n\n";
    
    // Corrigir status
    $payment->status = 'approved';
    if (!$payment->paid_at) {
        $payment->paid_at = now();
    }
    $payment->save();
    
    // Atualizar pedido
    $pedido = $payment->pedido;
    $pedido->status = 'confirmado';
    $pedido->payment_status = 'paid';
    $pedido->save();
    
    echo "✅ Pagamento corrigido!\n";
    echo "Novo status: {$payment->status}\n";
    echo "Paid at: {$payment->paid_at->format('d/m/Y H:i:s')}\n";
    echo "Status do pedido: {$pedido->status}\n";
    echo "Payment status: {$pedido->payment_status}\n";
} else {
    echo "❌ Pagamento não encontrado\n";
}
