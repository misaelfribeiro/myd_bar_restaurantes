<?php
namespace App\Http\Controllers;
use App\Models\Entregador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class EntregadorController extends Controller
{
 public function index()
 {
 $entregadores = Entregador::with('aprovador')
 ->orderBy('created_at', 'desc')
 ->paginate(20);
 $statusOptions = [
 'pendente' => 'Pendente',
 'em_analise' => 'Em Análise', 
 'aprovado' => 'Aprovado',
 'ativo' => 'Ativo',
 'inativo' => 'Inativo',
 'suspenso' => 'Suspenso',
 'reprovado' => 'Reprovado'
 ];
 $tipoOptions = [
 'interno' => 'Interno',
 'externo' => 'App Externo'
 ];
 $stats = [
 'total' => Entregador::count(),
 'ativos' => Entregador::ativos()->count(),
 'pendentes' => Entregador::where('status', 'pendente')->count(),
 'disponiveis' => Entregador::disponiveis()->count(),
 ];
 return view('entregadores.index', compact('entregadores', 'stats', 'statusOptions', 'tipoOptions'));
 }
 public function create()
 {
 $estados = [
 'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
 'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
 'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul',
 'MG' => 'Minas Gerais', 'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
 'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
 'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
 'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
 ];
 return view('entregadores.create', compact('estados'));
 }
 public function store(Request $request)
 {
 $validator = Validator::make($request->all(), [
 'nome' => 'required|string|max:255',
 'email' => 'required|email|unique:entregadores,email',
 'telefone' => 'required|string|max:20',
 'cpf' => 'required|string|size:14|unique:entregadores,cpf',
 'data_nascimento' => 'required|date|before:today',
 'cep' => 'required|string|max:10',
 'endereco' => 'required|string|max:255',
 'numero' => 'required|string|max:20',
 'bairro' => 'required|string|max:100',
 'cidade' => 'required|string|max:100',
 'estado' => 'required|string|size:2',
 'tipo_veiculo' => 'required|in:moto,carro,bicicleta,pe',
 'tipo' => 'required|in:interno,externo',
 'foto_rg' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
 'foto_cpf' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
 'foto_cnh' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
 'foto_comprovante_endereco' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
 'foto_entregador' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
 ]);
 if (in_array($request->tipo_veiculo, ['moto', 'carro'])) {
 $validator->addRules([
 'cnh_numero' => 'required|string|max:20',
 'cnh_categoria' => 'required|in:A,B,AB,C,D,E',
 'cnh_validade' => 'required|date|after:today',
 'placa_veiculo' => 'required|string|max:10',
 'foto_cnh' => 'required|image|mimes:jpeg,png,jpg|max:2048',
 ]);
 }
 if ($validator->fails()) {
 return back()->withErrors($validator)->withInput();
 }
 $data = $request->all();
 $documentos = ['foto_rg', 'foto_cpf', 'foto_cnh', 'foto_comprovante_endereco', 'foto_entregador'];
 foreach ($documentos as $documento) {
 if ($request->hasFile($documento)) {
 $file = $request->file($documento);
 $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
 $path = $file->storeAs('entregadores/documentos', $filename, 'public');
 $data[$documento] = $path;
 }
 }
 if ($data['tipo'] === 'interno') {
 $data['status'] = 'aprovado';
 $data['data_aprovacao'] = now();
 $data['aprovado_por'] = auth()->id();
 }
 $entregador = Entregador::create($data);
 return redirect()->route('entregadores.show', $entregador->id)
 ->with('success', 'Entregador cadastrado com sucesso!');
 }
 public function show(Entregador $entregador)
 {
 $entregador->load(['aprovador', 'pedidos', 'avaliacoes.cliente']);
 $estatisticas = [
 'pedidos_total' => $entregador->pedidos()->count(),
 'pedidos_entregues' => $entregador->pedidos()->where('status', 'entregue')->count(),
 'pedidos_cancelados' => $entregador->pedidos()->where('status', 'cancelado')->count(),
 'total_entregas' => $entregador->entregas_realizadas ?? 0,
 'entregas_mes' => $entregador->pedidos()->where('status', 'entregue')
 ->whereMonth('updated_at', now()->month)
 ->whereYear('updated_at', now()->year)
 ->count(),
 'media_avaliacoes' => $entregador->avaliacao_media ?? 0,
 'total_avaliacoes' => $entregador->total_avaliacoes ?? 0,
 'taxa_sucesso' => $entregador->taxa_sucesso ?? 0,
 ];
 $avaliacoesRecentes = $entregador->avaliacoes()
 ->with('cliente')
 ->latest()
 ->limit(6)
 ->get();
 $pedidosRecentes = $entregador->pedidos()
 ->latest()
 ->limit(10)
 ->get();
 return view('entregadores.show', compact('entregador', 'estatisticas', 'avaliacoesRecentes', 'pedidosRecentes'));
 }
 public function edit(Entregador $entregador)
 {
 $estados = [
 'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
 'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
 'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul',
 'MG' => 'Minas Gerais', 'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
 'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
 'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
 'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
 ];
 return view('entregadores.edit', compact('entregador', 'estados'));
 }
 public function update(Request $request, Entregador $entregador)
 {
 $validator = Validator::make($request->all(), [
 'nome' => 'required|string|max:255',
 'email' => 'required|email|unique:entregadores,email,' . $entregador->id,
 'telefone' => 'required|string|max:20',
 'cpf' => 'required|string|size:14|unique:entregadores,cpf,' . $entregador->id,
 'data_nascimento' => 'required|date|before:today',
 'cep' => 'required|string|max:10',
 'endereco' => 'required|string|max:255',
 'numero' => 'required|string|max:20',
 'bairro' => 'required|string|max:100',
 'cidade' => 'required|string|max:100',
 'estado' => 'required|string|size:2',
 'tipo_veiculo' => 'required|in:moto,carro,bicicleta,pe',
 'foto_rg' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
 'foto_cpf' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
 'foto_cnh' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
 'foto_comprovante_endereco' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
 'foto_entregador' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
 ]);
 if ($validator->fails()) {
 return back()->withErrors($validator)->withInput();
 }
 $data = $request->all();
 $documentos = ['foto_rg', 'foto_cpf', 'foto_cnh', 'foto_comprovante_endereco', 'foto_entregador'];
 foreach ($documentos as $documento) {
 if ($request->hasFile($documento)) {
 if ($entregador->$documento) {
 Storage::disk('public')->delete($entregador->$documento);
 }
 $file = $request->file($documento);
 $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
 $path = $file->storeAs('entregadores/documentos', $filename, 'public');
 $data[$documento] = $path;
 }
 }
 $entregador->update($data);
 return redirect()->route('entregadores.show', $entregador->id)
 ->with('success', 'Dados do entregador atualizados com sucesso!');
 }
 public function destroy(Entregador $entregador)
 {
 $pedidosPendentes = $entregador->pedidos()
 ->whereIn('status', ['em_preparo', 'pronto', 'saiu_entrega'])
 ->count();
 if ($pedidosPendentes > 0) {
 return back()->with('error', 'Não é possível excluir um entregador com pedidos pendentes.');
 }
 $documentos = ['foto_rg', 'foto_cpf', 'foto_cnh', 'foto_comprovante_endereco', 'foto_entregador'];
 foreach ($documentos as $documento) {
 if ($entregador->$documento) {
 Storage::disk('public')->delete($entregador->$documento);
 }
 }
 $entregador->delete();
 return redirect()->route('entregadores.index')
 ->with('success', 'Entregador removido com sucesso!');
 }
 public function aprovar(Request $request, Entregador $entregador)
 {
 $request->validate([
 'observacoes' => 'nullable|string|max:1000'
 ]);
 $entregador->aprovar(auth()->id(), $request->observacoes);
 return back()->with('success', 'Entregador aprovado com sucesso!');
 }
 public function reprovar(Request $request, Entregador $entregador)
 {
 $request->validate([
 'observacoes' => 'required|string|max:1000'
 ]);
 $entregador->reprovar(auth()->id(), $request->observacoes);
 return back()->with('success', 'Entregador reprovado.');
 }
 public function suspender(Request $request, Entregador $entregador)
 {
 $request->validate([
 'observacoes' => 'required|string|max:1000'
 ]);
 $entregador->suspender(auth()->id(), $request->observacoes);
 return back()->with('success', 'Entregador suspenso.');
 }
 public function ativar(Entregador $entregador)
 {
 $entregador->ativar();
 return back()->with('success', 'Entregador ativado com sucesso!');
 }
 public function desativar(Entregador $entregador)
 {
 $entregador->desativar();
 return back()->with('success', 'Entregador desativado.');
 }
 public function toggleDisponibilidade(Entregador $entregador)
 {
 if ($entregador->disponivel) {
 $entregador->marcarComoIndisponivel();
 $message = 'Entregador marcado como indisponível.';
 } else {
 $entregador->marcarComoDisponivel();
 $message = 'Entregador marcado como disponível.';
 }
 return back()->with('success', $message);
 }
 public function pendentes()
 {
 $entregadores = Entregador::where('status', 'pendente')
 ->with('aprovador')
 ->orderBy('created_at', 'desc')
 ->paginate(20);
 return view('entregadores.pendentes', compact('entregadores'));
 }
 public function disponiveis()
 {
 $entregadores = Entregador::disponiveis()
 ->select(['id', 'nome', 'tipo_veiculo', 'avaliacao_media'])
 ->orderBy('avaliacao_media', 'desc')
 ->get();
 if (request()->expectsJson() || request()->is('api/*')) {
 return response()->json([
 'success' => true,
 'entregadores' => $entregadores
 ]);
 }
 $entregadores = Entregador::disponiveis()
 ->orderBy('avaliacao_media', 'desc')
 ->paginate(20);
 return view('entregadores.disponiveis', compact('entregadores'));
 }
 public function downloadDocumento(Entregador $entregador, $tipo)
 {
 $tipos_validos = [
 'foto_rg' => 'RG',
 'foto_cpf' => 'CPF', 
 'foto_cnh' => 'CNH',
 'foto_comprovante_endereco' => 'Comprovante de Endereço',
 'foto_entregador' => 'Foto do Entregador'
 ];
 if (!array_key_exists($tipo, $tipos_validos)) {
 abort(404, 'Tipo de documento inválido.');
 }
 if (!$entregador->$tipo) {
 abort(404, 'Documento não encontrado.');
 }
 $path = storage_path('app/public/' . $entregador->$tipo);
 if (!file_exists($path)) {
 abort(404, 'Arquivo não encontrado.');
 }
 $nomeArquivo = $entregador->nome . '_' . $tipos_validos[$tipo] . '.' . pathinfo($path, PATHINFO_EXTENSION);
 return response()->download($path, $nomeArquivo);
 }
}