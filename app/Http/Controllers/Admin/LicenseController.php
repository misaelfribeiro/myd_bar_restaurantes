<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class LicenseController extends Controller
{
 public function index()
 {
 $licenses = License::latest()->paginate(20);
 $statistics = [
 'total' => License::count(),
 'ativas' => License::where('status', 'ativa')->count(),
 'expiradas' => License::where('status', 'expirada')->count(),
 'proximas_expirar' => License::where('status', 'ativa')
 ->whereNotNull('data_expiracao')
 ->where('data_expiracao', '<=', Carbon::now()->addDays(30))
 ->count()
 ];
 return view('admin.licenses.index', compact('licenses', 'statistics'));
 }
 public function create()
 {
 return view('admin.licenses.create');
 }
 public function store(Request $request)
 {
 $validated = $request->validate([
 'tenant_code' => 'required|string|max:255',
 'cliente_nome' => 'required|string|max:255',
 'cliente_email' => 'required|email|max:255',
 'cliente_documento' => 'nullable|string|max:20',
 'cliente_telefone' => 'nullable|string|max:20',
 'tipo' => 'required|in:standard,premium,enterprise',
 'max_usuarios' => 'required|integer|min:1',
 'max_mesas' => 'required|integer|min:1',
 'modulo_delivery' => 'boolean',
 'modulo_rh' => 'boolean',
 'modulo_financeiro' => 'boolean',
 'data_expiracao' => 'nullable|date|after:today',
 'observacoes' => 'nullable|string'
 ]);
 $validated['license_key'] = License::gerarChave();
 $validated['status'] = 'ativa';
 $validated['modulo_delivery'] = $request->has('modulo_delivery');
 $validated['modulo_rh'] = $request->has('modulo_rh');
 $validated['modulo_financeiro'] = $request->has('modulo_financeiro');
 $license = License::create($validated);
 return redirect()
 ->route('admin.licenses.show', $license)
 ->with('success', 'Licença criada com sucesso!');
 }
 public function show(License $license)
 {
 return view('admin.licenses.show', compact('license'));
 }
 public function edit(License $license)
 {
 return view('admin.licenses.edit', compact('license'));
 }
 public function update(Request $request, License $license)
 {
 $validated = $request->validate([
 'cliente_nome' => 'required|string|max:255',
 'cliente_email' => 'required|email|max:255',
 'cliente_documento' => 'nullable|string|max:20',
 'cliente_telefone' => 'nullable|string|max:20',
 'tipo' => 'required|in:standard,premium,enterprise',
 'max_usuarios' => 'required|integer|min:1',
 'max_mesas' => 'required|integer|min:1',
 'modulo_delivery' => 'boolean',
 'modulo_rh' => 'boolean',
 'modulo_financeiro' => 'boolean',
 'data_expiracao' => 'nullable|date',
 'status' => 'required|in:ativa,expirada,suspensa,cancelada',
 'observacoes' => 'nullable|string'
 ]);
 $validated['modulo_delivery'] = $request->has('modulo_delivery');
 $validated['modulo_rh'] = $request->has('modulo_rh');
 $validated['modulo_financeiro'] = $request->has('modulo_financeiro');
 $license->update($validated);
 return redirect()
 ->route('admin.licenses.show', $license)
 ->with('success', 'Licença atualizada com sucesso!');
 }
 public function destroy(License $license)
 {
 $license->delete();
 return redirect()
 ->route('admin.licenses.index')
 ->with('success', 'Licença removida com sucesso!');
 }
 public function verify(Request $request)
 {
 $request->validate([
 'license_key' => 'required|string',
 'hardware_id' => 'required|string'
 ]);
 $license = License::where('license_key', $request->license_key)->first();
 if (!$license) {
 return response()->json([
 'valid' => false,
 'message' => 'Licença não encontrada'
 ], 404);
 }
 if ($license->hardware_id && $license->hardware_id !== $request->hardware_id) {
 return response()->json([
 'valid' => false,
 'message' => 'Licença já ativada em outro computador'
 ], 403);
 }
 if (!$license->isAtiva()) {
 return response()->json([
 'valid' => false,
 'message' => 'Licença ' . $license->status,
 'status' => $license->status
 ], 403);
 }
 $license->registrarVerificacao();
 if (!$license->hardware_id) {
 $license->ativar($request->hardware_id);
 }
 return response()->json([
 'valid' => true,
 'license' => [
 'tipo' => $license->tipo,
 'cliente_nome' => $license->cliente_nome,
 'max_usuarios' => $license->max_usuarios,
 'max_mesas' => $license->max_mesas,
 'modulo_delivery' => $license->modulo_delivery,
 'modulo_rh' => $license->modulo_rh,
 'modulo_financeiro' => $license->modulo_financeiro,
 'data_expiracao' => $license->data_expiracao,
 'dias_restantes' => $license->diasRestantes()
 ]
 ]);
 }
 public function reativar(License $license)
 {
 $license->update([
 'hardware_id' => null,
 'tentativas_ativacao' => 0
 ]);
 return redirect()
 ->route('admin.licenses.show', $license)
 ->with('success', 'Licença liberada para reativação em novo computador!');
 }
 public function regenerateKey(License $license)
 {
 $license->update([
 'license_key' => License::gerarChave(),
 'hardware_id' => null,
 'tentativas_ativacao' => 0
 ]);
 return redirect()
 ->route('admin.licenses.show', $license)
 ->with('success', 'Nova chave de licença gerada!');
 }
 public function export(License $license)
 {
 $data = [
 'license_key' => $license->license_key,
 'cliente_nome' => $license->cliente_nome,
 'cliente_email' => $license->cliente_email,
 'tipo' => $license->tipo,
 'data_expiracao' => $license->data_expiracao?->format('Y-m-d'),
 'modulos' => [
 'delivery' => $license->modulo_delivery,
 'rh' => $license->modulo_rh,
 'financeiro' => $license->modulo_financeiro
 ],
 'limites' => [
 'max_usuarios' => $license->max_usuarios,
 'max_mesas' => $license->max_mesas
 ]
 ];
 $filename = 'license-' . $license->license_key . '.json';
 $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
 return response($json)
 ->header('Content-Type', 'application/json')
 ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
 }
}