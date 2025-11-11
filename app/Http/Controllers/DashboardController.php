<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\Usuario;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }
    
    public function stats()
    {
        $stats = [
            'total_categorias' => Categoria::count(),
            'total_produtos' => Produto::count(),
            'total_mesas' => Mesa::count(),
            'total_usuarios' => Usuario::count(),
            'total_pedidos' => Pedido::count(),
            'pedidos_pendentes' => Pedido::where('status', 'pendente')->count(),
            'pedidos_em_preparo' => Pedido::where('status', 'em_preparo')->count(),
            'pedidos_prontos' => Pedido::where('status', 'pronto')->count(),
            'faturamento_hoje' => Pedido::whereDate('created_at', today())
                                      ->where('status', 'entregue')
                                      ->sum('total'),
            'pedidos_hoje' => Pedido::whereDate('created_at', today())->count(),
        ];
        
        return response()->json($stats);
    }
    
    public function pedidosPorStatus()
    {
        $pedidos = Pedido::selectRaw('status, COUNT(*) as total')
                        ->groupBy('status')
                        ->get();
        
        return response()->json($pedidos);
    }
    
    public function produtosMaisVendidos()
    {
        // Esta query funcionará quando tivermos ItemPedido implementado
        $produtos = Produto::withCount(['itensPedido' => function($query) {
            $query->whereHas('pedido', function($q) {
                $q->where('status', 'entregue');
            });
        }])->orderBy('itens_pedido_count', 'desc')->limit(5)->get();
        
        return response()->json($produtos);
    }
}
