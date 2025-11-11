<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Produto;
use App\Models\Mesa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function vendas(Request $request)
    {
        $dataInicio = $request->get('data_inicio', now()->subDays(7));
        $dataFim = $request->get('data_fim', now());
        
        $vendas = Pedido::whereBetween('created_at', [$dataInicio, $dataFim])
                       ->where('status', 'entregue')
                       ->selectRaw('DATE(created_at) as data, COUNT(*) as total_pedidos, SUM(total) as faturamento')
                       ->groupBy('data')
                       ->orderBy('data')
                       ->get();
        
        return response()->json([
            'periodo' => [
                'inicio' => $dataInicio,
                'fim' => $dataFim
            ],
            'vendas' => $vendas,
            'resumo' => [
                'total_pedidos' => $vendas->sum('total_pedidos'),
                'faturamento_total' => $vendas->sum('faturamento'),
                'ticket_medio' => $vendas->sum('total_pedidos') > 0 
                    ? round($vendas->sum('faturamento') / $vendas->sum('total_pedidos'), 2) 
                    : 0
            ]
        ]);
    }
    
    public function mesasPopulares()
    {
        $mesas = Mesa::withCount(['pedidos' => function($query) {
            $query->where('status', 'entregue')
                  ->whereBetween('created_at', [now()->subDays(30), now()]);
        }])
        ->orderBy('pedidos_count', 'desc')
        ->get();
        
        return response()->json($mesas);
    }
    
    public function horariosMovimento()
    {
        $movimentacao = Pedido::selectRaw('HOUR(created_at) as hora, COUNT(*) as total_pedidos')
                             ->whereBetween('created_at', [now()->subDays(30), now()])
                             ->groupBy('hora')
                             ->orderBy('hora')
                             ->get();
        
        return response()->json($movimentacao);
    }
    
    public function statusPedidos()
    {
        $status = Pedido::selectRaw('status, COUNT(*) as total, AVG(total) as ticket_medio')
                       ->groupBy('status')
                       ->get();
        
        return response()->json($status);
    }
}
