<?php
namespace App\Http\Controllers;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class UsuarioController extends Controller
{
 public function index()
 {
 $usuarios = Usuario::with('pedidos')->get();
 return response()->json($usuarios);
 }
 public function create()
 {
 }
 public function store(Request $request)
 {
 $request->validate([
 'nome' => 'required|string|max:255',
 'email' => 'required|email|unique:usuarios',
 'password' => 'required|string|min:6'
 ]);
 $usuario = Usuario::create([
 'nome' => $request->nome,
 'email' => $request->email,
 'password' => Hash::make($request->password)
 ]);
 return response()->json($usuario, 201);
 }
 public function show(Usuario $usuario)
 {
 return response()->json($usuario->load('pedidos'));
 }
 public function edit(Usuario $usuario)
 {
 }
 public function update(Request $request, Usuario $usuario)
 {
 $request->validate([
 'nome' => 'sometimes|string|max:255',
 'email' => 'sometimes|email|unique:usuarios,email,' . $usuario->id,
 'password' => 'sometimes|string|min:6'
 ]);
 $data = $request->only(['nome', 'email']);
 if ($request->has('password')) {
 $data['password'] = Hash::make($request->password);
 }
 $usuario->update($data);
 return response()->json($usuario);
 }
 public function destroy(Usuario $usuario)
 {
 $usuario->delete();
 return response()->json(['message' => 'Usuário deletado com sucesso']);
 }
}