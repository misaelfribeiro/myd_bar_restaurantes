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
}
