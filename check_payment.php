<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Verificando Pagamento ID 2 ===\n\n";

$payment = \App\Models\Payment::find(2);

if ($payment) {
    echo "Payment ID: {$payment->id}\n";
    echo "Status: {$payment->status}\n";
    echo "Paid at: " . ($payment->paid_at ?? 'NULL') . "\n";
    echo "Amount: R$ {$payment->amount}\n";
    echo "Pedido ID: {$payment->pedido_id}\n\n";
    
    $pedido = $payment->pedido;
    echo "=== Pedido ===\n";
    echo "ID: {$pedido->id}\n";
    echo "Número: {$pedido->numero_pedido}\n";
    echo "Status: {$pedido->status}\n";
    echo "Payment Status: " . ($pedido->payment_status ?? 'NULL') . "\n";
    echo "Total: R$ {$pedido->total}\n";
} else {
    echo "❌ Payment ID 2 não encontrado\n";
}

echo "\n=== Todos os Pagamentos ===\n";
$payments = \App\Models\Payment::orderBy('id', 'desc')->limit(5)->get();
foreach ($payments as $p) {
    echo "ID: {$p->id} | Status: {$p->status} | Amount: R$ {$p->amount} | Paid: " . ($p->paid_at ? 'SIM' : 'NÃO') . "\n";
}
