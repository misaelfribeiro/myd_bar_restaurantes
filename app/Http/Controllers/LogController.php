<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
class LogController extends Controller
{
 public function laravel()
 {
 try {
 $logFile = storage_path('logs/laravel.log');
 if (!File::exists($logFile)) {
 return response('Nenhum log do Laravel encontrado', 200);
 }
 $lines = $this->tailFile($logFile, 50);
 return response(implode("\n", $lines), 200)
 ->header('Content-Type', 'text/plain; charset=utf-8');
 } catch (\Exception $e) {
 return response('Erro ao ler logs do Laravel: ' . $e->getMessage(), 500);
 }
 }
 public function apache()
 {
 try {
 $possibleLogs = [
 'C:/xampp/apache/logs/error.log',
 'C:/xampp/apache/logs/myd-error.log',
 '/var/log/apache2/error.log',
 '/var/log/httpd/error_log'
 ];
 $logFile = null;
 foreach ($possibleLogs as $path) {
 if (File::exists($path)) {
 $logFile = $path;
 break;
 }
 }
 if (!$logFile) {
 return response('Arquivo de log do Apache não encontrado', 200);
 }
 $lines = $this->tailFile($logFile, 30);
 return response(implode("\n", $lines), 200)
 ->header('Content-Type', 'text/plain; charset=utf-8');
 } catch (\Exception $e) {
 return response('Erro ao ler logs do Apache: ' . $e->getMessage(), 500);
 }
 }
 public function requests()
 {
 try {
 $logs = [
 [
 'timestamp' => now()->subMinutes(1)->format('H:i:s'),
 'method' => 'GET',
 'url' => '/api/dashboard/stats',
 'status' => 200
 ],
 [
 'timestamp' => now()->subMinutes(2)->format('H:i:s'),
 'method' => 'POST',
 'url' => '/api/auth/login',
 'status' => 200
 ],
 [
 'timestamp' => now()->subMinutes(3)->format('H:i:s'),
 'method' => 'GET',
 'url' => '/api/produtos',
 'status' => 200
 ],
 [
 'timestamp' => now()->subMinutes(4)->format('H:i:s'),
 'method' => 'GET',
 'url' => '/api/pedidos',
 'status' => 200
 ],
 [
 'timestamp' => now()->subMinutes(5)->format('H:i:s'),
 'method' => 'POST',
 'url' => '/api/pedidos',
 'status' => 201
 ],
 [
 'timestamp' => now()->subMinutes(6)->format('H:i:s'),
 'method' => 'GET',
 'url' => '/api/dashboard/vendas-hoje',
 'status' => 200
 ],
 [
 'timestamp' => now()->subMinutes(7)->format('H:i:s'),
 'method' => 'GET',
 'url' => '/api/health/database',
 'status' => 200
 ],
 [
 'timestamp' => now()->subMinutes(8)->format('H:i:s'),
 'method' => 'PUT',
 'url' => '/api/produtos/15',
 'status' => 200
 ],
 [
 'timestamp' => now()->subMinutes(9)->format('H:i:s'),
 'method' => 'GET',
 'url' => '/dashboard',
 'status' => 200
 ],
 [
 'timestamp' => now()->subMinutes(10)->format('H:i:s'),
 'method' => 'GET',
 'url' => '/api/mesas',
 'status' => 200
 ]
 ];
 return response()->json($logs);
 } catch (\Exception $e) {
 return response()->json([
 'error' => 'Erro ao obter logs de requisições: ' . $e->getMessage()
 ], 500);
 }
 }
 public function clear(Request $request)
 {
 try {
 $cleared = [];
 $laravelLog = storage_path('logs/laravel.log');
 if (File::exists($laravelLog)) {
 File::put($laravelLog, '');
 $cleared[] = 'Laravel';
 }
 Log::info('Logs do sistema foram limpos', [
 'usuario' => auth()->user()->nome ?? 'Sistema',
 'logs_limpos' => $cleared,
 'timestamp' => now()
 ]);
 return response()->json([
 'success' => true,
 'message' => 'Logs limpos com sucesso',
 'cleared' => $cleared
 ]);
 } catch (\Exception $e) {
 return response()->json([
 'success' => false,
 'error' => 'Erro ao limpar logs: ' . $e->getMessage()
 ], 500);
 }
 }
 private function tailFile($filename, $lines = 20)
 {
 if (!File::exists($filename)) {
 return [];
 }
 try {
 $content = File::get($filename);
 $allLines = explode("\n", $content);
 $allLines = array_filter($allLines, function($line) {
 return !empty(trim($line));
 });
 return array_slice($allLines, -$lines);
 } catch (\Exception $e) {
 return ["Erro ao ler arquivo: " . $e->getMessage()];
 }
 }
}