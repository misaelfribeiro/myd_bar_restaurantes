<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogsController extends Controller
{
    public function index()
    {
        return view('logs.index');
    }

    public function getLogs(Request $request)
    {
        $query = AccessLog::with('usuario')
                         ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        return response()->json([
            'success' => true,
            'logs' => $logs
        ]);
    }

    public function getStats()
    {
        $stats = [
            'total_logins' => AccessLog::where('action', 'login')->count(),
            'successful_logins' => AccessLog::where('action', 'login')->where('status', 'success')->count(),
            'failed_logins' => AccessLog::where('action', 'login')->where('status', 'failed')->count(),
            'total_api_access' => AccessLog::where('action', 'api_access')->count(),
            'denied_access' => AccessLog::where('status', 'denied')->count(),
            'today_logins' => AccessLog::where('action', 'login')
                                    ->whereDate('created_at', today())
                                    ->count(),
        ];

        // Atividade por hora (últimas 24h)
        $hourlyActivity = AccessLog::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subHours(24))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();

        // Preencher horas faltantes com 0
        for ($i = 0; $i < 24; $i++) {
            if (!isset($hourlyActivity[$i])) {
                $hourlyActivity[$i] = 0;
            }
        }
        ksort($hourlyActivity);

        // Usuários mais ativos
        $activeUsers = AccessLog::select('usuario_id', 'email')
                              ->selectRaw('COUNT(*) as total_actions')
                              ->where('usuario_id', '!=', null)
                              ->where('created_at', '>=', now()->subDays(7))
                              ->groupBy('usuario_id', 'email')
                              ->orderBy('total_actions', 'desc')
                              ->limit(10)
                              ->get();

        // Endpoints mais acessados
        $topEndpoints = AccessLog::select('endpoint', 'method')
                               ->selectRaw('COUNT(*) as count')
                               ->where('endpoint', '!=', null)
                               ->where('created_at', '>=', now()->subDays(7))
                               ->groupBy('endpoint', 'method')
                               ->orderBy('count', 'desc')
                               ->limit(10)
                               ->get();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'hourly_activity' => array_values($hourlyActivity),
            'hourly_labels' => array_keys($hourlyActivity),
            'active_users' => $activeUsers,
            'top_endpoints' => $topEndpoints
        ]);
    }

    public function getSecurityEvents()
    {
        $events = AccessLog::where('status', 'failed')
                          ->orWhere('status', 'denied')
                          ->orderBy('created_at', 'desc')
                          ->limit(20)
                          ->get();

        return response()->json([
            'success' => true,
            'events' => $events
        ]);
    }

    public function clearOldLogs(Request $request)
    {
        $days = $request->input('days', 30);
        
        $deleted = AccessLog::where('created_at', '<', now()->subDays($days))->delete();

        return response()->json([
            'success' => true,
            'message' => "Logs anteriores a {$days} dias foram removidos.",
            'deleted_count' => $deleted
        ]);
    }
}
