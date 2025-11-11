<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function getUsers()
    {
        $usuarios = Usuario::select('id', 'nome', 'email', 'role', 'created_at', 'updated_at')
                          ->orderBy('created_at', 'desc')
                          ->get();

        return response()->json([
            'success' => true,
            'users' => $usuarios
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,gerente,garcom,cliente',
        ]);

        $usuario = Usuario::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuário criado com sucesso!',
            'user' => $usuario
        ], 201);
    }

    public function show(Usuario $usuario)
    {
        return response()->json([
            'success' => true,
            'user' => $usuario
        ]);
    }

    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('usuarios')->ignore($usuario->id)],
            'role' => 'required|in:admin,gerente,garcom,cliente',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = [
            'nome' => $request->nome,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso!',
            'user' => $usuario->fresh()
        ]);
    }

    public function destroy(Usuario $usuario)
    {
        // Verificar se não é o último admin
        if ($usuario->role === 'admin') {
            $adminCount = Usuario::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir o último administrador do sistema!'
                ], 400);
            }
        }

        $usuario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuário excluído com sucesso!'
        ]);
    }

    public function getRoleStats()
    {
        $stats = Usuario::selectRaw('role, COUNT(*) as count')
                       ->groupBy('role')
                       ->get()
                       ->pluck('count', 'role');

        return response()->json([
            'success' => true,
            'stats' => [
                'admin' => $stats->get('admin', 0),
                'gerente' => $stats->get('gerente', 0),
                'garcom' => $stats->get('garcom', 0),
                'cliente' => $stats->get('cliente', 0),
                'total' => Usuario::count()
            ]
        ]);
    }
}
