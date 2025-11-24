@extends('layouts.app')

@section('title', 'Novo Combo')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-plus-circle me-2"></i>
                    Novo Combo
                </h1>
                <p class="page-subtitle">Crie uma oferta especial com múltiplos produtos</p>
            </div>
            <a href="{{ route('combos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erro ao cadastrar combo!</h5>
                    <hr>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('combos.store') }}" method="POST" enctype="multipart/form-data" id="comboForm">
                @csrf
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Nome do Combo *</label>
                            <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" 
                                   value="{{ old('nome') }}" required>
                            @error('nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="descricao" class="form-control @error('descricao') is-invalid @enderror" 
                                      rows="3">{{ old('descricao') }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Imagem do Combo <small class="text-muted">(Opcional)</small></label>
                            <input type="file" name="imagem" id="imagem" class="form-control @error('imagem') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif">
                            <small class="text-muted">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</small>
                            @error('imagem')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="imagemPreview" class="mt-2" style="display: none;">
                                <img id="imagemPreviewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="destaque" id="destaque" value="1" {{ old('destaque') ? 'checked' : '' }}>
                                <label class="form-check-label" for="destaque">
                                    <strong><i class="fas fa-star text-warning me-1"></i>Destacar no App</strong>
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle me-1"></i>Combos em destaque aparecem no topo do app de delivery
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-calculator me-2"></i>Preços</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label small">Preço Original (calculado)</label>
                                    <input type="text" id="preco_original_display" class="form-control" readonly value="R$ 0,00">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small">Preço do Combo *</label>
                                    <input type="number" name="preco_combo" id="preco_combo" 
                                           class="form-control @error('preco_combo') is-invalid @enderror" 
                                           step="0.01" min="0" value="{{ old('preco_combo') }}" required>
                                    @error('preco_combo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div id="economia_info" class="alert alert-success small" style="display:none;">
                                    <strong>Desconto:</strong> <span id="desconto_percentual">0</span>%<br>
                                    <strong>Economia:</strong> R$ <span id="economia_valor">0,00</span>
                                </div>

                                <div id="alerta_roi" class="alert alert-warning small" style="display:none;">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <strong>Atenção: Desconto Alto!</strong>
                                    <p class="mb-1 mt-2">Descontos acima de 30% podem impactar negativamente seu ROI (Retorno sobre Investimento).</p>
                                </div>

                                <div id="alerta_destaque" class="alert alert-info small" style="display:none;">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    <strong>Dica de Marketing</strong>
                                    <p class="mb-1 mt-2">Combo em destaque aumenta visibilidade em até 3x no app. Recomendado para:</p>
                                    <ul class="mb-0 ps-3">
                                        <li>Lançamentos de produtos</li>
                                        <li>Promoções por tempo limitado</li>
                                        <li>Aumentar ticket médio</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mt-3">
                            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 16px;">
                                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Estratégia de Precificação</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th class="border-0 text-muted" style="width: 30%; font-size: 12px; font-weight: 600;">DESCONTO</th>
                                                <th class="border-0 text-muted" style="font-size: 12px; font-weight: 600;">RECOMENDAÇÃO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge bg-success" style="font-size: 13px; padding: 6px 12px; font-weight: 500;">10-15%</span></td>
                                                <td class="small" style="line-height: 1.6;">Margem de lucro saudável, baixo risco</td>
                                            </tr>
                                            <tr style="background-color: #f0f9ff;">
                                                <td><span class="badge bg-primary" style="font-size: 13px; padding: 6px 12px; font-weight: 500;">15-20% ⭐</span></td>
                                                <td class="small" style="line-height: 1.6;"><strong class="text-primary">IDEAL:</strong> Atrativo, bom ROI, alta conversão</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-warning text-dark" style="font-size: 13px; padding: 6px 12px; font-weight: 500;">20-30%</span></td>
                                                <td class="small" style="line-height: 1.6;">Agressivo, requer monitoramento constante</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-danger" style="font-size: 13px; padding: 6px 12px; font-weight: 500;">&gt;30%</span></td>
                                                <td class="small" style="line-height: 1.6;"><strong class="text-danger">CUIDADO:</strong> Reduz lucro significativamente</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-info mb-0 mt-3" style="background-color: #e3f2fd; border-left: 4px solid #2196F3; padding: 12px;">
                                    <i class="fas fa-lightbulb text-primary me-2"></i>
                                    <small style="line-height: 1.6;"><strong>Dica Profissional:</strong> Combos em destaque com 15-20% de desconto têm <strong>3x mais conversão</strong> e mantém ROI positivo.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3"><i class="fas fa-box-open me-2"></i>Produtos do Combo *</h5>
                
                <div class="row mb-3">
                    <div class="col-md-8">
                        <select id="produto_select" class="form-select">
                            <option value="">Selecione um produto...</option>
                            @foreach($produtos as $produto)
                                <option value="{{ $produto->id }}" data-preco="{{ $produto->preco }}" data-nome="{{ $produto->nome }}">
                                    {{ $produto->nome }} - R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" id="quantidade_input" class="form-control" value="1" min="1" placeholder="Qtd">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-success w-100" onclick="adicionarProduto()">
                            <i class="fas fa-plus"></i> Adicionar
                        </button>
                    </div>
                </div>

                <div id="produtos_selecionados" class="mb-3">
                    <!-- Produtos serão adicionados aqui via JS -->
                </div>

                @error('produtos')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('combos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Salvar Combo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let produtosSelecionados = [];

// Preview da imagem
document.getElementById('imagem').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Valida tamanho (2MB = 2097152 bytes)
        if (file.size > 2097152) {
            alert('A imagem não pode ser maior que 2MB!');
            e.target.value = '';
            document.getElementById('imagemPreview').style.display = 'none';
            return;
        }
        
        // Valida tipo
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            alert('Formato inválido! Use JPG, PNG ou GIF.');
            e.target.value = '';
            document.getElementById('imagemPreview').style.display = 'none';
            return;
        }
        
        // Mostra preview
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagemPreviewImg').src = e.target.result;
            document.getElementById('imagemPreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('imagemPreview').style.display = 'none';
    }
});

function adicionarProduto() {
    const select = document.getElementById('produto_select');
    const quantidade = parseInt(document.getElementById('quantidade_input').value);
    
    if (!select.value || quantidade < 1) {
        alert('Selecione um produto e quantidade válida');
        return;
    }

    const option = select.options[select.selectedIndex];
    const produto = {
        id: select.value,
        nome: option.dataset.nome,
        preco: parseFloat(option.dataset.preco),
        quantidade: quantidade
    };

    // Verifica se já existe
    const index = produtosSelecionados.findIndex(p => p.id === produto.id);
    if (index >= 0) {
        produtosSelecionados[index].quantidade += quantidade;
    } else {
        produtosSelecionados.push(produto);
    }

    renderizarProdutos();
    calcularPrecos();
    
    select.value = '';
    document.getElementById('quantidade_input').value = 1;
}

function removerProduto(id) {
    produtosSelecionados = produtosSelecionados.filter(p => p.id !== id);
    renderizarProdutos();
    calcularPrecos();
}

function renderizarProdutos() {
    const container = document.getElementById('produtos_selecionados');
    
    if (produtosSelecionados.length === 0) {
        container.innerHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Nenhum produto adicionado</div>';
        return;
    }

    let html = '<div class="list-group">';
    produtosSelecionados.forEach(produto => {
        const subtotal = produto.preco * produto.quantidade;
        html += `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>${produto.quantidade}x ${produto.nome}</strong>
                    <br>
                    <small class="text-muted">R$ ${produto.preco.toFixed(2).replace('.', ',')} cada</small>
                </div>
                <div class="text-end">
                    <div class="mb-1">R$ ${subtotal.toFixed(2).replace('.', ',')}</div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerProduto('${produto.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

function calcularPrecos() {
    const precoOriginal = produtosSelecionados.reduce((sum, p) => sum + (p.preco * p.quantidade), 0);
    document.getElementById('preco_original_display').value = 'R$ ' + precoOriginal.toFixed(2).replace('.', ',');
    
    const precoCombo = parseFloat(document.getElementById('preco_combo').value) || 0;
    
    if (precoCombo > 0 && precoOriginal > 0) {
        const economia = precoOriginal - precoCombo;
        const desconto = Math.round((economia / precoOriginal) * 100);
        
        document.getElementById('desconto_percentual').textContent = desconto;
        document.getElementById('economia_valor').textContent = economia.toFixed(2).replace('.', ',');
        document.getElementById('economia_info').style.display = 'block';
        
        // Alerta de ROI para descontos altos
        if (desconto > 30) {
            document.getElementById('alerta_roi').style.display = 'block';
        } else {
            document.getElementById('alerta_roi').style.display = 'none';
        }
    } else {
        document.getElementById('economia_info').style.display = 'none';
        document.getElementById('alerta_roi').style.display = 'none';
    }
}

document.getElementById('preco_combo').addEventListener('input', calcularPrecos);

// Alerta sobre destaque no app
document.getElementById('destaque').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('alerta_destaque').style.display = 'block';
    } else {
        document.getElementById('alerta_destaque').style.display = 'none';
    }
});

document.getElementById('comboForm').addEventListener('submit', function(e) {
    if (produtosSelecionados.length === 0) {
        e.preventDefault();
        alert('Adicione pelo menos um produto ao combo!');
        return false;
    }

    // Adiciona inputs hidden com os produtos
    produtosSelecionados.forEach((produto, index) => {
        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = `produtos[${index}][id]`;
        inputId.value = produto.id;
        
        const inputQtd = document.createElement('input');
        inputQtd.type = 'hidden';
        inputQtd.name = `produtos[${index}][quantidade]`;
        inputQtd.value = produto.quantidade;
        
        this.appendChild(inputId);
        this.appendChild(inputQtd);
    });
});

// Inicializa
renderizarProdutos();
</script>
@endsection
