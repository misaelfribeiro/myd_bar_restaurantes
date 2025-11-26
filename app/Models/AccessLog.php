<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class AccessLog extends Model
{
 use HasFactory;
 protected $fillable = [
 'usuario_id',
 'email',
 'action',
 'endpoint',
 'method',
 'ip_address',
 'user_agent',
 'metadata',
 'status'
 ];
 protected $casts = [
 'metadata' => 'array'
 ];
 public function usuario()
 {
 return $this->belongsTo(Usuario::class);
 }
 public static function logAccess($action, $data = [])
 {
 return self::create(array_merge([
 'action' => $action,
 'ip_address' => request()->ip(),
 'user_agent' => request()->userAgent(),
 'created_at' => now()
 ], $data));
 }
 public static function logLogin($usuario, $success = true)
 {
 return self::logAccess('login', [
 'usuario_id' => $usuario ? $usuario->id : null,
 'email' => $usuario ? $usuario->email : request('email'),
 'status' => $success ? 'success' : 'failed'
 ]);
 }
 public static function logLogout($usuario)
 {
 return self::logAccess('logout', [
 'usuario_id' => $usuario->id,
 'email' => $usuario->email,
 'status' => 'success'
 ]);
 }
 public static function logApiAccess($usuario, $endpoint, $method, $success = true)
 {
        // Se for um Cliente (não Usuario), não loga o usuario_id pois a FK é para usuarios
        $usuarioId = null;
        $email = null;
        
        if ($usuario) {
            // Só loga usuario_id se for realmente um Usuario
            if ($usuario instanceof \App\Models\Usuario) {
                $usuarioId = $usuario->id;
                $email = $usuario->email;
            } elseif ($usuario instanceof \App\Models\Cliente) {
                // Para clientes, só loga o email sem usuario_id
                $email = $usuario->email;
            }
        }
        
 return self::logAccess('api_access', [
            'usuario_id' => $usuarioId,
            'email' => $email,
 'endpoint' => $endpoint,
 'method' => $method,
 'status' => $success ? 'success' : 'denied'
 ]);
 }
}