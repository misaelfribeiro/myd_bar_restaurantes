<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Plano;
use App\Models\Empresa;
use App\Models\HistoricoContrato;
use App\Models\Fatura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class ContratoController extends Controller
{
 public function __construct()
 {
 $this->middleware(function ($request, $next) {
 if (!$this->isMaster()) {
 abort(403, 'Acesso negado.');
 }
 return $next($request);
 });
 }
 public function index(Request $request)
 {
 $query = Contrato::with(['empresa', 'plano']);
 if ($request->filled('status')) {
 $query->where('status', $request->status);
 }
 if ($request->filled('plano')) {
 $query->whereHas('plano', function($q) use ($request) {
 $q->where('codigo', $request->plano);
 });
 }
 if ($request->filled('search')) {
 $query->whereHas('empresa', function($q) use ($request) {
 $q->where('nome_fantasia', 'like', '%' . $request->search . '%')
 ->orWhere('razao_social', 'like', '%' . $request->search . '%');
 });
 }
 $contratos = $query->orderBy('created_at', 'desc')->paginate(20);
 $stats = [
 'total' => Contrato::count(),
 'ativos' => Contrato::where('status', 'ativo')->count(),
 'trial' => Contrato::where('status', 'trial')->count(),
 'vencendo' => Contrato::vencendo(30)->count(),
 'receita_mensal' => Contrato::where('status', 'ativo')
 ->where('tipo_pagamento', 'mensal')
 ->sum('valor_final'),
 ];
 $planos = Plano::ativo()->ordenado()->get();
 return view('admin.contratos.index', compact('contratos', 'stats', 'planos'));
 }
 public function create()
 {
 $empresas = Empresa::tenants()->orderBy('nome_fantasia')->get();
 $planos = Plano::ativo()->ordenado()->get();
 return view('admin.contratos.create', compact('empresas', 'planos'));
 }
 public function store(Request $request)
 {
 $validated = $request->validate([
 'empresa_id' => 'required|exists:empresas,id',
 'plano_id' => 'required|exists:planos,id',
 'data_inicio' => 'required|date',
 'tipo_pagamento' => 'required|in:mensal,anual',
 'desconto_aplicado' => 'nullable|numeric|min:0',
 'observacoes' => 'nullable|string',
 'documento_assinado' => 'nullable|file|mimes:pdf|max:10240',
 'documento_identidade' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
 'comprovante_endereco' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
 ]);
 DB::beginTransaction();
 try {
 $plano = Plano::findOrFail($request->plano_id);
 $empresa = Empresa::findOrFail($request->empresa_id);
 $valorContratado = $request->tipo_pagamento === 'mensal' 
 ? $plano->valor_mensal 
 : $plano->valor_anual;
 $descontoAplicado = $request->desconto_aplicado ?? 0;
 $valorFinal = $valorContratado - $descontoAplicado;
 $dataInicio = \Carbon\Carbon::parse($request->data_inicio);
 $dataFim = $request->tipo_pagamento === 'mensal' 
 ? $dataInicio->copy()->addMonth() 
 : $dataInicio->copy()->addYear();
 $documentos = [];
 foreach (['documento_assinado', 'documento_identidade', 'comprovante_endereco'] as $campo) {
 if ($request->hasFile($campo)) {
 $documentos[$campo] = $request->file($campo)->store('contratos/' . $empresa->tenant_code, 'public');
 }
 }
 $contrato = Contrato::create([
 'empresa_id' => $empresa->id,
 'plano_id' => $plano->id,
 'numero_contrato' => Contrato::gerarNumeroContrato(),
 'data_inicio' => $dataInicio,
 'data_fim' => $dataFim,
 'tipo_pagamento' => $request->tipo_pagamento,
 'valor_contratado' => $valorContratado,
 'desconto_aplicado' => $descontoAplicado,
 'valor_final' => $valorFinal,
 'max_usuarios' => $plano->max_usuarios,
 'max_produtos' => $plano->max_produtos,
 'max_pedidos_mes' => $plano->max_pedidos_mes,
 'max_filiais' => $plano->max_filiais,
 'status' => 'ativo',
 'observacoes' => $request->observacoes,
 'criado_por' => auth()->id(),
 ...$documentos
 ]);
 $empresa->update([
 'plano' => $plano->codigo,
 'status_contrato' => 'ativo',
 'data_inicio_contrato' => $dataInicio,
 'data_fim_contrato' => $dataFim,
 'valor_mensalidade' => $valorFinal,
 'max_usuarios' => $plano->max_usuarios,
 'max_produtos' => $plano->max_produtos,
 'max_pedidos_mes' => $plano->max_pedidos_mes,
 'max_filiais' => $plano->max_filiais,
 ]);
 HistoricoContrato::registrar(
 $contrato->id,
 'criado',
 'Contrato criado',
 null,
 $contrato->toArray()
 );
 if ($request->tipo_pagamento === 'mensal') {
 Fatura::create([
 'contrato_id' => $contrato->id,
 'empresa_id' => $empresa->id,
 'numero_fatura' => Fatura::gerarNumeroFatura(),
 'data_referencia' => $dataInicio,
 'data_vencimento' => $dataInicio->copy()->addDays(10),
 'data_emissao' => now(),
 'valor_plano' => $valorFinal,
 'valor_total' => $valorFinal,
 'status' => 'pendente',
 ]);
 }
 DB::commit();
 return redirect()->route('admin.contratos.show', $contrato->id)
 ->with('success', 'Contrato criado com sucesso!');
 } catch (\Exception $e) {
 DB::rollBack();
 return back()->withInput()->with('error', 'Erro ao criar contrato: ' . $e->getMessage());
 }
 }
 public function show($id)
 {
 $contrato = Contrato::with(['empresa', 'plano', 'historico.usuario', 'faturas'])
 ->findOrFail($id);
 return view('admin.contratos.show', compact('contrato'));
 }
 public function renovar(Request $request, $id)
 {
 $contratoAtual = Contrato::findOrFail($id);
 $request->validate([
 'tipo_pagamento' => 'required|in:mensal,anual',
 'desconto_aplicado' => 'nullable|numeric|min:0',
 ]);
 DB::beginTransaction();
 try {
 $plano = $contratoAtual->plano;
 $valorContratado = $request->tipo_pagamento === 'mensal' 
 ? $plano->valor_mensal 
 : $plano->valor_anual;
 $descontoAplicado = $request->desconto_aplicado ?? 0;
 $valorFinal = $valorContratado - $descontoAplicado;
 $dataInicio = $contratoAtual->data_fim->addDay();
 $dataFim = $request->tipo_pagamento === 'mensal' 
 ? $dataInicio->copy()->addMonth() 
 : $dataInicio->copy()->addYear();
 $novoContrato = Contrato::create([
 'empresa_id' => $contratoAtual->empresa_id,
 'plano_id' => $contratoAtual->plano_id,
 'numero_contrato' => Contrato::gerarNumeroContrato(),
 'data_inicio' => $dataInicio,
 'data_fim' => $dataFim,
 'tipo_pagamento' => $request->tipo_pagamento,
 'valor_contratado' => $valorContratado,
 'desconto_aplicado' => $descontoAplicado,
 'valor_final' => $valorFinal,
 'max_usuarios' => $plano->max_usuarios,
 'max_produtos' => $plano->max_produtos,
 'max_pedidos_mes' => $plano->max_pedidos_mes,
 'max_filiais' => $plano->max_filiais,
 'status' => 'ativo',
 'criado_por' => auth()->id(),
 ]);
 $contratoAtual->update([
 'data_renovacao' => now(),
 ]);
 $contratoAtual->empresa->update([
 'data_fim_contrato' => $dataFim,
 'valor_mensalidade' => $valorFinal,
 ]);
 HistoricoContrato::registrar(
 $novoContrato->id,
 'renovado',
 "Contrato renovado a partir de {$contratoAtual->numero_contrato}",
 $contratoAtual->toArray(),
 $novoContrato->toArray()
 );
 DB::commit();
 return response()->json([
 'success' => true,
 'message' => 'Contrato renovado com sucesso!',
 'contrato_id' => $novoContrato->id
 ]);
 } catch (\Exception $e) {
 DB::rollBack();
 return response()->json([
 'success' => false,
 'message' => 'Erro ao renovar contrato: ' . $e->getMessage()
 ], 500);
 }
 }
 public function suspender(Request $request, $id)
 {
 $contrato = Contrato::findOrFail($id);
 $request->validate([
 'motivo' => 'required|string',
 ]);
 DB::beginTransaction();
 try {
 $dadosAnteriores = $contrato->toArray();
 $contrato->update([
 'status' => 'suspenso',
 'observacoes' => $request->motivo,
 ]);
 $contrato->empresa->update([
 'status_contrato' => 'suspenso',
 ]);
 HistoricoContrato::registrar(
 $contrato->id,
 'suspenso',
 'Contrato suspenso: ' . $request->motivo,
 $dadosAnteriores,
 $contrato->toArray()
 );
 DB::commit();
 return response()->json([
 'success' => true,
 'message' => 'Contrato suspenso com sucesso!'
 ]);
 } catch (\Exception $e) {
 DB::rollBack();
 return response()->json([
 'success' => false,
 'message' => 'Erro ao suspender contrato: ' . $e->getMessage()
 ], 500);
 }
 }
 public function cancelar(Request $request, $id)
 {
 $contrato = Contrato::findOrFail($id);
 $request->validate([
 'motivo_cancelamento' => 'required|string',
 ]);
 DB::beginTransaction();
 try {
 $dadosAnteriores = $contrato->toArray();
 $contrato->update([
 'status' => 'cancelado',
 'data_cancelamento' => now(),
 'motivo_cancelamento' => $request->motivo_cancelamento,
 'cancelado_por' => auth()->id(),
 ]);
 $contrato->empresa->update([
 'status_contrato' => 'cancelado',
 ]);
 HistoricoContrato::registrar(
 $contrato->id,
 'cancelado',
 'Contrato cancelado: ' . $request->motivo_cancelamento,
 $dadosAnteriores,
 $contrato->toArray()
 );
 DB::commit();
 return response()->json([
 'success' => true,
 'message' => 'Contrato cancelado com sucesso!'
 ]);
 } catch (\Exception $e) {
 DB::rollBack();
 return response()->json([
 'success' => false,
 'message' => 'Erro ao cancelar contrato: ' . $e->getMessage()
 ], 500);
 }
 }
 public function reativar($id)
 {
 $contrato = Contrato::findOrFail($id);
 DB::beginTransaction();
 try {
 $dadosAnteriores = $contrato->toArray();
 $contrato->update([
 'status' => 'ativo',
 ]);
 $contrato->empresa->update([
 'status_contrato' => 'ativo',
 ]);
 HistoricoContrato::registrar(
 $contrato->id,
 'reativado',
 'Contrato reativado',
 $dadosAnteriores,
 $contrato->toArray()
 );
 DB::commit();
 return response()->json([
 'success' => true,
 'message' => 'Contrato reativado com sucesso!'
 ]);
 } catch (\Exception $e) {
 DB::rollBack();
 return response()->json([
 'success' => false,
 'message' => 'Erro ao reativar contrato: ' . $e->getMessage()
 ], 500);
 }
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