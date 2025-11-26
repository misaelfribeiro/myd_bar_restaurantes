<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fatura;
use App\Models\Contrato;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceiroController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        $query = Fatura::with(['contrato.empresa', 'contrato.plano']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_fatura', 'like', "%{$search}%")
                  ->orWhereHas('contrato.empresa', function($eq) use ($search) {
                      $eq->where('nome_fantasia', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('mes')) {
            $query->whereMonth('data_referencia', $request->mes);
        }

        if ($request->filled('ano')) {
            $query->whereYear('data_referencia', $request->ano);
        }

        $faturas = $query->orderBy('data_vencimento', 'desc')->paginate(20);

        // Estatísticas
        $estatisticas = [
            'total_faturado' => Fatura::sum('valor_total'),
            'total_pago' => Fatura::where('status', 'pago')->sum('valor_total'),
            'total_pendente' => Fatura::where('status', 'pendente')->sum('valor_total'),
            'total_vencido' => Fatura::where('status', 'vencido')->sum('valor_total'),
            'total_cancelado' => Fatura::where('status', 'cancelado')->sum('valor_total'),
            'mes_atual' => Fatura::whereMonth('data_referencia', now()->month)
                                ->whereYear('data_referencia', now()->year)
                                ->sum('valor_total'),
            'mes_pago' => Fatura::whereMonth('data_referencia', now()->month)
                               ->whereYear('data_referencia', now()->year)
                               ->where('status', 'pago')
                               ->sum('valor_total'),
        ];

        return view('admin.financeiro.index', compact('faturas', 'estatisticas'));
    }

    /**
     * Show the form for creating a new invoice
     */
    public function create()
    {
        $empresas = Empresa::where('is_master', false)->orderBy('nome_fantasia')->get();
        return view('admin.financeiro.create', compact('empresas'));
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'contrato_id' => 'required|exists:contratos,id',
            'data_referencia' => 'required|date',
            'data_vencimento' => 'required|date',
            'valor_plano' => 'required|numeric|min:0',
            'valor_adicional' => 'nullable|numeric|min:0',
            'valor_desconto' => 'nullable|numeric|min:0',
            'descricao' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $numeroFatura = 'FAT-' . date('Y') . '-' . str_pad(Fatura::count() + 1, 5, '0', STR_PAD_LEFT);

            $valorTotal = $validated['valor_plano'] 
                        + ($validated['valor_adicional'] ?? 0) 
                        - ($validated['valor_desconto'] ?? 0);

            $fatura = Fatura::create([
                'contrato_id' => $validated['contrato_id'],
                'empresa_id' => $validated['empresa_id'],
                'numero_fatura' => $numeroFatura,
                'data_referencia' => $validated['data_referencia'],
                'data_vencimento' => $validated['data_vencimento'],
                'data_emissao' => now(),
                'valor_plano' => $validated['valor_plano'],
                'valor_adicional' => $validated['valor_adicional'] ?? 0,
                'valor_desconto' => $validated['valor_desconto'] ?? 0,
                'valor_total' => $valorTotal,
                'status' => 'pendente',
                'descricao' => $validated['descricao'],
            ]);

            DB::commit();

            return redirect()->route('admin.financeiro.show', $fatura)
                           ->with('success', 'Fatura criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar fatura: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Display the specified invoice
     */
    public function show(Fatura $fatura)
    {
        $fatura->load(['contrato.empresa', 'contrato.plano']);
        return view('admin.financeiro.show', compact('fatura'));
    }

    /**
     * Mark invoice as paid
     */
    public function marcarPago(Request $request, Fatura $fatura)
    {
        $validated = $request->validate([
            'data_pagamento' => 'required|date',
            'forma_pagamento' => 'required|in:boleto,cartao,pix,transferencia',
            'observacoes' => 'nullable|string',
        ]);

        $fatura->update([
            'status' => 'pago',
            'data_pagamento' => $validated['data_pagamento'],
            'forma_pagamento' => $validated['forma_pagamento'],
            'observacoes' => $validated['observacoes'],
        ]);

        return redirect()->route('admin.financeiro.show', $fatura)
                       ->with('success', 'Fatura marcada como paga!');
    }

    /**
     * Cancel invoice
     */
    public function cancelar(Request $request, Fatura $fatura)
    {
        $validated = $request->validate([
            'motivo_cancelamento' => 'required|string',
        ]);

        $fatura->update([
            'status' => 'cancelado',
            'observacoes' => 'Cancelado: ' . $validated['motivo_cancelamento'],
        ]);

        return redirect()->route('admin.financeiro.index')
                       ->with('success', 'Fatura cancelada!');
    }

    /**
     * Financial reports
     */
    public function relatorios(Request $request)
    {
        $ano = $request->ano ?? now()->year;
        $mes = $request->mes;

        // Receita por mês
        $receitaMensal = Fatura::select(
                DB::raw('MONTH(data_referencia) as mes'),
                DB::raw('SUM(valor_total) as total'),
                DB::raw('SUM(CASE WHEN status = "pago" THEN valor_total ELSE 0 END) as pago'),
                DB::raw('SUM(CASE WHEN status = "pendente" THEN valor_total ELSE 0 END) as pendente')
            )
            ->whereYear('data_referencia', $ano)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Receita por plano
        $receitaPorPlano = DB::table('faturas')
            ->join('contratos', 'faturas.contrato_id', '=', 'contratos.id')
            ->join('planos', 'contratos.plano_id', '=', 'planos.id')
            ->select(
                'planos.nome',
                'planos.codigo',
                DB::raw('COUNT(faturas.id) as total_faturas'),
                DB::raw('SUM(faturas.valor_total) as total_faturado'),
                DB::raw('SUM(CASE WHEN faturas.status = "pago" THEN faturas.valor_total ELSE 0 END) as total_pago')
            )
            ->whereYear('faturas.data_referencia', $ano)
            ->groupBy('planos.id', 'planos.nome', 'planos.codigo')
            ->get();

        // Top 10 empresas
        $topEmpresas = DB::table('faturas')
            ->join('empresas', 'faturas.empresa_id', '=', 'empresas.id')
            ->select(
                'empresas.nome_fantasia',
                DB::raw('SUM(faturas.valor_total) as total'),
                DB::raw('COUNT(faturas.id) as qtd_faturas')
            )
            ->whereYear('faturas.data_referencia', $ano)
            ->groupBy('empresas.id', 'empresas.nome_fantasia')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Inadimplência
        $inadimplencia = Fatura::where('status', 'vencido')
            ->whereYear('data_vencimento', $ano)
            ->with('contrato.empresa')
            ->orderBy('data_vencimento')
            ->get();

        return view('admin.financeiro.relatorios', compact(
            'receitaMensal',
            'receitaPorPlano',
            'topEmpresas',
            'inadimplencia',
            'ano',
            'mes'
        ));
    }

    /**
     * Update overdue invoices status
     */
    public function atualizarVencidas()
    {
        $updated = Fatura::where('status', 'pendente')
            ->where('data_vencimento', '<', now())
            ->update(['status' => 'vencido']);

        return back()->with('success', "{$updated} faturas atualizadas para vencidas!");
    }

    /**
     * Get active contracts for an empresa
     */
    public function getContratos($empresaId)
    {
        $contratos = Contrato::with('plano')
            ->where('empresa_id', $empresaId)
            ->where('status', 'ativo')
            ->orderBy('data_inicio', 'desc')
            ->get()
            ->map(function($contrato) {
                return [
                    'id' => $contrato->id,
                    'data_inicio' => \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y'),
                    'data_fim' => \Carbon\Carbon::parse($contrato->data_fim)->format('d/m/Y'),
                    'plano' => [
                        'nome' => $contrato->plano->nome
                    ]
                ];
            });

        return response()->json(['contratos' => $contratos]);
    }

    // ==========================================
    // PAINEL DE PAGAMENTOS (MERCADO PAGO)
    // ==========================================

    /**
     * Dashboard financeiro - vis\u00e3o geral
     */
    public function dashboardPagamentos()
    {
        $mesAtual = now()->startOfMonth();

        // Estat\u00edsticas do m\u00eas atual
        $stats = [
            'total_transacoes' => \App\Models\Payment::where('created_at', '>=', $mesAtual)->count(),
            'valor_aprovado' => \App\Models\Payment::where('status', 'approved')
                ->where('created_at', '>=', $mesAtual)
                ->sum('amount'),
            'transacoes_pendentes' => \App\Models\Payment::where('status', 'pending')
                ->where('created_at', '>=', $mesAtual)
                ->count(),
            'taxa_plataforma' => \App\Models\Payment::where('status', 'approved')
                ->where('created_at', '>=', $mesAtual)
                ->sum('platform_fee'),
            'taxa_entrega' => \App\Models\Payment::where('status', 'approved')
                ->where('created_at', '>=', $mesAtual)
                ->sum('delivery_fee'),
            'liquido_restaurantes' => \App\Models\Payment::where('status', 'approved')
                ->where('created_at', '>=', $mesAtual)
                ->sum('net_amount'),
        ];

        // Top 10 restaurantes
        $top_restaurantes = \App\Models\Payment::select('tenant_code', 
                DB::raw('COUNT(*) as total_transacoes'),
                DB::raw('SUM(amount) as total_faturamento'),
                DB::raw('SUM(platform_fee) as total_taxa'),
                DB::raw('SUM(delivery_fee) as total_entrega'),
                DB::raw('SUM(net_amount) as total_liquido'))
            ->where('status', 'approved')
            ->where('created_at', '>=', $mesAtual)
            ->groupBy('tenant_code')
            ->orderByDesc('total_faturamento')
            ->limit(10)
            ->get();

        // M\u00e9todos de pagamento
        $metodos_pagamento = \App\Models\Payment::select('payment_method',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(amount) as total_valor'))
            ->where('status', 'approved')
            ->where('created_at', '>=', $mesAtual)
            ->groupBy('payment_method')
            ->get();

        return view('admin.financeiro.pagamentos-dashboard', compact('stats', 'top_restaurantes', 'metodos_pagamento'));
    }

    /**
     * Listar todas as transações
     */
    public function listarPagamentos(Request $request)
    {
        $query = \App\Models\Payment::with(['pedido.estornos.solicitante', 'pedido.estornos.aprovador'])->orderBy('created_at', 'desc');

        // Filtros
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->tenant_code) {
            $query->where('tenant_code', $request->tenant_code);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->data_inicio && $request->data_fim) {
            $query->whereBetween('created_at', [$request->data_inicio, $request->data_fim]);
        }

        $pagamentos = $query->paginate(50);

        return view('admin.financeiro.pagamentos-lista', compact('pagamentos'));
    }

    /**
     * Detalhes de um pagamento
     */
    public function detalhesPagamento($id)
    {
        $pagamento = \App\Models\Payment::with('pedido')->findOrFail($id);
        
        return view('admin.financeiro.pagamentos-detalhes', compact('pagamento'));
    }

    /**
     * Estornar pagamento
     */
    public function estornarPagamento(Request $request, $id)
    {
        $request->validate([
            'motivo' => 'required|string|max:500'
        ]);

        $pagamento = \App\Models\Payment::findOrFail($id);

        if ($pagamento->status !== 'approved') {
            return back()->with('error', 'Apenas pagamentos aprovados podem ser estornados');
        }

        try {
            // Se tiver mp_payment_id, tentar estornar no Mercado Pago
            if ($pagamento->mp_payment_id) {
                \MercadoPago\SDK::setAccessToken(config('services.mercadopago.access_token'));
                $mpPayment = \MercadoPago\Payment::find_by_id($pagamento->mp_payment_id);
                $mpPayment->refund();
            }

            // Atualizar status local
            $pagamento->status = 'refunded';
            $pagamento->refund_reason = $request->motivo;
            $pagamento->save();

            \Log::info("\ud83d\udcb8 Estorno processado pelo admin - Payment #{$id} | Motivo: {$request->motivo}");

            return back()->with('success', 'Pagamento estornado com sucesso');

        } catch (\Exception $e) {
            \Log::error("Erro ao estornar pagamento #{$id}: " . $e->getMessage());
            return back()->with('error', 'Erro ao processar estorno: ' . $e->getMessage());
        }
    }

    /**
     * Relat\u00f3rio financeiro por per\u00edodo
     */
    public function relatorioPagamentos(Request $request)
    {
        $dataInicio = $request->data_inicio ?? now()->startOfMonth();
        $dataFim = $request->data_fim ?? now()->endOfMonth();

        $relatorio = [
            'periodo' => [
                'inicio' => $dataInicio,
                'fim' => $dataFim
            ],
            'totais' => [
                'transacoes' => \App\Models\Payment::whereBetween('created_at', [$dataInicio, $dataFim])->count(),
                'aprovadas' => \App\Models\Payment::where('status', 'approved')
                    ->whereBetween('created_at', [$dataInicio, $dataFim])
                    ->count(),
                'valor_total' => \App\Models\Payment::where('status', 'approved')
                    ->whereBetween('created_at', [$dataInicio, $dataFim])
                    ->sum('amount'),
                'taxa_plataforma' => \App\Models\Payment::where('status', 'approved')
                    ->whereBetween('created_at', [$dataInicio, $dataFim])
                    ->sum('platform_fee'),
                'taxa_entrega' => \App\Models\Payment::where('status', 'approved')
                    ->whereBetween('created_at', [$dataInicio, $dataFim])
                    ->sum('delivery_fee'),
                'valor_restaurantes' => \App\Models\Payment::where('status', 'approved')
                    ->whereBetween('created_at', [$dataInicio, $dataFim])
                    ->sum('net_amount'),
            ],
            'por_restaurante' => \App\Models\Payment::select('tenant_code',
                    DB::raw('COUNT(*) as total_transacoes'),
                    DB::raw('SUM(amount) as total_valor'),
                    DB::raw('SUM(platform_fee) as total_taxa'),
                    DB::raw('SUM(delivery_fee) as total_entrega'),
                    DB::raw('SUM(net_amount) as total_liquido'))
                ->where('status', 'approved')
                ->whereBetween('created_at', [$dataInicio, $dataFim])
                ->groupBy('tenant_code')
                ->orderByDesc('total_valor')
                ->get()
        ];

        return view('admin.financeiro.pagamentos-relatorio', compact('relatorio'));
    }
}
