<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
class WebAuthController extends Controller
{    
 public function showLogin()
 {
 return view('login-simple');
 }
 public function login(Request $request)
 {
 $request->validate([
 'email' => 'required|email',
 'password' => 'required|min:6'
 ], [
 'email.required' => 'O email é obrigatório.',
 'email.email' => 'Digite um email válido.',
 'password.required' => 'A senha é obrigatória.',
 'password.min' => 'A senha deve ter pelo menos 6 caracteres.'
 ]);
 $usuario = Usuario::where('email', $request->email)->first();
 if (!$usuario || !Hash::check($request->password, $usuario->password)) {
 return back()->withErrors([
 'email' => 'Credenciais inválidas.',
 ])->withInput($request->only('email'));
 }
 Auth::login($usuario, $request->filled('remember'));
 $request->session()->regenerate();
 $redirectTo = $this->getRedirectPath($usuario);
 return redirect()->intended($redirectTo)->with('success', 'Login realizado com sucesso!');
 }
 public function logout(Request $request)
 {
 Auth::logout();
 $request->session()->invalidate();
 $request->session()->regenerateToken();
 return redirect('/login')->with('success', 'Logout realizado com sucesso!');
 }    
 private function getRedirectPath($usuario)
 {
 switch ($usuario->role) {
 case 'admin':
 return '/dashboard';
 case 'gerente':
 return '/dashboard';
 case 'garcom':
 return '/garcom/dashboard';
 case 'caixa':
 return '/caixa';
 default:
 return '/dashboard';
 }
 }
 public function showRegister()
 {
 return view('register');
 }
 public function register(Request $request)
 {        $request->validate([
 'nome' => 'required|string|max:255',
 'email' => 'required|string|email|max:255|unique:usuarios',
 'password' => 'required|string|min:6|confirmed',
 'role' => 'required|in:admin,gerente,garcom,caixa'
 ]);
 $usuario = Usuario::create([
 'nome' => $request->nome,
 'email' => $request->email,
 'password' => Hash::make($request->password),
 'role' => $request->role,
 ]);
 Auth::login($usuario);
 return redirect($this->getRedirectPath($usuario));
 }
}