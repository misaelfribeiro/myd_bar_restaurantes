<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Verificando Pagamentos Recentes ===\n\n";

$payments = \App\Models\Payment::with('pedido')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

if ($payments->count() > 0) {
    foreach ($payments as $payment) {
        echo "==========================================\n";
        echo "Payment ID: {$payment->id}\n";
        echo "Pedido ID: {$payment->pedido_id}\n";
        echo "Status: {$payment->status}\n";
        echo "Método: {$payment->payment_method}\n";
        echo "Valor: R$ " . number_format($payment->amount, 2, ',', '.') . "\n";
        echo "Mercado Pago ID: {$payment->mp_payment_id}\n";
        echo "Pago em: " . ($payment->paid_at ? $payment->paid_at->format('d/m/Y H:i:s') : 'Não pago') . "\n";
        echo "Criado em: {$payment->created_at->format('d/m/Y H:i:s')}\n";
        
        if ($payment->pedido) {
            echo "\n--- Pedido Relacionado ---\n";
            echo "Status do Pedido: {$payment->pedido->status}\n";
            echo "Payment Status: {$payment->pedido->payment_status}\n";
            echo "Total: R$ " . number_format($payment->pedido->total, 2, ',', '.') . "\n";
        }
        echo "==========================================\n\n";
    }
} else {
    echo "Nenhum pagamento encontrado.\n";
}

echo "\n=== Status Final ===\n";
$approved = \App\Models\Payment::where('status', 'approved')->count();
$pending = \App\Models\Payment::where('status', 'pending')->count();
echo "✅ Pagamentos Aprovados: {$approved}\n";
echo "⏳ Pagamentos Pendentes: {$pending}\n";
