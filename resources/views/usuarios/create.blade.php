@extends('layouts.app')

@section('title', 'Novo Usuário')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-user-plus me-2"></i>
                    Novo Usuário
                </h1>
                <p class="page-subtitle">Cadastre um novo usuário no sistema</p>
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
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações do Usuário
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('usuarios.store') }}" method="POST">
                        @csrf

                        <!-- Nome -->
                        <div class="mb-3">
                            <label for="nome" class="form-label">
                                Nome Completo <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nome') is-invalid @enderror" 
                                   id="nome" 
                                   name="nome" 
                                   value="{{ old('nome') }}" 
                                   required>
                            @error('nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Perfil/Role -->
                        <div class="mb-3">
                            <label for="role" class="form-label">
                                Perfil <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" 
                                    name="role" 
                                    required>
                                <option value="">Selecione um perfil</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                    <i class="fas fa-user-shield"></i> Administrador
                                </option>
                                <option value="garcom" {{ old('role') == 'garcom' ? 'selected' : '' }}>
                                    Garçom
                                </option>
                                <option value="cozinha" {{ old('role') == 'cozinha' ? 'selected' : '' }}>
                                    Cozinha
                                </option>
                                <option value="caixa" {{ old('role') == 'caixa' ? 'selected' : '' }}>
                                    Caixa
                                </option>
                                <option value="entregador" {{ old('role') == 'entregador' ? 'selected' : '' }}>
                                    Entregador
                                </option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Define o nível de acesso do usuário no sistema
                            </small>
                        </div>

                        @if($isMaster)
                        <!-- Empresa (apenas para Master) -->
                        <div class="mb-3">
                            <label for="tenant_code" class="form-label">
                                Empresa <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('tenant_code') is-invalid @enderror" 
                                    id="tenant_code" 
                                    name="tenant_code" 
                                    required>
                                <option value="">Selecione uma empresa</option>
                                @foreach($empresas as $empresa)
                                <option value="{{ $empresa->tenant_code }}" {{ old('tenant_code') == $empresa->tenant_code ? 'selected' : '' }}>
                                    {{ $empresa->nome_fantasia }} ({{ $empresa->tenant_code }})
                                </option>
                                @endforeach
                            </select>
                            @error('tenant_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-crown text-warning"></i>
                                Como Master da plataforma, você pode criar usuários para qualquer empresa
                            </small>
                        </div>
                        @endif

                        <!-- Senha -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Senha <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Mínimo de 6 caracteres
                            </small>
                        </div>

                        <!-- Confirmar Senha -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                Confirmar Senha <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Importante:</strong> Após criar o usuário, você poderá configurar permissões individuais acessando a opção "Editar Permissões" na listagem de usuários.
                        </div>

                        <!-- Botões -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Salvar Usuário
                            </button>
                            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
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

.form-label {
    font-weight: 500;
    color: #4a5568;
}
</style>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection
