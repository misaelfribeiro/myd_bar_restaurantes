@extends('layouts.app')

@section('title', 'Relatório de Transações')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">📊 Relatório de Transações</h1>
            <p class="text-muted mb-0">Análise detalhada de pagamentos e receitas</p>
        </div>
        <a href="{{ route('admin.financeiro.pagamentos.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="fas fa-filter"></i> Filtros</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.financeiro.pagamentos.relatorios') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control" 
                           value="{{ request('data_inicio', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" 
                           value="{{ request('data_fim', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Gerar Relatório
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Período do Relatório -->
    <div class="alert alert-info d-flex align-items-center mb-4">
        <i class="fas fa-calendar-alt fa-2x me-3"></i>
        <div>
            <strong>Período do Relatório:</strong><br>
            De {{ \Carbon\Carbon::parse($relatorio['periodo']['inicio'])->format('d/m/Y') }} 
            até {{ \Carbon\Carbon::parse($relatorio['periodo']['fim'])->format('d/m/Y') }}
            ({{ \Carbon\Carbon::parse($relatorio['periodo']['inicio'])->diffInDays(\Carbon\Carbon::parse($relatorio['periodo']['fim'])) + 1 }} dias)
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #667eea !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted mb-1 small">Total de Transações</p>
                            <h2 class="mb-0">{{ $relatorio['totais']['transacoes'] }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                        </div>
                    </div>
                    <small class="text-success">
                        <i class="fas fa-check-circle"></i> {{ $relatorio['totais']['aprovadas'] }} aprovadas
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #10b981 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted mb-1 small">Valor Total Aprovado</p>
                            <h2 class="mb-0">R$ {{ number_format($relatorio['totais']['valor_total'], 2, ',', '.') }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-dollar-sign fa-2x text-success"></i>
                        </div>
                    </div>
                    <small class="text-muted">Valor bruto das vendas</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #06b6d4 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted mb-1 small">Taxa Gestora</p>
                            <h2 class="mb-0">R$ {{ number_format($relatorio['totais']['taxa_plataforma'], 2, ',', '.') }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-percentage fa-2x text-info"></i>
                        </div>
                    </div>
                    <small class="text-muted">+ R$ {{ number_format($relatorio['totais']['taxa_entrega'] ?? 0, 2, ',', '.') }} entrega</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f59e0b !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted mb-1 small">Líquido Restaurantes</p>
                            <h2 class="mb-0">R$ {{ number_format($relatorio['totais']['valor_restaurantes'], 2, ',', '.') }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-store fa-2x text-warning"></i>
                        </div>
                    </div>
                    <small class="text-muted">90% - A pagar parceiros</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela por Restaurante -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Detalhamento por Restaurante</h5>
                <span class="badge bg-white text-primary">{{ $relatorio['por_restaurante']->count() }} restaurantes</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($relatorio['por_restaurante']->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="5%">#</th>
                            <th width="20%">Restaurante</th>
                            <th class="text-center" width="10%">
                                <i class="fas fa-shopping-cart"></i><br>
                                <small>Transações</small>
                            </th>
                            <th class="text-end" width="15%">
                                <i class="fas fa-dollar-sign"></i><br>
                                <small>Valor Bruto</small>
                            </th>
                            <th class="text-end" width="15%">
                                <i class="fas fa-percentage"></i><br>
                                <small>Taxa</small>
                            </th>
                            <th class="text-end" width="12%">
                                <i class="fas fa-truck"></i><br>
                                <small>Entrega</small>
                            </th>
                            <th class="text-end" width="15%">
                                <i class="fas fa-money-bill-wave"></i><br>
                                <small>Líquido</small>
                            </th>
                            <th width="18%">
                                <i class="fas fa-chart-pie"></i><br>
                                <small>% do Total</small>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($relatorio['por_restaurante'] as $index => $rest)
                            @php
                                $percentual = $relatorio['totais']['valor_total'] > 0 
                                    ? ($rest->total_valor / $relatorio['totais']['valor_total'] * 100) 
                                    : 0;
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    @if($index < 3)
                                        <span class="badge bg-warning text-dark fw-bold">{{ $index + 1 }}°</span>
                                    @else
                                        <span class="text-muted fw-bold">{{ $index + 1 }}°</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-primary">{{ $rest->tenant_code }}</strong>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary fs-6">{{ $rest->total_transacoes }}</span>
                                </td>
                                <td class="text-end">
                                    <strong>R$ {{ number_format($rest->total_valor, 2, ',', '.') }}</strong>
                                </td>
                                <td class="text-end text-info">
                                    <strong>R$ {{ number_format($rest->total_taxa, 2, ',', '.') }}</strong>
                                </td>
                                <td class="text-end text-warning">
                                    <strong>R$ {{ number_format($rest->total_entrega ?? 0, 2, ',', '.') }}</strong>
                                </td>
                                <td class="text-end text-success">
                                    <strong class="fs-6">R$ {{ number_format($rest->total_liquido, 2, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 25px;">
                                            <div class="progress-bar bg-primary" 
                                                 style="width: {{ $percentual }}%"
                                                 role="progressbar">
                                                <strong>{{ number_format($percentual, 1) }}%</strong>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light border-top border-2">
                        <tr class="fw-bold">
                            <td colspan="2" class="ps-4">
                                <i class="fas fa-calculator"></i> TOTAL GERAL
                            </td>
                            <td class="text-center">
                                <span class="badge bg-dark fs-6">{{ $relatorio['totais']['transacoes'] }}</span>
                            </td>
                            <td class="text-end text-dark">R$ {{ number_format($relatorio['totais']['valor_total'], 2, ',', '.') }}</td>
                            <td class="text-end text-info">R$ {{ number_format($relatorio['totais']['taxa_plataforma'], 2, ',', '.') }}</td>
                            <td class="text-end text-warning">R$ {{ number_format($relatorio['totais']['taxa_entrega'] ?? 0, 2, ',', '.') }}</td>
                            <td class="text-end text-success">R$ {{ number_format($relatorio['totais']['valor_restaurantes'], 2, ',', '.') }}</td>
                            <td>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" style="width: 100%">
                                        <strong>100%</strong>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma transação encontrada</h5>
                <p class="text-muted">Não há transações aprovadas no período selecionado.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Botões de Ação -->
    <div class="text-center mb-4">
        <button onclick="window.print()" class="btn btn-outline-primary btn-lg me-2">
            <i class="fas fa-print"></i> Imprimir Relatório
        </button>
        <a href="{{ route('admin.financeiro.pagamentos.lista') }}" class="btn btn-outline-secondary btn-lg">
            <i class="fas fa-list"></i> Ver Todas Transações
        </a>
    </div>
</div>

<style>
@media print {
    .btn, .card-header, .alert, nav, .sidebar { 
        display: none !important; 
    }
    .card { 
        border: 1px solid #ddd !important; 
        box-shadow: none !important; 
        page-break-inside: avoid;
    }
    body {
        font-size: 12px;
    }
}

.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}

.progress {
    background-color: #e9ecef;
}

.progress-bar {
    transition: width 0.6s ease;
}
</style>
@endsection
