@extends('layouts.app')

@section('title', 'Gerenciar Permissões - ' . $usuario->nome)

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-shield-alt me-2"></i>
                    Gerenciar Permissões
                </h1>
                <p class="page-subtitle">
                    Usuário: <strong>{{ $usuario->nome }}</strong> 
                    <span class="badge bg-primary ms-2">{{ ucfirst($usuario->role) }}</span>
                </p>
            </div>
            <div>
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Sistema de Permissões Individuais:</strong> Configure exatamente o que este usuário pode visualizar, criar, editar ou excluir em cada módulo do sistema. As permissões aqui sobrescrevem as permissões padrão do perfil.
            </div>

            <form action="{{ route('usuarios.permissoes.update', $usuario->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-lock me-2"></i>
                            Permissões por Módulo
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 px-4 py-3" style="width: 25%;">Módulo</th>
                                        <th class="border-0 px-4 py-3 text-center">
                                            <i class="fas fa-eye me-1"></i>
                                            Visualizar
                                        </th>
                                        <th class="border-0 px-4 py-3 text-center">
                                            <i class="fas fa-plus me-1"></i>
                                            Criar
                                        </th>
                                        <th class="border-0 px-4 py-3 text-center">
                                            <i class="fas fa-edit me-1"></i>
                                            Editar
                                        </th>
                                        <th class="border-0 px-4 py-3 text-center">
                                            <i class="fas fa-trash me-1"></i>
                                            Excluir
                                        </th>
                                        <th class="border-0 px-4 py-3 text-center">
                                            <i class="fas fa-check-double me-1"></i>
                                            Selecionar Todos
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $modulosInfo = [
                                            'dashboard' => ['label' => 'Dashboard', 'icon' => 'tachometer-alt', 'color' => 'primary'],
                                            'pedidos' => ['label' => 'Pedidos', 'icon' => 'shopping-cart', 'color' => 'success'],
                                            'produtos' => ['label' => 'Produtos', 'icon' => 'box', 'color' => 'info'],
                                            'categorias' => ['label' => 'Categorias', 'icon' => 'tags', 'color' => 'warning'],
                                            'mesas' => ['label' => 'Mesas', 'icon' => 'chair', 'color' => 'secondary'],
                                            'usuarios' => ['label' => 'Usuários', 'icon' => 'users', 'color' => 'danger'],
                                            'empresas' => ['label' => 'Empresas (Master)', 'icon' => 'building', 'color' => 'dark'],
                                            'relatorios' => ['label' => 'Relatórios', 'icon' => 'chart-line', 'color' => 'purple'],
                                            'caixa' => ['label' => 'Caixa', 'icon' => 'cash-register', 'color' => 'success'],
                                            'delivery' => ['label' => 'Delivery', 'icon' => 'motorcycle', 'color' => 'info'],
                                            'clientes' => ['label' => 'Clientes', 'icon' => 'user-friends', 'color' => 'primary']
                                        ];
                                    @endphp

                                    @foreach($modulos as $modulo)
                                        @php
                                            $info = $modulosInfo[$modulo] ?? ['label' => ucfirst($modulo), 'icon' => 'cog', 'color' => 'secondary'];
                                            $permissao = $usuario->permissoes->where('modulo', $modulo)->first();
                                        @endphp
                                        <tr class="modulo-row">
                                            <td class="px-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="module-icon bg-{{ $info['color'] }} bg-opacity-10 text-{{ $info['color'] }} me-3">
                                                        <i class="fas fa-{{ $info['icon'] }}"></i>
                                                    </div>
                                                    <strong>{{ $info['label'] }}</strong>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="permissoes[{{ $modulo }}][visualizar]"
                                                           id="perm_{{ $modulo }}_visualizar"
                                                           data-modulo="{{ $modulo }}"
                                                           data-acao="visualizar"
                                                           {{ $permissao && $permissao->visualizar ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="permissoes[{{ $modulo }}][criar]"
                                                           id="perm_{{ $modulo }}_criar"
                                                           data-modulo="{{ $modulo }}"
                                                           data-acao="criar"
                                                           {{ $permissao && $permissao->criar ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="permissoes[{{ $modulo }}][editar]"
                                                           id="perm_{{ $modulo }}_editar"
                                                           data-modulo="{{ $modulo }}"
                                                           data-acao="editar"
                                                           {{ $permissao && $permissao->editar ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="permissoes[{{ $modulo }}][excluir]"
                                                           id="perm_{{ $modulo }}_excluir"
                                                           data-modulo="{{ $modulo }}"
                                                           data-acao="excluir"
                                                           {{ $permissao && $permissao->excluir ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary select-all-modulo"
                                                        data-modulo="{{ $modulo }}"
                                                        onclick="toggleAllModulo('{{ $modulo }}')">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-outline-success me-2" onclick="selectAll()">
                                    <i class="fas fa-check-square me-2"></i>
                                    Marcar Todas
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="deselectAll()">
                                    <i class="fas fa-times-circle me-2"></i>
                                    Desmarcar Todas
                                </button>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Salvar Permissões
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.page-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.page-subtitle {
    color: #718096;
    margin-bottom: 0;
}

.module-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.form-check-input {
    width: 2.5rem;
    height: 1.25rem;
    cursor: pointer;
}

.form-check-input:checked {
    background-color: #10b981;
    border-color: #10b981;
}

.modulo-row:hover {
    background-color: #f7fafc;
}

.bg-purple {
    background-color: #9333ea;
}

.text-purple {
    color: #9333ea;
}

.bg-opacity-10 {
    opacity: 0.1;
}
</style>

<script>
function toggleAllModulo(modulo) {
    const checkboxes = document.querySelectorAll(`input[data-modulo="${modulo}"]`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
    });
}

function selectAll() {
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.checked = true;
    });
}

function deselectAll() {
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
    });
}

// Auto-check visualizar quando outras ações são marcadas
document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const modulo = this.dataset.modulo;
        const acao = this.dataset.acao;
        
        // Se marcou criar, editar ou excluir, marcar visualizar também
        if (this.checked && acao !== 'visualizar') {
            const visualizarCheckbox = document.getElementById(`perm_${modulo}_visualizar`);
            if (visualizarCheckbox && !visualizarCheckbox.checked) {
                visualizarCheckbox.checked = true;
            }
        }
    });
});
</script>
@endsection
