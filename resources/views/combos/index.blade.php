@extends('layouts.app')

@section('title', 'Combos')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-fire me-2"></i>
                    Combos e Promoções
                </h1>
                <p class="page-subtitle">Gerencie combos e ofertas especiais</p>
            </div>
            <a href="{{ route('combos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Novo Combo
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        @forelse($combos as $combo)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm {{ !$combo->ativo ? 'opacity-50' : '' }}">
                    @if($combo->imagem)
                        <img src="{{ asset('storage/' . $combo->imagem) }}" class="card-img-top" alt="{{ $combo->nome }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-gradient-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-hamburger fa-4x text-white opacity-50"></i>
                        </div>
                    @endif
                    
                    @if($combo->desconto > 0)
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-danger fs-5">-{{ $combo->desconto }}%</span>
                        </div>
                    @endif

                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $combo->nome }}</h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       id="ativo_{{ $combo->id }}" 
                                       {{ $combo->ativo ? 'checked' : '' }}
                                       onchange="toggleStatus({{ $combo->id }})">
                            </div>
                        </div>

                        @if($combo->descricao)
                            <p class="card-text text-muted small">{{ Str::limit($combo->descricao, 80) }}</p>
                        @endif

                        <div class="mb-3">
                            <h6 class="text-muted mb-2"><i class="fas fa-box me-1"></i>Itens do Combo:</h6>
                            <ul class="list-unstyled small">
                                @foreach($combo->produtos as $produto)
                                    <li>
                                        <i class="fas fa-check text-success me-1"></i>
                                        {{ $produto->pivot->quantidade }}x {{ $produto->nome }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Preço Normal:</span>
                                <span class="text-decoration-line-through text-muted">R$ {{ number_format($combo->preco_original, 2, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Preço Combo:</span>
                                <span class="text-success fw-bold fs-5">R$ {{ number_format($combo->preco_combo, 2, ',', '.') }}</span>
                            </div>
                            @if($combo->economia > 0)
                                <div class="text-center mt-2">
                                    <small class="badge bg-success">Economize R$ {{ number_format($combo->economia, 2, ',', '.') }}</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer bg-transparent border-top-0">
                        <div class="btn-group w-100">
                            <a href="{{ route('combos.edit', $combo) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button onclick="deleteCombo({{ $combo->id }})" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Nenhum combo cadastrado. <a href="{{ route('combos.create') }}">Criar primeiro combo</a>
                </div>
            </div>
        @endforelse
    </div>
</div>

<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
function toggleStatus(id) {
    fetch(`/combos/${id}/toggle-status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deleteCombo(id) {
    if (confirm('Tem certeza que deseja excluir este combo?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/combos/${id}`;
        form.submit();
    }
}
</script>
@endsection
