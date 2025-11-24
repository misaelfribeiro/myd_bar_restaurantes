<?php
namespace App\Http\Controllers;
use App\Models\Cliente;
use App\Models\Delivery;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ClienteController extends Controller
{
 public function index(Request $request)
 {
 $query = Cliente::query();
 if ($request->filled('search')) {
 $search = $request->get('search');
 $query->where(function ($q) use ($search) {
 $q->where('nome', 'like', "%{$search}%")
 ->orWhere('email', 'like', "%{$search}%")
 ->orWhere('telefone', 'like', "%{$search}%");
 });
 }
 if ($request->filled('status')) {
 if ($request->get('status') === 'ativo') {
 $query->where('ativo', true);
 } elseif ($request->get('status') === 'inativo') {
 $query->where('ativo', false);
 }
 }
 $clientes = $query->withCount(['deliveries', 'pedidos'])
 ->orderBy('nome')
 ->paginate(15);
 return view('clientes.index', compact('clientes'));
 }
 public function create()
 {
 return view('clientes.create');
 }
 public function store(Request $request)
 {
 $request->validate([
 'nome' => 'required|string|max:255',
 'email' => 'nullable|email|unique:clientes,email',
 'telefone' => 'required|string|max:20',
 'endereco_rua' => 'nullable|string|max:255',
 'endereco_numero' => 'nullable|string|max:20',
 'endereco_complemento' => 'nullable|string|max:255',
 'endereco_bairro' => 'nullable|string|max:100',
 'endereco_cidade' => 'nullable|string|max:100',
 'endereco_cep' => 'nullable|string|max:10',
 'cpf' => 'nullable|string|max:14',
 'data_nascimento' => 'nullable|date',
 'observacoes' => 'nullable|string',
 ]);
 Cliente::create($request->only([
 'nome', 'email', 'telefone', 'endereco_rua', 'endereco_numero',
 'endereco_complemento', 'endereco_bairro', 'endereco_cidade', 'endereco_cep',
 'cpf', 'data_nascimento', 'observacoes'
 ]));
 return redirect()->route('clientes.index')->with('success', 'Cliente cadastrado com sucesso!');
 }
 public function show(Cliente $cliente)
 {
 $estatisticas = [
 'total_pedidos' => $cliente->pedidos()->count(),
 'total_deliveries' => $cliente->deliveries()->count(),
 'valor_total_pedidos' => $cliente->pedidos()->sum('total'),
 'ultimo_pedido' => $cliente->pedidos()->latest()->first(),
 ];
 $ultimosPedidos = $cliente->pedidos()
 ->with(['itemPedidos.produto', 'mesa', 'entregador'])
 ->latest()
 ->take(5)
 ->get();
 $ultimasDeliveries = $cliente->deliveries()
 ->with('pedido')
 ->latest()
 ->take(5)
 ->get();
 return view('clientes.show', compact('cliente', 'estatisticas', 'ultimosPedidos', 'ultimasDeliveries'));
 }
 public function edit(Cliente $cliente)
 {
 return view('clientes.edit', compact('cliente'));
 }
 public function update(Request $request, Cliente $cliente)
 {
 $request->validate([
 'nome' => 'required|string|max:255',
 'email' => 'nullable|email|unique:clientes,email,' . $cliente->id,
 'telefone' => 'required|string|max:20',
 'endereco_rua' => 'nullable|string|max:255',
 'endereco_numero' => 'nullable|string|max:20',
 'endereco_complemento' => 'nullable|string|max:255',
 'endereco_bairro' => 'nullable|string|max:100',
 'endereco_cidade' => 'nullable|string|max:100',
 'endereco_cep' => 'nullable|string|max:10',
 'cpf' => 'nullable|string|max:14',
 'data_nascimento' => 'nullable|date',
 'observacoes' => 'nullable|string',
 'ativo' => 'boolean',
 ]);
 $cliente->update($request->only([
 'nome', 'email', 'telefone', 'endereco_rua', 'endereco_numero',
 'endereco_complemento', 'endereco_bairro', 'endereco_cidade', 'endereco_cep',
 'cpf', 'data_nascimento', 'observacoes', 'ativo'
 ]));
 return redirect()->route('clientes.index')->with('success', 'Cliente atualizado com sucesso!');
 }
 public function destroy(Cliente $cliente)
 {
 if ($cliente->pedidos()->exists() || $cliente->deliveries()->exists()) {
 return redirect()->route('clientes.index')->with('error', 'Não é possível excluir cliente com pedidos ou deliveries associados.');
 }
 $cliente->delete();
 return redirect()->route('clientes.index')->with('success', 'Cliente removido com sucesso!');
 }
 public function toggleStatus(Cliente $cliente)
 {
 $cliente->update(['ativo' => !$cliente->ativo]);
 $status = $cliente->ativo ? 'ativado' : 'desativado';
 return redirect()->back()->with('success', "Cliente {$status} com sucesso!");
 }
}