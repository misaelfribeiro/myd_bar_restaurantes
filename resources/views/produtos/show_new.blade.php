<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $produto->nome }} - Sistema Bar/Restaurante</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            border-radius: 0.5rem;
        }
        .card-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
        }
        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            border: none;
            color: white;
        }
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
            border: none;
        }
        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
        }
        .info-item {
            background: #ffffff;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .info-item h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .info-item .value {
            font-size: 1.1rem;
            font-weight: 500;
        }
        .badge-ativo {
            background: linear-gradient(135deg, #28a745, #20c997);
            font-size: 0.9rem;
        }
        .badge-inativo {
            background: linear-gradient(135deg, #6c757d, #495057);
            font-size: 0.9rem;
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .price-highlight {
            font-size: 2rem;
            font-weight: 700;
            color: #28a745;
        }
        .stats-card {
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
            border-radius: 0.5rem;
            padding: 1rem;
        }
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #495057;
        }
        .product-image {
            width: 120px;
            height: 120px;
            border-radius: 8px;
            object-fit: cover;
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #6c757d;
        }
        @media (max-width: 768px) {
            .header-actions {
                flex-direction: column;
                gap: 1rem;
            }
            .price-highlight {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-utensils me-2"></i>
                Sistema Bar/Restaurante
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('produtos.index') }}">
                    <i class="fas fa-box-open me-1"></i>Produtos
                </a>
                <a class="nav-link" href="/">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <div class="header-actions">
                            <h4 class="mb-0">
                                <i class="fas fa-eye me-2"></i>
                                Detalhes do Produto
                            </h4>
                            <div class="btn-group">
                                <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i>
                                    Editar
                                </a>
                                <a href="{{ route('produtos.index') }}" class="btn btn-light btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Voltar
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-1"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="fas fa-info-circle me-1"></i>
                                {{ session('info') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Cabeçalho com imagem e informações principais -->
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <div class="product-image mx-auto">
                                    <i class="fas fa-utensils"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h2 class="mb-2">{{ $produto->nome }}</h2>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-hashtag me-1"></i>
                                    ID: #{{ $produto->id }}
                                </p>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-calendar me-1"></i>
                                    Criado em: {{ $produto->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="price-highlight mb-2">{{ $produto->preco_formatado }}</div>
                                <span class="badge {{ $produto->ativo ? 'badge-ativo' : 'badge-inativo' }} fs-6">
                                    <i class="fas {{ $produto->ativo ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                    {{ $produto->status }}
                                </span>
                            </div>
                        </div>

                        <!-- Informações detalhadas -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <h6>
                                        <i class="fas fa-tags me-2"></i>
                                        Categoria
                                    </h6>
                                    <div class="value">
                                        @if($produto->categoria)
                                            <span class="badge bg-info fs-6">{{ $produto->categoria->nome }}</span>
                                        @else
                                            <span class="text-muted">Sem categoria</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <h6>
                                        <i class="fas fa-calendar-edit me-2"></i>
                                        Última Atualização
                                    </h6>
                                    <div class="value">
                                        {{ $produto->updated_at->format('d/m/Y') }}
                                        <small class="text-muted">às {{ $produto->updated_at->format('H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($produto->descricao)
                            <div class="info-item">
                                <h6>
                                    <i class="fas fa-align-left me-2"></i>
                                    Descrição
                                </h6>
                                <div class="value">{{ $produto->descricao }}</div>
                            </div>
                        @endif

                        <!-- Estatísticas de vendas -->
                        @php
                            $itensCount = $produto->itens ? $produto->itens->count() : 0;
                            $quantidadeVendida = $produto->itens ? $produto->itens->sum('quantidade') : 0;
                            $receitaTotal = $produto->itens ? $produto->itens->sum('subtotal') : 0;
                        @endphp

                        @if($itensCount > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="mb-3">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        Estatísticas de Vendas
                                    </h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-card text-center">
                                        <div class="stat-number">{{ $itensCount }}</div>
                                        <small class="text-muted">Vendas Totais</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-card text-center">
                                        <div class="stat-number">{{ $quantidadeVendida }}</div>
                                        <small class="text-muted">Unidades Vendidas</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-card text-center">
                                        <div class="stat-number">
                                            R$ {{ number_format($receitaTotal, 2, ',', '.') }}
                                        </div>
                                        <small class="text-muted">Receita Total</small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle me-1"></i>
                                Este produto ainda não foi vendido.
                            </div>
                        @endif

                        <!-- Ações -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-1"></i>
                                Excluir Produto
                            </button>
                            
                            <div class="d-grid gap-2 d-md-flex">
                                <a href="{{ route('produtos.index') }}" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-list me-1"></i>
                                    Lista de Produtos
                                </a>
                                <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i>
                                    Editar Produto
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmação de exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmar Exclusão
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        <strong>Tem certeza que deseja excluir este produto?</strong>
                    </p>
                    <div class="bg-light p-3 rounded">
                        <h6 class="mb-1">{{ $produto->nome }}</h6>
                        <small class="text-muted">
                            Preço: {{ $produto->preco_formatado }} | 
                            Categoria: {{ $produto->categoria ? $produto->categoria->nome : 'Sem categoria' }}
                        </small>
                    </div>
                    @if($itensCount > 0)
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Atenção:</strong> Este produto possui {{ $itensCount }} venda(s) registrada(s).
                        </div>
                    @endif
                    <p class="mt-3 mb-0 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        <strong>Atenção:</strong> Esta ação não pode ser desfeita.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" class="d-inline" id="formExcluir">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>
                            Excluir Definitivamente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Confirmação adicional antes de excluir
            const deleteForm = document.getElementById('formExcluir');
            
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    const hasItens = {{ $itensCount }};
                    
                    if (hasItens > 0) {
                        if (!confirm('Este produto possui vendas registradas. Confirma a exclusão?')) {
                            e.preventDefault();
                            return false;
                        }
                    }
                    
                    // Desabilitar botão para evitar duplo clique
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Excluindo...';
                    }
                });
            }

            // Auto-hide alerts após 5 segundos
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert.classList.contains('show')) {
                        alert.style.transition = 'opacity 0.3s';
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.remove();
                        }, 300);
                    }
                }, 5000);
            });
        });
    </script>
</body>
</html>
