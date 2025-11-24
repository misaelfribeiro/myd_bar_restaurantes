<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Funcionario;
use App\Models\Cargo;
use Illuminate\Http\Request;
class FuncionarioController extends Controller
{
 public function index()
 {
 $funcionarios = Funcionario::with('cargo')->orderBy('nome_completo', 'asc')->get();
 return view('admin.funcionarios.index', compact('funcionarios'));
 }
 public function create()
 {
 $cargos = Cargo::where('ativo', true)->orderBy('nome')->get();
 return view('admin.funcionarios.create', compact('cargos'));
 }
 public function store(Request $request)
 {
 try {
 $validated = $request->validate([
 'nome_completo' => 'required|string|max:255',
 'cargo_id' => 'required|exists:cargos,id',
 'cpf' => 'required|string|size:14|unique:funcionarios,cpf',
 'rg' => 'nullable|string|max:20',
 'data_nascimento' => 'required|date',
 'email' => 'required|email|unique:funcionarios,email',
 'telefone' => 'nullable|string|max:20',
 'celular' => 'nullable|string|max:20',
 'cep' => 'nullable|string|max:10',
 'endereco' => 'nullable|string|max:255',
 'cidade' => 'nullable|string|max:100',
 'estado' => 'nullable|string|max:2',
 'data_admissao' => 'required|date',
 'salario' => 'nullable|numeric|min:0',
 'tipo_comissao' => 'nullable|in:percentual,fixa',
 'percentual_comissao' => 'nullable|numeric|min:0|max:100',
 'observacoes' => 'nullable|string',
 ]);
 $validated['ativo'] = $request->has('ativo');
 $validated['status'] = 'ativo';
 $validated['tipo_contrato'] = 'clt';
 $funcionario = Funcionario::create($validated);
 return redirect()->route('admin.funcionarios.index')
 ->with('success', 'Funcionário cadastrado com sucesso!');
 } catch (\Illuminate\Validation\ValidationException $e) {
 return redirect()->back()
 ->withInput()
 ->withErrors($e->errors());
 } catch (\Exception $e) {
 \Log::error('Erro ao criar funcionário: ' . $e->getMessage());
 \Log::error('Stack trace: ' . $e->getTraceAsString());
 return redirect()->back()
 ->withInput()
 ->with('error', 'Erro ao cadastrar funcionário: ' . $e->getMessage());
 }
 }
 public function show($id)
 {
 $funcionario = Funcionario::with(['cargo', 'comissoes', 'bonus'])->findOrFail($id);
 return view('admin.funcionarios.show', compact('funcionario'));
 }
 public function edit($id)
 {
 $funcionario = Funcionario::findOrFail($id);
 $cargos = Cargo::where('ativo', true)->orderBy('nome')->get();
 return view('admin.funcionarios.edit', compact('funcionario', 'cargos'));
 }
 public function update(Request $request, $id)
 {
 $funcionario = Funcionario::findOrFail($id);
 $validated = $request->validate([
 'nome_completo' => 'required|string|max:255',
 'cargo_id' => 'required|exists:cargos,id',
 'cpf' => 'required|string|size:14|unique:funcionarios,cpf,' . $id,
 'rg' => 'nullable|string|max:20',
 'data_nascimento' => 'required|date',
 'email' => 'required|email|unique:funcionarios,email,' . $id,
 'telefone' => 'nullable|string|max:20',
 'celular' => 'nullable|string|max:20',
 'cep' => 'nullable|string|max:10',
 'endereco' => 'nullable|string|max:255',
 'cidade' => 'nullable|string|max:100',
 'estado' => 'nullable|string|max:2',
 'data_admissao' => 'required|date',
 'data_demissao' => 'nullable|date',
 'salario' => 'nullable|numeric|min:0',
 'tipo_comissao' => 'nullable|in:percentual,fixa',
 'percentual_comissao' => 'nullable|numeric|min:0|max:100',
 'observacoes' => 'nullable|string',
 ]);
 $validated['ativo'] = $request->has('ativo');
 $funcionario->update($validated);
 return redirect()->route('admin.funcionarios.index')
 ->with('success', 'Funcionário atualizado com sucesso!');
 }
 public function destroy($id)
 {
 $funcionario = Funcionario::findOrFail($id);
 if ($funcionario->comissoes()->count() > 0 || $funcionario->bonus()->count() > 0) {
 return redirect()->route('admin.funcionarios.index')
 ->with('error', 'Não é possível excluir este funcionário pois possui comissões ou bônus vinculados.');
 }
 $funcionario->delete();
 return redirect()->route('admin.funcionarios.index')
 ->with('success', 'Funcionário excluído com sucesso!');
 }
}