<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\Funcionario;
use Illuminate\Http\Request;
class BonusController extends Controller
{
 public function index(Request $request)
 {
 $query = Bonus::with('funcionario');
 if ($request->filled('funcionario_id')) {
 $query->where('funcionario_id', $request->funcionario_id);
 }
 if ($request->filled('tipo')) {
 $query->where('tipo', $request->tipo);
 }
 if ($request->filled('status')) {
 $query->where('status', $request->status);
 }
 $bonus = $query->orderBy('data_referencia', 'desc')->get();
 $funcionarios = Funcionario::where('ativo', true)->orderBy('nome_completo')->get();
 return view('admin.bonus.index', compact('bonus', 'funcionarios'));
 }
 public function create()
 {
 $funcionarios = Funcionario::where('ativo', true)->orderBy('nome_completo')->get();
 return view('admin.bonus.create', compact('funcionarios'));
 }
 public function store(Request $request)
 {
 $validated = $request->validate([
 'funcionario_id' => 'required|exists:funcionarios,id',
 'tipo' => 'required|string|max:50',
 'titulo' => 'required|string|max:255',
 'descricao' => 'required|string',
 'data_referencia' => 'required|date',
 'valor' => 'required|numeric|min:0',
 'status' => 'required|in:pendente,pago,cancelado',
 'data_pagamento' => 'nullable|date',
 'observacoes' => 'nullable|string',
 ]);
 Bonus::create($validated);
 return redirect()->route('admin.bonus.index')
 ->with('success', 'Bônus cadastrado com sucesso!');
 }
 public function edit($id)
 {
 $bonus = Bonus::findOrFail($id);
 $funcionarios = Funcionario::where('ativo', true)->orderBy('nome_completo')->get();
 return view('admin.bonus.edit', compact('bonus', 'funcionarios'));
 }
 public function update(Request $request, $id)
 {
 $bonus = Bonus::findOrFail($id);
 $validated = $request->validate([
 'funcionario_id' => 'required|exists:funcionarios,id',
 'tipo' => 'required|string|max:50',
 'titulo' => 'required|string|max:255',
 'descricao' => 'required|string',
 'data_referencia' => 'required|date',
 'valor' => 'required|numeric|min:0',
 'status' => 'required|in:pendente,pago,cancelado',
 'data_pagamento' => 'nullable|date',
 'observacoes' => 'nullable|string',
 ]);
 $bonus->update($validated);
 return redirect()->route('admin.bonus.index')
 ->with('success', 'Bônus atualizado com sucesso!');
 }
 public function destroy($id)
 {
 $bonus = Bonus::findOrFail($id);
 $bonus->delete();
 return redirect()->route('admin.bonus.index')
 ->with('success', 'Bônus excluído com sucesso!');
 }
}