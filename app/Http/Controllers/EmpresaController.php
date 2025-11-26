<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::with(['matriz', 'filiais'])
            ->orderBy('tipo')
            ->orderBy('nome_fantasia')
            ->paginate(15);

        return view('empresas.index', compact('empresas'));
    }

    public function create()
    {
        $matrizes = Empresa::matrizes()->ativas()->orderBy('nome_fantasia')->get();
        return view('empresas.create', compact('matrizes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome_fantasia' => 'required|string|max:255',
            'razao_social' => 'required|string|max:255',
            'cnpj' => 'required|string|unique:empresas,cnpj',
            'inscricao_estadual' => 'nullable|string|max:255',
            'inscricao_municipal' => 'nullable|string|max:255',
            'telefone' => 'required|string',
            'celular' => 'nullable|string',
            'email' => 'required|email|unique:empresas,email',
            'site' => 'nullable|url',
            'endereco_rua' => 'required|string|max:255',
            'endereco_numero' => 'required|string|max:20',
            'endereco_complemento' => 'nullable|string|max:255',
            'endereco_bairro' => 'required|string|max:255',
            'endereco_cidade' => 'required|string|max:255',
            'endereco_estado' => 'required|string|size:2',
            'endereco_cep' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'tipo' => 'required|in:matriz,filial',
            'empresa_matriz_id' => 'nullable|exists:empresas,id|required_if:tipo,filial',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'descricao' => 'nullable|string',
            'horario_abertura' => 'nullable|string',
            'horario_fechamento' => 'nullable|string',
            'dias_funcionamento' => 'nullable|array',
            'tipo_recebimento_pagamento' => 'nullable|in:manual,automatico',
            'aceita_delivery' => 'boolean',
            'taxa_entrega_padrao' => 'nullable|numeric|min:0',
            'raio_entrega_km' => 'nullable|numeric|min:0',
            'pedido_minimo' => 'nullable|numeric|min:0',
            'ativo' => 'boolean'
        ]);

        // Upload da logo
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('empresas/logos', 'public');
        }

        $empresa = Empresa::create($validated);

        return redirect()->route('empresas.show', $empresa->id)
            ->with('success', 'Empresa cadastrada com sucesso!');
    }

    public function show(Empresa $empresa)
    {
        $empresa->load(['matriz', 'filiais']);
        return view('empresas.show', compact('empresa'));
    }

    public function edit(Empresa $empresa)
    {
        $matrizes = Empresa::matrizes()
            ->ativas()
            ->where('id', '!=', $empresa->id)
            ->orderBy('nome_fantasia')
            ->get();
            
        return view('empresas.edit', compact('empresa', 'matrizes'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'nome_fantasia' => 'required|string|max:255',
            'razao_social' => 'required|string|max:255',
            'cnpj' => 'required|string|unique:empresas,cnpj,' . $empresa->id,
            'inscricao_estadual' => 'nullable|string|max:255',
            'inscricao_municipal' => 'nullable|string|max:255',
            'telefone' => 'required|string',
            'celular' => 'nullable|string',
            'email' => 'required|email|unique:empresas,email,' . $empresa->id,
            'site' => 'nullable|url',
            'endereco_rua' => 'required|string|max:255',
            'endereco_numero' => 'required|string|max:20',
            'endereco_complemento' => 'nullable|string|max:255',
            'endereco_bairro' => 'required|string|max:255',
            'endereco_cidade' => 'required|string|max:255',
            'endereco_estado' => 'required|string|size:2',
            'endereco_cep' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'tipo' => 'required|in:matriz,filial',
            'empresa_matriz_id' => 'nullable|exists:empresas,id|required_if:tipo,filial',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'descricao' => 'nullable|string',
            'horario_abertura' => 'nullable|string',
            'horario_fechamento' => 'nullable|string',
            'dias_funcionamento' => 'nullable|array',
            'tipo_recebimento_pagamento' => 'nullable|in:manual,automatico',
            'aceita_delivery' => 'boolean',
            'taxa_entrega_padrao' => 'nullable|numeric|min:0',
            'raio_entrega_km' => 'nullable|numeric|min:0',
            'pedido_minimo' => 'nullable|numeric|min:0',
            'ativo' => 'boolean'
        ]);

        // Upload da nova logo
        if ($request->hasFile('logo')) {
            // Deletar logo antiga
            if ($empresa->logo) {
                Storage::disk('public')->delete($empresa->logo);
            }
            $validated['logo'] = $request->file('logo')->store('empresas/logos', 'public');
        }

        $empresa->update($validated);

        return redirect()->route('empresas.show', $empresa->id)
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    public function destroy(Empresa $empresa)
    {
        // Verificar se tem filiais ativas
        if ($empresa->tipo === 'matriz' && $empresa->filiais()->count() > 0) {
            return redirect()->route('empresas.index')
                ->with('error', 'Não é possível excluir uma matriz que possui filiais cadastradas.');
        }

        // Deletar logo
        if ($empresa->logo) {
            Storage::disk('public')->delete($empresa->logo);
        }

        $empresa->delete();

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa excluída com sucesso!');
    }
}
