<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class ClienteApiController extends Controller
{
 public function buscar(Request $request)
 {
 $validator = Validator::make($request->all(), [
 'telefone' => 'required_without:email|string',
 'email' => 'required_without:telefone|email',
 ]);
 if ($validator->fails()) {
 return response()->json([
 'success' => false,
 'message' => 'Informe telefone ou email para busca',
 'errors' => $validator->errors()
 ], 400);
 }
 $query = Cliente::where('ativo', true);
 if ($request->filled('telefone')) {
 $query->where('telefone', $request->telefone);
 }
 if ($request->filled('email')) {
 $query->where('email', $request->email);
 }
 $clientes = $query->get();
 return response()->json([
 'success' => true,
 'data' => $clientes,
 'total' => $clientes->count()
 ]);
 }
 public function search(Request $request)
 {
 $search = $request->get('search', '');
 if (strlen($search) < 3) {
 return response()->json([
 'success' => false,
 'message' => 'Digite pelo menos 3 caracteres para buscar',
 'data' => []
 ]);
 }
 $clientes = Cliente::where('ativo', true)
 ->where(function($query) use ($search) {
 $query->where('nome', 'LIKE', "%{$search}%")
 ->orWhere('telefone', 'LIKE', "%{$search}%")
 ->orWhere('email', 'LIKE', "%{$search}%")
 ->orWhere('endereco_bairro', 'LIKE', "%{$search}%");
 })
 ->orderBy('nome')
 ->limit(10)
 ->get()
 ->map(function($cliente) {
 return [
 'id' => $cliente->id,
 'nome' => $cliente->nome,
 'telefone' => $cliente->telefone,
 'email' => $cliente->email,
 'endereco_bairro' => $cliente->endereco_bairro,
 'endereco_completo' => trim(implode(', ', array_filter([
 $cliente->endereco_rua,
 $cliente->endereco_numero,
 $cliente->endereco_bairro,
 $cliente->endereco_cidade
 ])))
 ];
 });
 return response()->json([
 'success' => true,
 'data' => $clientes,
 'total' => $clientes->count()
 ]);
 }
 public function show($id)
 {
 $cliente = Cliente::find($id);
 if (!$cliente) {
 return response()->json([
 'success' => false,
 'message' => 'Cliente não encontrado'
 ], 404);
 }
 $dados = $cliente->toArray();
 $dados['tem_endereco'] = !empty($cliente->endereco_rua) && !empty($cliente->endereco_numero);
 if ($dados['tem_endereco']) {
 $endereco_partes = array_filter([
 $cliente->endereco_rua,
 $cliente->endereco_numero,
 $cliente->endereco_complemento,
 $cliente->endereco_bairro,
 $cliente->endereco_cidade,
 $cliente->endereco_cep ? "CEP: {$cliente->endereco_cep}" : null
 ]);
 $dados['endereco_completo'] = implode(', ', $endereco_partes);
 } else {
 $dados['endereco_completo'] = null;
 }
 $dados['total_pedidos'] = $cliente->pedidos()->count() ?? 0;
 return response()->json([
 'success' => true,
 'data' => $dados
 ]);
 }
 public function store(Request $request)
 {
 $validator = Validator::make($request->all(), [
 'nome' => 'required|string|max:255',
 'telefone' => 'required|string|max:20',
 'email' => 'nullable|email|unique:clientes,email',
 'endereco_rua' => 'nullable|string|max:255',
 'endereco_numero' => 'nullable|string|max:20',
 'endereco_complemento' => 'nullable|string|max:255',
 'endereco_bairro' => 'nullable|string|max:100',
 'endereco_cidade' => 'nullable|string|max:100',
 'endereco_cep' => 'nullable|string|max:10',
 ]);
 if ($validator->fails()) {
 return response()->json([
 'success' => false,
 'message' => 'Dados inválidos',
 'errors' => $validator->errors()
 ], 400);
 }
 $clienteExistente = Cliente::where('telefone', $request->telefone)->first();
 if ($clienteExistente) {
 return response()->json([
 'success' => false,
 'message' => 'Cliente já cadastrado com este telefone',
 'data' => $clienteExistente
 ], 409);
 }
 $cliente = Cliente::create($request->all());
 return response()->json([
 'success' => true,
 'message' => 'Cliente cadastrado com sucesso',
 'data' => $cliente
 ], 201);
 }
 public function update(Request $request, $id)
 {
 $cliente = Cliente::find($id);
 if (!$cliente) {
 return response()->json([
 'success' => false,
 'message' => 'Cliente não encontrado'
 ], 404);
 }
 $validator = Validator::make($request->all(), [
 'nome' => 'sometimes|required|string|max:255',
 'telefone' => 'sometimes|required|string|max:20',
 'email' => 'sometimes|nullable|email|unique:clientes,email,' . $id,
 'endereco_rua' => 'sometimes|nullable|string|max:255',
 'endereco_numero' => 'sometimes|nullable|string|max:20',
 'endereco_complemento' => 'sometimes|nullable|string|max:255',
 'endereco_bairro' => 'sometimes|nullable|string|max:100',
 'endereco_cidade' => 'sometimes|nullable|string|max:100',
 'endereco_cep' => 'sometimes|nullable|string|max:10',
 ]);
 if ($validator->fails()) {
 return response()->json([
 'success' => false,
 'message' => 'Dados inválidos',
 'errors' => $validator->errors()
 ], 400);
 }
 if ($request->filled('telefone') && $request->telefone !== $cliente->telefone) {
 $telefoneExiste = Cliente::where('telefone', $request->telefone)
 ->where('id', '!=', $id)
 ->exists();
 if ($telefoneExiste) {
 return response()->json([
 'success' => false,
 'message' => 'Telefone já cadastrado em outro cliente'
 ], 409);
 }
 }
 $cliente->update($request->only([
 'nome', 'telefone', 'email', 'endereco_rua', 'endereco_numero',
 'endereco_complemento', 'endereco_bairro', 'endereco_cidade', 'endereco_cep'
 ]));
 return response()->json([
 'success' => true,
 'message' => 'Cliente atualizado com sucesso',
 'data' => $cliente->fresh()
 ]);
 }
 public function buscarOuCriarParaDelivery(Request $request)
 {
 $validator = Validator::make($request->all(), [
 'nome' => 'required|string|max:255',
 'telefone' => 'required|string|max:20',
 'email' => 'nullable|email',
 'endereco_rua' => 'required|string|max:255',
 'endereco_numero' => 'required|string|max:20',
 'endereco_complemento' => 'nullable|string|max:255',
 'endereco_bairro' => 'required|string|max:100',
 'endereco_cidade' => 'required|string|max:100',
 'endereco_cep' => 'required|string|max:10',
 ]);
 if ($validator->fails()) {
 return response()->json([
 'success' => false,
 'message' => 'Dados inválidos para delivery',
 'errors' => $validator->errors()
 ], 400);
 }
 $telefone = preg_replace('/\D/', '', $request->telefone);
 $cliente = Cliente::where('telefone', 'like', "%{$telefone}%")->first();
 if ($cliente) {
 $dadosAtualizacao = [];
 $mudancas = [];
 if ($request->filled('email') && $cliente->email !== $request->email) {
 $emailExiste = Cliente::where('email', $request->email)
 ->where('id', '!=', $cliente->id)
 ->exists();
 if (!$emailExiste) {
 $dadosAtualizacao['email'] = $request->email;
 $mudancas[] = 'email';
 }
 }
 $enderecoAtual = [$cliente->endereco_rua, $cliente->endereco_numero, $cliente->endereco_bairro];
 $enderecoNovo = [$request->endereco_rua, $request->endereco_numero, $request->endereco_bairro];
 $enderecoAtualCompleto = !in_array(null, $enderecoAtual) && !in_array('', $enderecoAtual);
 $enderecoNovoCompleto = !in_array(null, $enderecoNovo) && !in_array('', $enderecoNovo);
 if (!$enderecoAtualCompleto || ($enderecoNovoCompleto && $enderecoAtual !== $enderecoNovo)) {
 $camposEndereco = ['endereco_rua', 'endereco_numero', 'endereco_complemento', 'endereco_bairro', 'endereco_cidade', 'endereco_cep'];
 foreach ($camposEndereco as $campo) {
 if ($request->filled($campo) && $cliente->{$campo} !== $request->{$campo}) {
 $dadosAtualizacao[$campo] = $request->{$campo};
 $mudancas[] = $campo;
 }
 }
 }
 if (!empty($dadosAtualizacao)) {
 $cliente->update($dadosAtualizacao);
 }
 return response()->json([
 'success' => true,
 'message' => 'Cliente encontrado' . (!empty($mudancas) ? ' e atualizado' : ''),
 'data' => $cliente->fresh(),
 'created' => false,
 'updated' => !empty($mudancas),
 'changes' => $mudancas,
 'endereco_completo' => $cliente->endereco_completo
 ]);
 }
 if ($request->filled('email')) {
 $emailExiste = Cliente::where('email', $request->email)->exists();
 if ($emailExiste) {
 return response()->json([
 'success' => false,
 'message' => 'Email já cadastrado para outro cliente. Telefone será usado para criar novo cadastro.',
 'suggestion' => 'use_phone_only'
 ], 409);
 }
 }
 $dadosCliente = $request->only([
 'nome', 'telefone', 'email', 'endereco_rua', 'endereco_numero',
 'endereco_complemento', 'endereco_bairro', 'endereco_cidade', 'endereco_cep'
 ]);
 $cliente = Cliente::create($dadosCliente);
 return response()->json([
 'success' => true,
 'message' => 'Cliente criado com sucesso para delivery',
 'data' => $cliente,
 'created' => true,
 'endereco_completo' => $cliente->endereco_completo
 ], 201);
 }
 public function buscarParaDelivery(Request $request)
 {
 $validator = Validator::make($request->all(), [
 'busca' => 'required|string|min:3',
 'limite' => 'sometimes|integer|min:1|max:50',
 ]);
 if ($validator->fails()) {
 return response()->json([
 'success' => false,
 'message' => 'Termo de busca deve ter pelo menos 3 caracteres',
 'errors' => $validator->errors()
 ], 400);
 }
 $termo = $request->busca;
 $limite = $request->get('limite', 20);
 $query = Cliente::where('ativo', true)
 ->where(function($q) use ($termo) {
 $telefoneNumerico = preg_replace('/\D/', '', $termo);
 if (strlen($telefoneNumerico) >= 8) {
 $q->orWhere('telefone', 'like', "%{$telefoneNumerico}%");
 }
 $q->orWhere('nome', 'like', "%{$termo}%");
 $q->orWhere('email', 'like', "%{$termo}%");
 $q->orWhere('endereco_rua', 'like', "%{$termo}%")
 ->orWhere('endereco_bairro', 'like', "%{$termo}%")
 ->orWhere('endereco_cidade', 'like', "%{$termo}%")
 ->orWhere('endereco_cep', 'like', "%{$termo}%");
 })
 ->with('deliveries')
 ->orderByRaw("CASE 
 WHEN telefone LIKE '%{$termo}%' THEN 1
 WHEN nome LIKE '%{$termo}%' THEN 2
 WHEN email LIKE '%{$termo}%' THEN 3
 ELSE 4 END")
 ->orderBy('nome')
 ->limit($limite);
 $clientes = $query->get();
 $clientesFormatados = $clientes->map(function($cliente) {
 return [
 'id' => $cliente->id,
 'nome' => $cliente->nome,
 'telefone' => $cliente->telefone,
 'email' => $cliente->email,
 'endereco_completo' => $cliente->endereco_completo,
 'endereco' => [
 'rua' => $cliente->endereco_rua,
 'numero' => $cliente->endereco_numero,
 'complemento' => $cliente->endereco_complemento,
 'bairro' => $cliente->endereco_bairro,
 'cidade' => $cliente->endereco_cidade,
 'cep' => $cliente->endereco_cep,
 ],
 'tem_endereco' => !empty($cliente->endereco_rua),
 'total_pedidos' => $cliente->deliveries->count(),
 'ultimo_pedido' => $cliente->deliveries->sortByDesc('created_at')->first() 
 ? $cliente->deliveries->sortByDesc('created_at')->first()->created_at->diffForHumans() 
 : null,
 'created_at' => $cliente->created_at->format('d/m/Y')
 ];
 });
 return response()->json([
 'success' => true,
 'data' => $clientesFormatados,
 'total' => $clientesFormatados->count(),
 'sugestoes' => $this->gerarSugestoes($termo, $clientes)
 ]);
 }
 private function gerarSugestoes($termo, $clientes)
 {
 $sugestoes = [];
 $telefoneNumerico = preg_replace('/\D/', '', $termo);
 if (strlen($telefoneNumerico) >= 8 && $clientes->isEmpty()) {
 $sugestoes[] = [
 'tipo' => 'criar_cliente',
 'texto' => 'Criar novo cliente com telefone ' . $termo,
 'dados' => [
 'telefone' => $termo
 ]
 ];
 }
 $clientesSemEndereco = $clientes->filter(function($cliente) {
 return empty($cliente->endereco_rua);
 });
 if ($clientesSemEndereco->isNotEmpty()) {
 $sugestoes[] = [
 'tipo' => 'atualizar_endereco',
 'texto' => 'Alguns clientes não possuem endereço cadastrado',
 'clientes' => $clientesSemEndereco->pluck('id')
 ];
 }
 return $sugestoes;
 }
}