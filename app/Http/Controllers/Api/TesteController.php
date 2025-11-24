<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class TesteController extends Controller
{
 public function testarConexao()
 {
 try {
 $conectado = \Illuminate\Support\Facades\DB::connection()->getPdo();
 $resultado = \Illuminate\Support\Facades\DB::select('SELECT 1 as teste');
 $totalMesas = \App\Models\Mesa::count();
 return response()->json([
 'success' => true,
 'message' => 'Conexão com banco funcionando',
 'data' => [
 'pdo_conectado' => !is_null($conectado),
 'query_teste' => $resultado[0]->teste,
 'total_mesas' => $totalMesas,
 'timestamp' => now()
 ]
 ]);
 } catch (\Exception $e) {
 return response()->json([
 'success' => false,
 'message' => 'Erro na conexão com banco',
 'error' => $e->getMessage(),
 'trace' => $e->getTraceAsString()
 ], 500);
 }
 }
}