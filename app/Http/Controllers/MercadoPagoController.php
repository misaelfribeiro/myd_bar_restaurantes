<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Pagamento;
use App\Models\Pedido;
use App\Models\Empresa;
use MercadoPago\SDK;
use MercadoPago\Payment as MPPayment;

class MercadoPagoController extends Controller
{
    public function __construct()
    {
        // Configurar SDK do Mercado Pago
        SDK::setAccessToken(config('services.mercadopago.access_token'));
    }

    /**
     * Criar pagamento PIX
     */
    public function createPixPayment(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required_without:pedido_id|numeric|min:0.01',
                'pedido_id' => 'nullable|exists:pedidos,id',
                'email' => 'required|email',
                'cpf' => 'nullable|string',
                'description' => 'nullable|string',
                'pedido_data' => 'nullable|string' // JSON com dados para criar pedido após aprovação
            ]);

            // Se tem pedido_id, busca o pedido existente
            if ($request->pedido_id) {
                $pedido = Pedido::findOrFail($request->pedido_id);
                
                // Verificar se já existe pagamento pendente
                $existingPayment = Payment::where('pedido_id', $pedido->id)
                    ->whereIn('status', ['pending', 'approved', 'in_process'])
                    ->first();
                    
                if ($existingPayment && $existingPayment->isApproved()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este pedido já possui um pagamento aprovado'
                    ], 400);
                }

                $amount = $pedido->total;
                $tenantCode = $pedido->tenant_code;
                $description = "Pedido #{$pedido->id} - EATSFOOD";
            } else {
                // Pagamento SEM pedido (será criado após aprovação)
                $amount = $request->amount;
                $tenantCode = null; // Será definido quando criar o pedido
                $description = $request->description ?? "Pagamento via PIX - EATSFOOD";
            }

            // Buscar taxa da empresa (se tiver tenant_code)
            $taxaPlataforma = 10;
            if ($tenantCode) {
                $empresa = Empresa::where('tenant_code', $tenantCode)->first();
                $taxaPlataforma = $empresa->taxa_servico_plataforma ?? 10;
            }
            
            // Calcular taxas
            $deliveryFee = 0;
            if (isset($pedido)) {
                $deliveryFee = $pedido->taxa_entrega ?? 0;
            }
            $platformFee = $amount * ($taxaPlataforma / 100);
            $netAmount = $amount - $platformFee - $deliveryFee;

            // Criar pagamento no Mercado Pago
            $payment = new MPPayment();
            $payment->transaction_amount = floatval($amount);
            $payment->description = $description;
            $payment->payment_method_id = "pix";
            $payment->payer = [
                "email" => $request->email,
                "first_name" => "Cliente",
                "identification" => [
                    "type" => "CPF",
                    "number" => $request->cpf ?? "00000000000"
                ]
            ];
            
            // Metadata para identificação
            $metadata = [
                'pedido_id' => $request->pedido_id ?? null,
                'tenant_code' => $tenantCode,
                'platform' => 'EATSFOOD'
            ];
            
            // Se tem dados do pedido para criar depois, adiciona ao metadata
            if ($request->pedido_data) {
                $metadata['pedido_data'] = $request->pedido_data;
            }
            
            // Salvar pagamento no MP
            $payment->save();

            if ($payment->status === 'pending' && isset($payment->point_of_interaction)) {
                // Salvar na tabela local (payments)
                $localPayment = Payment::create([
                    'pedido_id' => $request->pedido_id ?? null, // NULL se ainda não criou pedido
                    'numero_pedido' => isset($pedido) ? $pedido->numero_pedido : null,
                    'tenant_code' => $tenantCode,
                    'mp_payment_id' => $payment->id,
                    'payment_method' => 'pix',
                    'status' => 'pending',
                    'amount' => $amount,
                    'platform_fee' => $platformFee,
                    'gateway_fee' => 0,
                    'delivery_fee' => $deliveryFee,
                    'net_amount' => $netAmount,
                    'pix_qr_code' => $payment->point_of_interaction->transaction_data->qr_code_base64 ?? null,
                    'pix_qr_code_url' => $payment->point_of_interaction->transaction_data->ticket_url ?? null,
                    'pix_copy_paste' => $payment->point_of_interaction->transaction_data->qr_code ?? null,
                    'expires_at' => now()->addMinutes(30),
                    'mp_response' => json_encode($payment)
                ]);

                return response()->json([
                    'success' => true,
                    'payment' => [
                        'id' => $localPayment->id,
                        'mp_payment_id' => $payment->id,
                        'status' => 'pending',
                        'amount' => $amount,
                        'pix' => [
                            'qr_code' => $localPayment->pix_qr_code,
                            'qr_code_url' => $localPayment->pix_qr_code_url,
                            'copy_paste' => $localPayment->pix_copy_paste,
                        ],
                        'expires_at' => $localPayment->expires_at->toIso8601String()
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar pagamento PIX',
                'details' => $payment
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Erro ao criar pagamento PIX: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar pagamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook do Mercado Pago
     */
    public function webhook(Request $request)
    {
        try {
            \Log::info('Webhook recebido do Mercado Pago:', $request->all());

            $type = $request->input('type');
            $dataId = $request->input('data.id');

            if ($type === 'payment') {
                // Buscar informações do pagamento no MP
                $payment = MPPayment::find_by_id($dataId);
                
                if ($payment) {
                    // Buscar pagamento local
                    $localPayment = Payment::where('mp_payment_id', $payment->id)->first();
                    
                    if ($localPayment) {
                        // Atualizar status do pagamento (tabela payments)
                        $localPayment->status = $payment->status;
                        $localPayment->mp_response = json_encode($payment);
                        
                        if ($payment->status === 'approved') {
                            $localPayment->paid_at = now();
                            $localPayment->save();
                            
                            \Log::info("💰 Pagamento aprovado - MP Payment ID: {$payment->id}");
                            
                            // SE NÃO TEM PEDIDO AINDA, CRIAR AGORA!
                            if (!$localPayment->pedido_id && isset($payment->metadata->pedido_data)) {
                                \Log::info("📝 Criando pedido após aprovação do pagamento...");
                                
                                try {
                                    $pedidoData = json_decode($payment->metadata->pedido_data, true);
                                    
                                    // Criar pedido
                                    $pedido = Pedido::create([
                                        'mesa_id' => null,
                                        'usuario_id' => 1, // Sistema
                                        'cliente_id' => $pedidoData['cliente_id'] ?? null,
                                        'status' => 'aberto',
                                        'total' => $localPayment->amount,
                                        'observacoes' => $pedidoData['observacoes'] ?? null,
                                        'tenant_code' => $pedidoData['tenant_code'] ?? $localPayment->tenant_code
                                    ]);
                                    
                                    // Criar itens do pedido
                                    foreach ($pedidoData['itens'] as $item) {
                                        \App\Models\ItemPedido::create([
                                            'pedido_id' => $pedido->id,
                                            'produto_id' => $item['produto_id'],
                                            'tipo_item' => 'produto',
                                            'quantidade' => $item['quantidade'],
                                            'preco_unitario' => $item['preco_unitario'],
                                            'subtotal' => $item['quantidade'] * $item['preco_unitario']
                                        ]);
                                    }
                                    
                                    // Criar delivery
                                    \App\Models\Delivery::create([
                                        'pedido_id' => $pedido->id,
                                        'cliente_id' => $pedidoData['cliente_id'] ?? null,
                                        'cliente_nome' => $pedidoData['cliente_nome'],
                                        'cliente_telefone' => $pedidoData['cliente_telefone'],
                                        'endereco_rua' => $pedidoData['cliente_endereco'],
                                        'endereco_numero' => $pedidoData['endereco_numero'] ?? 'S/N',
                                        'endereco_bairro' => $pedidoData['cliente_bairro'],
                                        'endereco_cidade' => 'Cidade',
                                        'endereco_cep' => '00000-000',
                                        'taxa_entrega' => $localPayment->delivery_fee,
                                        'tempo_estimado' => 45,
                                        'status' => 'pendente',
                                        'tenant_code' => $pedidoData['tenant_code'] ?? $localPayment->tenant_code
                                    ]);
                                    
                                    // Atualizar payment com pedido_id
                                    $localPayment->pedido_id = $pedido->id;
                                    $localPayment->numero_pedido = $pedido->numero_pedido ?? $pedido->id;
                                    $localPayment->save();
                                    
                                    \Log::info("✅ Pedido #{$pedido->id} criado após aprovação do pagamento!");
                                    
                                } catch (\Exception $e) {
                                    \Log::error("❌ Erro ao criar pedido após pagamento: " . $e->getMessage());
                                }
                            }
                            
                            // POPULAR TABELA PAGAMENTOS (operacional)
                            if ($localPayment->pedido_id) {
                                $pedido = Pedido::find($localPayment->pedido_id);
                                if ($pedido) {
                                    // Verificar se já existe registro na tabela pagamentos
                                    $pagamentoExistente = Pagamento::where('pedido_id', $pedido->id)
                                        ->where('forma_pagamento', 'pix')
                                        ->first();
                                        
                                    if (!$pagamentoExistente) {
                                        Pagamento::create([
                                            'pedido_id' => $pedido->id,
                                            'forma_pagamento' => 'pix',
                                            'valor' => $localPayment->amount,
                                            'valor_recebido' => $localPayment->amount,
                                            'troco' => 0,
                                            'status' => 'confirmado',
                                            'observacoes' => 'Pagamento via Mercado Pago PIX - ID: ' . $localPayment->mp_payment_id,
                                            'usuario_id' => 1,
                                            'data_pagamento' => now()
                                        ]);
                                        
                                        \Log::info("✅ Registro criado na tabela pagamentos para pedido #{$pedido->id}");
                                    }
                                }
                            }
                        } else {
                            $localPayment->save();
                        }
                        // NÃO altera status do pedido - pagamento é apenas controle financeiro
                    }
                }
            }

            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            \Log::error('Erro no webhook Mercado Pago: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Consultar status do pagamento
     */
    public function checkPaymentStatus($paymentId)
    {
        try {
            $localPayment = Payment::findOrFail($paymentId);
            
            // Se já está aprovado localmente, não buscar do MP (modo teste)
            if ($localPayment->status === 'approved') {
                return response()->json([
                    'success' => true,
                    'payment' => [
                        'id' => $localPayment->id,
                        'status' => $localPayment->status,
                        'amount' => $localPayment->amount,
                        'paid_at' => $localPayment->paid_at,
                        'is_approved' => true,
                        'is_pending' => false,
                        'platform_fee' => $localPayment->platform_fee,
                        'net_amount' => $localPayment->net_amount
                    ]
                ]);
            }
            
            // Buscar status atualizado no MP apenas se pendente
            if ($localPayment->mp_payment_id && $localPayment->status === 'pending') {
                try {
                    $mpPayment = MPPayment::find_by_id($localPayment->mp_payment_id);
                    
                    if ($mpPayment) {
                        $localPayment->status = $mpPayment->status;
                        $localPayment->mp_response = json_encode($mpPayment);
                        
                        if ($mpPayment->status === 'approved' && !$localPayment->paid_at) {
                            $localPayment->paid_at = now();
                        }
                        
                        $localPayment->save();
                    }
                } catch (\Exception $e) {
                    // Se falhar buscar do MP (modo teste), usa dados locais
                    \Log::warning("Não foi possível consultar MP (modo teste): " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'payment' => [
                    'id' => $localPayment->id,
                    'status' => $localPayment->status,
                    'amount' => $localPayment->amount,
                    'paid_at' => $localPayment->paid_at,
                    'is_approved' => $localPayment->isApproved(),
                    'is_pending' => $localPayment->isPending()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar pagamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancelar/Estornar pagamento
     */
    public function refundPayment(Request $request, $paymentId)
    {
        try {
            $localPayment = Payment::findOrFail($paymentId);
            
            if (!$localPayment->isApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Apenas pagamentos aprovados podem ser estornados'
                ], 400);
            }

            // Buscar pagamento no MP e estornar
            $mpPayment = MPPayment::find_by_id($localPayment->mp_payment_id);
            $refund = $mpPayment->refund();

            if ($refund) {
                $localPayment->status = 'refunded';
                $localPayment->refund_reason = $request->input('reason', 'Estorno solicitado');
                $localPayment->save();

                \Log::info("💸 Estorno realizado - Payment #{$paymentId} | Pedido: {$localPayment->numero_pedido}");
                // NÃO cancela pedido automaticamente - estorno é apenas financeiro

                return response()->json([
                    'success' => true,
                    'message' => 'Pagamento estornado com sucesso'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar estorno'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao estornar pagamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Estornar valor parcial de um pagamento
     */
    public function partialRefund(Request $request, $pedidoId)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'reason' => 'required|string|max:500'
            ]);

            $amount = $request->input('amount');
            $reason = $request->input('reason');

            // Buscar pagamento aprovado do pedido
            $localPayment = Payment::where('pedido_id', $pedidoId)
                ->where('status', 'approved')
                ->orderBy('paid_at', 'desc')
                ->first();

            if (!$localPayment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum pagamento aprovado encontrado para este pedido'
                ], 404);
            }

            if ($amount > $localPayment->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Valor do estorno não pode ser maior que o valor pago'
                ], 400);
            }

            // Processar estorno parcial no Mercado Pago
            $mpPayment = MPPayment::find_by_id($localPayment->mp_payment_id);
            
            // Estorno parcial
            $refund = new \MercadoPago\Refund();
            $refund->payment_id = $localPayment->mp_payment_id;
            $refund->amount = floatval($amount);
            $refund->save();

            if ($refund->status === 'approved') {
                // Atualizar status para refunded parcial se estornou tudo, senão manter approved
                if ($amount >= $localPayment->amount) {
                    $localPayment->status = 'refunded';
                }
                
                $localPayment->refund_reason = $reason . " - Valor: R$ " . number_format($amount, 2, ',', '.');
                $localPayment->save();

                \Log::info("💸 Estorno PARCIAL realizado - Payment #{$localPayment->id} | Pedido: #{$pedidoId} | Valor: R$ {$amount}");

                return response()->json([
                    'success' => true,
                    'message' => 'Estorno parcial processado com sucesso',
                    'refund_id' => $refund->id,
                    'amount' => $amount,
                    'mp_refund_status' => $refund->status
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar estorno no Mercado Pago',
                'mp_status' => $refund->status ?? 'unknown'
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Erro ao processar estorno parcial: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar estorno parcial',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simular aprovação de pagamento (apenas para testes)
     */
    public function simulateApproval($paymentId)
    {
        try {
            $localPayment = Payment::findOrFail($paymentId);
            
            if ($localPayment->isApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este pagamento já está aprovado'
                ], 400);
            }

            // Simular aprovação do pagamento
            $localPayment->status = 'approved';
            $localPayment->paid_at = now();
            $localPayment->save();

            \Log::info("💰 Pagamento #{$paymentId} aprovado (TESTE) - Pedido #{$localPayment->pedido_id} | Número: {$localPayment->numero_pedido}");
            
            // POPULAR TABELA PAGAMENTOS (operacional)
            $pedido = Pedido::find($localPayment->pedido_id);
            if ($pedido) {
                // Verificar se já existe registro na tabela pagamentos
                $pagamentoExistente = Pagamento::where('pedido_id', $pedido->id)
                    ->where('forma_pagamento', 'pix')
                    ->first();
                    
                if (!$pagamentoExistente) {
                    Pagamento::create([
                        'pedido_id' => $pedido->id,
                        'forma_pagamento' => 'pix',
                        'valor' => $localPayment->amount,
                        'valor_recebido' => $localPayment->amount,
                        'troco' => 0,
                        'status' => 'confirmado',
                        'observacoes' => 'Pagamento via Mercado Pago PIX (TESTE) - ID: ' . ($localPayment->mp_payment_id ?? 'simulado'),
                        'usuario_id' => 1, // Sistema
                        'data_pagamento' => now()
                    ]);
                    
                    \Log::info("✅ Registro criado na tabela pagamentos para pedido #{$pedido->id}");
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Pagamento aprovado com sucesso (simulação)',
                'payment' => [
                    'id' => $localPayment->id,
                    'status' => 'approved',
                    'paid_at' => $localPayment->paid_at->toIso8601String(),
                    'pedido_id' => $localPayment->pedido_id,
                    'numero_pedido' => $localPayment->numero_pedido,
                    'amount' => $localPayment->amount,
                    'platform_fee' => $localPayment->platform_fee,
                    'net_amount' => $localPayment->net_amount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao simular aprovação',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
