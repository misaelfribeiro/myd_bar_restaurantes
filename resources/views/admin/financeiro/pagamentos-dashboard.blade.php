@extends('layouts.app')

@section('title', 'Dashboard Financeiro')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line"></i> Dashboard Financeiro
        </h1>
        <div>
            <button class="btn btn-info" onclick="window.location.reload()">
                <i class="fas fa-sync-alt"></i> Atualizar
            </button>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row">
        <!-- Total Transações -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Transações (Mês)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_transacoes'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aprovadas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Valor Aprovado
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ {{ number_format($stats['valor_aprovado'] ?? 0, 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Taxa Plataforma -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Taxa Gestora
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ {{ number_format($stats['taxa_plataforma'] ?? 0, 2, ',', '.') }}
                            </div>
                            <div class="text-xs text-muted mt-1">
                                + R$ {{ number_format($stats['taxa_entrega'] ?? 0, 2, ',', '.') }} entrega
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Líquido Restaurantes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Líquido Restaurantes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ {{ number_format($stats['liquido_restaurantes'] ?? 0, 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top 10 Restaurantes -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy"></i> Top 10 Restaurantes por Faturamento
                    </h6>
                    <span class="badge badge-info">Mês Atual</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Restaurante</th>
                                    <th class="text-right">Transações</th>
                                    <th class="text-right">Faturamento</th>
                                    <th class="text-right">Taxa</th>
                                    <th class="text-right">Entrega</th>
                                    <th class="text-right">Líquido</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($top_restaurantes ?? [] as $index => $rest)
                                <tr>
                                    <td>{{ $index + 1 }}°</td>
                                    <td>
                                        <strong>{{ $rest->tenant_code }}</strong>
                                        @if($rest->empresa)
                                        <br><small class="text-muted">{{ $rest->empresa->nome }}</small>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ $rest->total_transacoes }}</td>
                                    <td class="text-right">R$ {{ number_format($rest->total_faturamento, 2, ',', '.') }}</td>
                                    <td class="text-right text-info">R$ {{ number_format($rest->total_taxa, 2, ',', '.') }}</td>
                                    <td class="text-right text-warning">R$ {{ number_format($rest->total_entrega ?? 0, 2, ',', '.') }}</td>
                                    <td class="text-right text-success">
                                        <strong>R$ {{ number_format($rest->total_liquido, 2, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Nenhuma transação no mês atual
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métodos de Pagamento -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-credit-card"></i> Métodos de Pagamento
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($metodos_pagamento ?? [] as $metodo)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-capitalize">
                                @if($metodo->payment_method == 'pix')
                                <i class="fas fa-qrcode text-primary"></i> PIX
                                @elseif($metodo->payment_method == 'credit_card')
                                <i class="fas fa-credit-card text-success"></i> Cartão Crédito
                                @elseif($metodo->payment_method == 'debit_card')
                                <i class="fas fa-credit-card text-info"></i> Cartão Débito
                                @else
                                {{ $metodo->payment_method }}
                                @endif
                            </span>
                            <strong>{{ $metodo->total }}</strong>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ ($metodo->total / ($stats['total_transacoes'] ?: 1)) * 100 }}%">
                                {{ number_format(($metodo->total / ($stats['total_transacoes'] ?: 1)) * 100, 1) }}%
                            </div>
                        </div>
                        <small class="text-muted">
                            R$ {{ number_format($metodo->total_valor, 2, ',', '.') }}
                        </small>
                    </div>
                    @empty
                    <p class="text-center text-muted py-3">Nenhuma transação registrada</p>
                    @endforelse
                </div>
            </div>

            <!-- Pendentes -->
            <div class="card shadow mb-4 border-left-warning">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                        <h5 class="font-weight-bold">{{ $stats['transacoes_pendentes'] ?? 0 }}</h5>
                        <p class="text-muted mb-0">Pagamentos Pendentes</p>
                        <a href="{{ route('admin.financeiro.pagamentos.lista') }}?status=pending" class="btn btn-sm btn-warning mt-2">
                            Ver Pendentes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Links Rápidos -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-link"></i> Acesso Rápido
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.financeiro.pagamentos.lista') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-list"></i> Todas Transações
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.financeiro.pagamentos.lista') }}?status=approved" class="btn btn-outline-success btn-block">
                                <i class="fas fa-check"></i> Aprovados
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.financeiro.pagamentos.lista') }}?status=refunded" class="btn btn-outline-danger btn-block">
                                <i class="fas fa-undo"></i> Estornos
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.financeiro.pagamentos.relatorios') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-file-alt"></i> Relatórios
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
</style>
@endsection
