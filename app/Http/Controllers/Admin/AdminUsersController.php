<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
class AdminUsersController extends Controller
{
 public function index()
 {
 if (!$this->isMaster()) {
 abort(403, 'Acesso negado. Apenas Master pode acessar esta área.');
 }
 $users = User::with('empresa')
 ->where('tenant_code', '!=', 'EATSFOOD')
 ->orderBy('created_at', 'desc')
 ->paginate(20);
 return view('admin.users.index', compact('users'));
 }
 public function create()
 {
 if (!$this->isMaster()) {
 abort(403, 'Acesso negado.');
 }
 $empresas = Empresa::where('tenant_code', '!=', 'EATSFOOD')
 ->where('is_master', false)
 ->orderBy('nome_fantasia')
 ->get();
 return view('admin.users.create', compact('empresas'));
 }
 public function store(Request $request)
 {
 if (!$this->isMaster()) {
 abort(403, 'Acesso negado.');
 }
 $request->validate([
 'name' => 'required|string|max:255',
 'email' => 'required|string|email|max:255|unique:users',
 'password' => 'required|string|min:8|confirmed',
 'tenant_code' => 'required|exists:empresas,tenant_code',
 ]);
 $user = User::create([
 'name' => $request->name,
 'email' => $request->email,
 'password' => Hash::make($request->password),
 'tenant_code' => $request->tenant_code,
 ]);
 return redirect()->route('admin.users.index')
 ->with('success', 'Usuário administrador criado com sucesso!');
 }
 public function show(User $user)
 {
 if (!$this->isMaster()) {
 abort(403, 'Acesso negado.');
 }
 $user->load('empresa');
 return view('admin.users.show', compact('user'));
 }
 public function edit(User $user)
 {
 if (!$this->isMaster()) {
 abort(403, 'Acesso negado.');
 }
 $empresas = Empresa::where('tenant_code', '!=', 'EATSFOOD')
 ->where('is_master', false)
 ->orderBy('nome_fantasia')
 ->get();
 return view('admin.users.edit', compact('user', 'empresas'));
 }
 public function update(Request $request, User $user)
 {
 if (!$this->isMaster()) {
 abort(403, 'Acesso negado.');
 }
 $request->validate([
 'name' => 'required|string|max:255',
 'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
 'tenant_code' => 'required|exists:empresas,tenant_code',
 'password' => 'nullable|string|min:8|confirmed',
 ]);
 $data = [
 'name' => $request->name,
 'email' => $request->email,
 'tenant_code' => $request->tenant_code,
 ];
 if ($request->filled('password')) {
 $data['password'] = Hash::make($request->password);
 }
 $user->update($data);
 return redirect()->route('admin.users.index')
 ->with('success', 'Usuário atualizado com sucesso!');
 }
 public function destroy(User $user)
 {
 if (!$this->isMaster()) {
 abort(403, 'Acesso negado.');
 }
 if ($user->id === auth('admin')->id()) {
 return redirect()->back()
 ->with('error', 'Você não pode deletar seu próprio usuário!');
 }
 if ($user->tenant_code === 'EATSFOOD') {
 return redirect()->back()
 ->with('error', 'Usuários Master não podem ser deletados por esta interface!');
 }
 $user->delete();
 return redirect()->route('admin.users.index')
 ->with('success', 'Usuário deletado com sucesso!');
 }
 public function stats()
 {
 if (!$this->isMaster()) {
 abort(403, 'Acesso negado.');
 }
 $stats = [
 'total' => User::where('tenant_code', '!=', 'EATSFOOD')->count(),
 'ativos' => User::where('tenant_code', '!=', 'EATSFOOD')
 ->whereDate('updated_at', '>=', now()->subDays(30))
 ->count(),
 'inativos' => User::where('tenant_code', '!=', 'EATSFOOD')
 ->whereDate('updated_at', '<', now()->subDays(30))
 ->count(),
 'por_empresa' => User::selectRaw('tenant_code, COUNT(*) as total')
 ->where('tenant_code', '!=', 'EATSFOOD')
 ->groupBy('tenant_code')
 ->get(),
 ];
 return response()->json($stats);
 }
 private function isMaster()
 {
 if (auth()->guard('admin')->check()) {
 $user = auth()->guard('admin')->user();
 if ($user->tenant_code === 'EATSFOOD') {
 $empresa = Empresa::where('tenant_code', 'EATSFOOD')->first();
 return $empresa && $empresa->is_master;
 }
 }
 return false;
 }
}