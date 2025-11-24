<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller
{
 public function register(Request $request)
 {
 $request->validate([
 'nome' => 'required|string|max:255',
 'email' => 'required|string|email|max:255|unique:usuarios',
 'password' => 'required|string|min:6|confirmed',
 'role' => 'sometimes|in:admin,gerente,garcom,cliente',
 ]);
 $usuario = Usuario::create([
 'nome' => $request->nome,
 'email' => $request->email,
 'password' => Hash::make($request->password),
 'role' => $request->get('role', 'cliente'),
 ]);
 $token = $usuario->createToken('auth_token')->plainTextToken;
 return response()->json([
 'message' => 'Usuário registrado com sucesso',
 'usuario' => $usuario,
 'access_token' => $token,
 'token_type' => 'Bearer',
 ], 201);
 }
 public function login(Request $request)
 {
 $request->validate([
 'email' => 'required|email',
 'password' => 'required',
 ]);
 
 // Busca primeiro na tabela usuarios
 $usuario = Usuario::where('email', $request->email)->first();
 
 if ($usuario && Hash::check($request->password, $usuario->password)) {
 $usuario->tokens()->delete();
 $token = $usuario->createToken('auth_token')->plainTextToken;
 AccessLog::logLogin($usuario, true);
 
 return response()->json([
 'message' => 'Login realizado com sucesso',
 'usuario' => [
 'id' => $usuario->id,
 'nome' => $usuario->nome,
 'email' => $usuario->email,
 'role' => $usuario->role,
 'tenant_code' => $usuario->tenant_code,
 ],
 'access_token' => $token,
 'token_type' => 'Bearer',
 ]);
 }
 
 // Se não encontrou em usuarios, busca em users (admin/master) com tenant_code != EATSFOOD
 $admin = \App\Models\User::where('email', $request->email)
     ->where('tenant_code', '!=', 'EATSFOOD')
     ->first();
 
 if ($admin && Hash::check($request->password, $admin->password)) {
 // Users não usam Sanctum tokens normalmente, mas podemos criar
 $admin->tokens()->delete();
 $token = $admin->createToken('auth_token')->plainTextToken;
 
 return response()->json([
 'message' => 'Login realizado com sucesso',
 'usuario' => [
 'id' => $admin->id,
 'nome' => $admin->name,
 'email' => $admin->email,
 'role' => 'admin',
 'tenant_code' => $admin->tenant_code,
 ],
 'access_token' => $token,
 'token_type' => 'Bearer',
 ]);
 }
 
 AccessLog::logLogin(null, false);
 throw ValidationException::withMessages([
 'email' => ['As credenciais fornecidas estão incorretas.'],
 ]);
 }
 public function logout(Request $request)
 {
 $user = $request->user();
 AccessLog::logLogout($user);
 $user->currentAccessToken()->delete();
 return response()->json([
 'message' => 'Logout realizado com sucesso'
 ]);
 }
 public function me(Request $request)
 {
 return response()->json([
 'usuario' => $request->user(),
 ]);
 }
 public function refresh(Request $request)
 {
 $request->user()->currentAccessToken()->delete();
 $token = $request->user()->createToken('auth_token')->plainTextToken;
 return response()->json([
 'message' => 'Token renovado com sucesso',
 'access_token' => $token,
 'token_type' => 'Bearer',
 ]);
 }
 public function revokeAll(Request $request)
 {
 $request->user()->tokens()->delete();
 return response()->json([
 'message' => 'Todos os tokens foram revogados'
 ]);
 }
}