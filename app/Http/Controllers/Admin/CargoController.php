<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Cargo;
use Illuminate\Http\Request;
class CargoController extends Controller
{
 public function index()
 {
 $cargos = Cargo::withCount('funcionarios')->orderBy('nivel_hierarquico', 'desc')->get();
 return view('admin.cargos.index', compact('cargos'));
 }
 public function create()
 {
 return view('admin.cargos.create');
 }
 public function store(Request $request)
 {
 $validated = $request->validate([
 'nome' => 'required|string|max:255',
 'descricao' => 'nullable|string',
 'nivel_hierarquico' => 'required|integer|min:1|max:5',
 'salario_base' => 'nullable|numeric|min:0',
 'percentual_comissao' => 'nullable|numeric|min:0|max:100',
 'tem_comissao' => 'nullable|boolean',
 'ativo' => 'nullable|boolean',
 ]);
 $validated['tem_comissao'] = $request->has('tem_comissao');
 $validated['ativo'] = $request->has('ativo');
 Cargo::create($validated);
 return redirect()->route('admin.cargos.index')
 ->with('success', 'Cargo cadastrado com sucesso!');
 }
 public function edit($id)
 {
 $cargo = Cargo::findOrFail($id);
 return view('admin.cargos.edit', compact('cargo'));
 }
 public function update(Request $request, $id)
 {
 $cargo = Cargo::findOrFail($id);
 $validated = $request->validate([
 'nome' => 'required|string|max:255',
 'descricao' => 'nullable|string',
 'nivel_hierarquico' => 'required|integer|min:1|max:5',
 'salario_base' => 'nullable|numeric|min:0',
 'percentual_comissao' => 'nullable|numeric|min:0|max:100',
 'tem_comissao' => 'nullable|boolean',
 'ativo' => 'nullable|boolean',
 ]);
 $validated['tem_comissao'] = $request->has('tem_comissao');
 $validated['ativo'] = $request->has('ativo');
 $cargo->update($validated);
 return redirect()->route('admin.cargos.index')
 ->with('success', 'Cargo atualizado com sucesso!');
 }
 public function destroy($id)
 {
 $cargo = Cargo::findOrFail($id);
 if ($cargo->funcionarios()->count() > 0) {
 return back()->with('error', 'Não é possível excluir um cargo com funcionários vinculados.');
 }
 $cargo->delete();
 return redirect()->route('admin.cargos.index')
 ->with('success', 'Cargo excluído com sucesso!');
 }
}