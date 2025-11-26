@extends('layouts.app')

@section('title', 'Relatórios Financeiros')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">📊 Relatórios Financeiros</h1>
            <p class="text-muted mb-0">Análise de receitas e inadimplência</p>
        </div>
        <div>
            <a href="{{ route('admin.financeiro.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.financeiro.relatorios') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Ano</label>
                    <select name="ano" class="form-select">
                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}" {{ $ano == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mês (Opcional)</label>
                    <select name="mes" class="form-select">
                        <option value="">Todos os meses</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Receita Mensal -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Receita Mensal - {{ $ano }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mês</th>
                                    <th class="text-end">Total Faturado</th>
                                    <th class="text-end">Pago</th>
                                    <th class="text-end">Pendente</th>
                                    <th class="text-end">% Recebido</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalGeral = 0; $totalPago = 0; $totalPendente = 0; @endphp
                                @forelse($receitaMensal as $receita)
                                    @php
                                        $totalGeral += $receita->total;
                                        $totalPago += $receita->pago;
                                        $totalPendente += $receita->pendente;
                                        $percentual = $receita->total > 0 ? ($receita->pago / $receita->total * 100) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ \Carbon\Carbon::create()->month($receita->mes)->translatedFormat('F') }}</strong>
                                        </td>
                                        <td class="text-end">R$ {{ number_format($receita->total, 2, ',', '.') }}</td>
                                        <td class="text-end text-success">R$ {{ number_format($receita->pago, 2, ',', '.') }}</td>
                                        <td class="text-end text-warning">R$ {{ number_format($receita->pendente, 2, ',', '.') }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-{{ $percentual >= 80 ? 'success' : ($percentual >= 50 ? 'warning' : 'danger') }}">
                                                {{ number_format($percentual, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Nenhuma receita no período</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($receitaMensal->count() > 0)
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>TOTAL</td>
                                    <td class="text-end">R$ {{ number_format($totalGeral, 2, ',', '.') }}</td>
                                    <td class="text-end text-success">R$ {{ number_format($totalPago, 2, ',', '.') }}</td>
                                    <td class="text-end text-warning">R$ {{ number_format($totalPendente, 2, ',', '.') }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">
                                            {{ $totalGeral > 0 ? number_format($totalPago / $totalGeral * 100, 1) : 0 }}%
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receita por Plano -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-tag"></i> Receita por Plano</h5>
                </div>
                <div class="card-body">
                    @forelse($receitaPorPlano as $plano)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>{{ $plano->nome }}</strong>
                                    <br><small class="text-muted">{{ $plano->total_faturas }} faturas</small>
                                </div>
                                <div class="text-end">
                                    <div class="text-success fw-bold">R$ {{ number_format($plano->total_pago, 2, ',', '.') }}</div>
                                    <small class="text-muted">de R$ {{ number_format($plano->total_faturado, 2, ',', '.') }}</small>
                                </div>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" 
                                     style="width: {{ $plano->total_faturado > 0 ? ($plano->total_pago / $plano->total_faturado * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Nenhum dado disponível</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top 10 Empresas -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-trophy"></i> Top 10 Empresas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Empresa</th>
                                    <th class="text-end">Faturas</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topEmpresas as $index => $empresa)
                                    <tr>
                                        <td>
                                            @if($index < 3)
                                                <span class="badge bg-warning">{{ $index + 1 }}°</span>
                                            @else
                                                {{ $index + 1 }}°
                                            @endif
                                        </td>
                                        <td>{{ $empresa->nome_fantasia }}</td>
                                        <td class="text-end">{{ $empresa->qtd_faturas }}</td>
                                        <td class="text-end fw-bold">R$ {{ number_format($empresa->total, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Nenhuma empresa encontrada</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inadimplência -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Inadimplência</h5>
                </div>
                <div class="card-body">
                    @if($inadimplencia->count() > 0)
                        <div class="alert alert-danger mb-3">
                            <strong>{{ $inadimplencia->count() }}</strong> faturas vencidas em {{ $ano }}
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm">
                                <thead class="sticky-top bg-white">
                                    <tr>
                                        <th>Empresa</th>
                                        <th>Vencimento</th>
                                        <th class="text-end">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inadimplencia as $fatura)
                                        <tr>
                                            <td>
                                                <small>{{ $fatura->contrato->empresa->nome_fantasia ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <small>{{ \Carbon\Carbon::parse($fatura->data_vencimento)->format('d/m/Y') }}</small>
                                            </td>
                                            <td class="text-end">
                                                <small class="text-danger fw-bold">R$ {{ number_format($fatura->valor_total, 2, ',', '.') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td colspan="2">TOTAL INADIMPLENTE</td>
                                        <td class="text-end text-danger">
                                            R$ {{ number_format($inadimplencia->sum('valor_total'), 2, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <p class="mb-0">Nenhuma inadimplência registrada!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
