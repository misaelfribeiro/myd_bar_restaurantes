<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Exception;
class HealthController extends Controller
{
 public function server()
 {
 try {
 $phpVersion = PHP_VERSION;
 $laravelVersion = app()->version();
 $memoryUsage = memory_get_usage(true);
 $memoryLimit = ini_get('memory_limit');
 return response()->json([
 'status' => 'ok',
 'message' => 'Servidor funcionando normalmente',
 'data' => [
 'php_version' => $phpVersion,
 'laravel_version' => $laravelVersion,
 'memory_usage' => round($memoryUsage / 1024 / 1024, 2) . 'MB',
 'memory_limit' => $memoryLimit,
 'timestamp' => now()->toDateTimeString()
 ]
 ]);
 } catch (Exception $e) {
 return response()->json([
 'status' => 'error',
 'message' => 'Erro no servidor: ' . $e->getMessage()
 ], 500);
 }
 }
 public function database()
 {
 try {
 DB::connection()->getPdo();
 $result = DB::select('SELECT 1 as test');
 return response()->json([
 'status' => 'ok',
 'message' => 'Banco de dados conectado',
 'data' => [
 'connection' => 'mysql',
 'database' => config('database.connections.mysql.database'),
 'timestamp' => now()->toDateTimeString()
 ]
 ]);
 } catch (Exception $e) {
 return response()->json([
 'status' => 'error',
 'message' => 'Erro na conexão com o banco: ' . $e->getMessage()
 ], 500);
 }
 }
 public function cache()
 {
 try {
 $key = 'health_check_' . time();
 $value = 'test_' . rand(1000, 9999);
 Cache::put($key, $value, 60);
 $retrieved = Cache::get($key);
 if ($retrieved === $value) {
 Cache::forget($key);
 return response()->json([
 'status' => 'ok',
 'message' => 'Cache funcionando',
 'data' => [
 'driver' => config('cache.default'),
 'timestamp' => now()->toDateTimeString()
 ]
 ]);
 } else {
 throw new Exception('Cache não retornou o valor correto');
 }
 } catch (Exception $e) {
 return response()->json([
 'status' => 'error',
 'message' => 'Erro no cache: ' . $e->getMessage()
 ], 500);
 }
 }
 public function php()
 {
 try {
 $phpInfo = [
 'version' => PHP_VERSION,
 'sapi' => php_sapi_name(),
 'extensions' => [
 'pdo_mysql' => extension_loaded('pdo_mysql'),
 'mbstring' => extension_loaded('mbstring'),
 'openssl' => extension_loaded('openssl'),
 'tokenizer' => extension_loaded('tokenizer'),
 'xml' => extension_loaded('xml'),
 'ctype' => extension_loaded('ctype'),
 'json' => extension_loaded('json'),
 'bcmath' => extension_loaded('bcmath'),
 ]
 ];
 $missingExtensions = array_filter($phpInfo['extensions'], function($loaded) {
 return !$loaded;
 });
 $status = empty($missingExtensions) ? 'ok' : 'warning';
 return response()->json([
 'status' => $status,
 'message' => $status === 'ok' ? 'PHP configurado corretamente' : 'Algumas extensões estão faltando',
 'data' => $phpInfo
 ]);
 } catch (Exception $e) {
 return response()->json([
 'status' => 'error',
 'message' => 'Erro ao verificar PHP: ' . $e->getMessage()
 ], 500);
 }
 }
 public function apache()
 {
 try {
 $serverInfo = $_SERVER;
 return response()->json([
 'status' => 'ok',
 'message' => 'Apache funcionando',
 'data' => [
 'software' => $serverInfo['SERVER_SOFTWARE'] ?? 'Unknown',
 'protocol' => $serverInfo['SERVER_PROTOCOL'] ?? 'Unknown',
 'port' => $serverInfo['SERVER_PORT'] ?? 'Unknown',
 'timestamp' => now()->toDateTimeString()
 ]
 ]);
 } catch (Exception $e) {
 return response()->json([
 'status' => 'error',
 'message' => 'Erro ao verificar Apache: ' . $e->getMessage()
 ], 500);
 }
 }
 public function mysql()
 {
 try {
 $version = DB::select('SELECT VERSION() as version')[0]->version;
 $variables = DB::select("SHOW VARIABLES LIKE 'max_connections'")[0];
 return response()->json([
 'status' => 'ok',
 'message' => 'MySQL funcionando',
 'data' => [
 'version' => $version,
 'max_connections' => $variables->Value,
 'timestamp' => now()->toDateTimeString()
 ]
 ]);
 } catch (Exception $e) {
 return response()->json([
 'status' => 'error',
 'message' => 'Erro ao verificar MySQL: ' . $e->getMessage()
 ], 500);
 }
 }
 public function laravel()
 {
 try {
 $config = [
 'app_env' => config('app.env'),
 'app_debug' => config('app.debug'),
 'app_url' => config('app.url'),
 'database_connection' => config('database.default'),
 'cache_driver' => config('cache.default'),
 'queue_driver' => config('queue.default'),
 ];
 return response()->json([
 'status' => 'ok',
 'message' => 'Laravel configurado corretamente',
 'data' => [
 'version' => app()->version(),
 'config' => $config,
 'timestamp' => now()->toDateTimeString()
 ]
 ]);
 } catch (Exception $e) {
 return response()->json([
 'status' => 'error',
 'message' => 'Erro ao verificar Laravel: ' . $e->getMessage()
 ], 500);
 }
 }
}