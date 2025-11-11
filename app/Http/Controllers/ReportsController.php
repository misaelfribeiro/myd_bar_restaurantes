<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Usuario;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function getGeneralStats()
    {
        $stats = [
            // Estatísticas gerais
            'total_usuarios' => Usuario::count(),
            'usuarios_ativos_hoje' => AccessLog::where('action', 'login')
                                            ->whereDate('created_at', today())
                                            ->distinct('usuario_id')
                                            ->count(),
            
            // Estatísticas de vendas
            'total_pedidos' => Pedido::count(),
            'pedidos_hoje' => Pedido::whereDate('created_at', today())->count(),
            'faturamento_total' => Pedido::sum('total'),
            'faturamento_hoje' => Pedido::whereDate('created_at', today())->sum('total'),
            'ticket_medio' => Pedido::avg('total'),
            
            // Estatísticas de produtos
            'total_produtos' => Produto::count(),
            'categorias_ativas' => Categoria::count(),
            'produto_mais_vendido' => $this->getProdutoMaisVendido(),
            
            // Estatísticas de mesas
            'total_mesas' => Mesa::count(),
            'mesa_mais_popular' => $this->getMesaMaisPopular(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getSalesChart(Request $request)
    {
        $days = $request->input('days', 30);
        
        $sales = Pedido::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_pedidos'),
                DB::raw('SUM(total) as faturamento')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = $sales->pluck('date')->map(function($date) {
            return date('d/m', strtotime($date));
        });

        return response()->json([
            'success' => true,
            'labels' => $labels,
            'pedidos' => $sales->pluck('total_pedidos'),
            'faturamento' => $sales->pluck('faturamento')
        ]);
    }

    public function getTopProducts()
    {
        $products = DB::table('item_pedidos')
            ->join('produtos', 'item_pedidos.produto_id', '=', 'produtos.id')
            ->join('categorias', 'produtos.categoria_id', '=', 'categorias.id')
            ->select(
                'produtos.nome as produto',
                'categorias.nome as categoria',
                DB::raw('SUM(item_pedidos.quantidade) as total_vendido'),
                DB::raw('SUM(item_pedidos.preco * item_pedidos.quantidade) as receita')
            )
            ->groupBy('produtos.id', 'produtos.nome', 'categorias.nome')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }

    public function getUserActivity()
    {
        $activity = AccessLog::with('usuario')
            ->select('usuario_id', 'email')
            ->selectRaw('COUNT(*) as total_actions')
            ->selectRaw('MAX(created_at) as last_activity')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('usuario_id', 'email')
            ->orderBy('total_actions', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'activity' => $activity
        ]);
    }

    public function getHourlyActivity()
    {
        $hourlyData = [];
        
        // Atividade de pedidos por hora
        $pedidos = Pedido::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();

        // Atividade de login por hora
        $logins = AccessLog::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->where('action', 'login')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();

        // Preencher todas as 24 horas
        for ($i = 0; $i < 24; $i++) {
            $hourlyData[] = [
                'hour' => str_pad($i, 2, '0', STR_PAD_LEFT) . ':00',
                'pedidos' => $pedidos[$i] ?? 0,
                'logins' => $logins[$i] ?? 0
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $hourlyData
        ]);
    }

    public function getFinancialSummary(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $summary = [
            'periodo' => [
                'inicio' => $startDate,
                'fim' => $endDate
            ],
            'faturamento_bruto' => Pedido::whereBetween('created_at', [$startDate, $endDate])
                                        ->sum('total'),
            'total_pedidos' => Pedido::whereBetween('created_at', [$startDate, $endDate])
                                    ->count(),
            'ticket_medio' => Pedido::whereBetween('created_at', [$startDate, $endDate])
                                   ->avg('total'),
            
            // Breakdown por status
            'por_status' => Pedido::whereBetween('created_at', [$startDate, $endDate])
                                 ->select('status')
                                 ->selectRaw('COUNT(*) as count, SUM(total) as revenue')
                                 ->groupBy('status')
                                 ->get(),
                                 
            // Top 5 dias com maior faturamento
            'melhores_dias' => Pedido::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total) as revenue'),
                    DB::raw('COUNT(*) as orders')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('revenue', 'desc')
                ->limit(5)
                ->get()
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary
        ]);
    }

    public function exportReport(Request $request)
    {
        $type = $request->input('type', 'sales');
        $format = $request->input('format', 'json');
        
        $data = [];
        
        switch ($type) {
            case 'sales':
                $data = $this->getSalesData();
                break;
            case 'users':
                $data = $this->getUsersData();
                break;
            case 'products':
                $data = $this->getProductsData();
                break;
        }
        
        if ($format === 'csv') {
            return $this->exportToCsv($data, $type);
        }
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'type' => $type
        ]);
    }

    private function getProdutoMaisVendido()
    {
        return DB::table('item_pedidos')
            ->join('produtos', 'item_pedidos.produto_id', '=', 'produtos.id')
            ->select('produtos.nome')
            ->selectRaw('SUM(item_pedidos.quantidade) as total')
            ->groupBy('produtos.id', 'produtos.nome')
            ->orderBy('total', 'desc')
            ->first();
    }

    private function getMesaMaisPopular()
    {
        return DB::table('pedidos')
            ->join('mesas', 'pedidos.mesa_id', '=', 'mesas.id')
            ->select('mesas.identificador')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('mesas.id', 'mesas.identificador')
            ->orderBy('total', 'desc')
            ->first();
    }

    private function getSalesData()
    {
        return Pedido::with(['mesa', 'usuario'])
            ->select('id', 'mesa_id', 'usuario_id', 'total', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function getUsersData()
    {
        return Usuario::select('id', 'nome', 'email', 'role', 'created_at')
            ->withCount('pedidos')
            ->get();
    }

    private function getProductsData()
    {
        return Produto::with('categoria')
            ->select('id', 'nome', 'preco', 'categoria_id', 'created_at')
            ->get();
    }

    private function exportToCsv($data, $type)
    {
        $filename = "relatorio_{$type}_" . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data, $type) {
            $file = fopen('php://output', 'w');
            
            // Headers do CSV baseado no tipo
            switch ($type) {
                case 'sales':
                    fputcsv($file, ['ID', 'Mesa', 'Usuario', 'Total', 'Status', 'Data']);
                    foreach ($data as $item) {
                        fputcsv($file, [
                            $item->id,
                            $item->mesa->identificador ?? 'N/A',
                            $item->usuario->nome ?? 'N/A',
                            $item->total,
                            $item->status,
                            $item->created_at->format('d/m/Y H:i')
                        ]);
                    }
                    break;
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
