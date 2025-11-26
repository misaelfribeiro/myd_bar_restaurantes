<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Comissao;
use App\Models\Funcionario;
use Illuminate\Http\Request;
class ComissaoController extends Controller
{
 public function index(Request $request)
 {
 $query = Comissao::with('funcionario');
 if ($request->filled('funcionario_id')) {
 $query->where('funcionario_id', $request->funcionario_id);
 }
 if ($request->filled('status')) {
 $query->where('status', $request->status);
 }
 if ($request->filled('mes')) {
 $query->whereMonth('data_referencia', $request->mes);
 }
 $comissoes = $query->orderBy('data_referencia', 'desc')->get();
 $funcionarios = Funcionario::where('ativo', true)->orderBy('nome_completo')->get();
 return view('admin.comissoes.index', compact('comissoes', 'funcionarios'));
 }
 public function create()
 {
 $funcionarios = Funcionario::where('ativo', true)->orderBy('nome_completo')->get();
 return view('admin.comissoes.create', compact('funcionarios'));
 }
 public function store(Request $request)
 {
 $validated = $request->validate([
 'funcionario_id' => 'required|exists:funcionarios,id',
 'tipo' => 'required|string|in:venda,meta,projeto,extra',
 'descricao' => 'required|string|max:255',
 'data_referencia' => 'required|date',
 'valor_base' => 'required|numeric|min:0',
 'percentual' => 'required|numeric|min:0|max:100',
 'status' => 'required|in:pendente,pago,cancelado',
 'data_pagamento' => 'nullable|date',
 'observacoes' => 'nullable|string',
 ]);
 $validated['valor_comissao'] = ($validated['valor_base'] * $validated['percentual']) / 100;
 Comissao::create($validated);
 return redirect()->route('admin.comissoes.index')
 ->with('success', 'Comissão cadastrada com sucesso!');
 }
 
 public function edit($id)
 {
 $comissao = Comissao::findOrFail($id);
 $funcionarios = Funcionario::where('ativo', true)->orderBy('nome_completo')->get();
 return view('admin.comissoes.edit', compact('comissao', 'funcionarios'));
 }
 public function update(Request $request, $id)
 {
 $comissao = Comissao::findOrFail($id);
 $validated = $request->validate([
 'funcionario_id' => 'required|exists:funcionarios,id',
 'tipo' => 'required|string|in:venda,meta,projeto,extra',
 'descricao' => 'required|string|max:255',
 'data_referencia' => 'required|date',
 'valor_base' => 'required|numeric|min:0',
 'percentual' => 'required|numeric|min:0|max:100',
 'status' => 'required|in:pendente,pago,cancelado',
 'data_pagamento' => 'nullable|date',
 'observacoes' => 'nullable|string',
 ]);
 $validated['valor_comissao'] = ($validated['valor_base'] * $validated['percentual']) / 100;
 $comissao->update($validated);
 return redirect()->route('admin.comissoes.index')
 ->with('success', 'Comissão atualizada com sucesso!');
 }
 public function destroy($id)
 {
 $comissao = Comissao::findOrFail($id);
 $comissao->delete();
 return redirect()->route('admin.comissoes.index')
 ->with('success', 'Comissão excluída com sucesso!');
 }
}