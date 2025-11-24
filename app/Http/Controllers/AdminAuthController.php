<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AdminAuthController extends Controller
{
    /**
     * Exibe o formulário de login
     */
    public function showLogin()
    {
        // Se já estiver logado, redireciona para o dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }

    /**
     * Processa o login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
        ]);

        // Limpar qualquer sessão anterior
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Buscar usuário APENAS na tabela users (Master/Admin)
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este e-mail não tem acesso ao painel administrativo.'
                ], 401);
            }
            
            return back()->withErrors([
                'email' => 'Este e-mail não tem acesso ao painel administrativo.'
            ])->withInput();
        }

        // Verificar senha
        if (!Hash::check($request->password, $user->password)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha incorreta.'
                ], 401);
            }
            
            return back()->withErrors([
                'password' => 'Senha incorreta.'
            ])->withInput();
        }

        // Fazer login no guard admin
        Auth::guard('admin')->login($user, $request->filled('remember'));
        $request->session()->regenerate();

        // Resposta JSON para AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso!',
                'redirect' => route('admin.dashboard')
            ]);
        }

        // Redirecionamento normal
        return redirect()->route('admin.dashboard')->with('success', 'Login realizado com sucesso!');
    }

    /**
     * Faz logout
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso!',
                'redirect' => route('admin.login')
            ]);
        }

        return redirect()->route('admin.login')->with('success', 'Logout realizado com sucesso!');
    }
}
