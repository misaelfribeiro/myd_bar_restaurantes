@extends('layouts.app')
@section('content')
<div class="container-fluid">
 <div class="row mb-4">
 <div class="col-md-12">
 <div class="d-flex justify-content-between align-items-center">
 <h2><i class="fas fa-user"></i> Detalhes do Funcionário</h2>
 <div>
 <button onclick="window.print()" class="btn btn-primary no-print">
 <i class="fas fa-print"></i> Imprimir Ficha
 </button>
 <a href="{{ route('admin.funcionarios.edit', $funcionario->id) }}" class="btn btn-warning no-print">
 <i class="fas fa-edit"></i> Editar
 </a>
 <a href="{{ route('admin.funcionarios.index') }}" class="btn btn-secondary no-print">
 <i class="fas fa-arrow-left"></i> Voltar
 </a>
 </div>
 </div>
 </div>
 </div>
 <div class="row">
 <div class="col-md-8">
 <div class="card mb-4">
 <div class="card-header bg-primary text-white">
 <h5 class="mb-0"><i class="fas fa-user"></i> Informações Pessoais</h5>
 </div>
 <div class="card-body">
 <div class="row mb-3">
 <div class="col-md-8">
 <strong>Nome Completo:</strong>
 <p class="mb-0">{{ $funcionario->nome_completo }}</p>
 </div>
 <div class="col-md-4">
 <strong>Status:</strong>
 <p class="mb-0">
 @if($funcionario->ativo)
 <span class="badge bg-success">Ativo</span>
 @else
 <span class="badge bg-danger">Inativo</span>
 @endif
 </p>
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-4">
 <strong>CPF:</strong>
 <p class="mb-0">{{ $funcionario->cpf }}</p>
 </div>
 <div class="col-md-4">
 <strong>RG:</strong>
 <p class="mb-0">{{ $funcionario->rg ?? 'N/A' }}</p>
 </div>
 <div class="col-md-4">
 <strong>Data de Nascimento:</strong>
 <p class="mb-0">{{ $funcionario->data_nascimento ? $funcionario->data_nascimento->format('d/m/Y') : 'N/A' }}</p>
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-6">
 <strong>Email:</strong>
 <p class="mb-0">{{ $funcionario->email }}</p>
 </div>
 <div class="col-md-3">
 <strong>Telefone:</strong>
 <p class="mb-0">{{ $funcionario->telefone ?? 'N/A' }}</p>
 </div>
 <div class="col-md-3">
 <strong>Celular:</strong>
 <p class="mb-0">{{ $funcionario->celular ?? 'N/A' }}</p>
 </div>
 </div>
 <div class="row">
 <div class="col-md-12">
 <strong>Endereço:</strong>
 <p class="mb-0">
 {{ $funcionario->endereco ?? 'N/A' }}
 @if($funcionario->numero)
 , {{ $funcionario->numero }}
 @endif
 @if($funcionario->complemento)
 - {{ $funcionario->complemento }}
 @endif
 @if($funcionario->bairro)
 - {{ $funcionario->bairro }}
 @endif
 @if($funcionario->cidade || $funcionario->estado)
 - {{ $funcionario->cidade }}/{{ $funcionario->estado }}
 @endif
 @if($funcionario->cep)
 - CEP: {{ $funcionario->cep }}
 @endif
 </p>
 </div>
 </div>
 </div>
 </div>
 <div class="card mb-4">
 <div class="card-header bg-secondary text-white">
 <h5 class="mb-0"><i class="fas fa-id-card"></i> Documentos</h5>
 </div>
 <div class="card-body">
 <div class="row mb-3">
 <div class="col-md-4">
 <strong>PIS/PASEP:</strong>
 <p class="mb-0">{{ $funcionario->pis_pasep ?? 'N/A' }}</p>
 </div>
 <div class="col-md-4">
 <strong>Título de Eleitor:</strong>
 <p class="mb-0">{{ $funcionario->titulo_eleitor ?? 'N/A' }}</p>
 </div>
 <div class="col-md-4">
 <strong>Carteira de Trabalho:</strong>
 <p class="mb-0">{{ $funcionario->carteira_trabalho ?? 'N/A' }}</p>
 </div>
 </div>
 </div>
 </div>
 <div class="card mb-4">
 <div class="card-header bg-success text-white">
 <h5 class="mb-0"><i class="fas fa-university"></i> Dados Bancários</h5>
 </div>
 <div class="card-body">
 <div class="row mb-3">
 <div class="col-md-6">
 <strong>Banco:</strong>
 <p class="mb-0">{{ $funcionario->banco ?? 'N/A' }}</p>
 </div>
 <div class="col-md-6">
 <strong>Tipo de Conta:</strong>
 <p class="mb-0">
 @if($funcionario->tipo_conta == 'corrente')
 Corrente
 @elseif($funcionario->tipo_conta == 'poupanca')
 Poupança
 @else
 N/A
 @endif
 </p>
 </div>
 </div>
 <div class="row">
 <div class="col-md-6">
 <strong>Agência:</strong>
 <p class="mb-0">{{ $funcionario->agencia ?? 'N/A' }}</p>
 </div>
 <div class="col-md-6">
 <strong>Conta Bancária:</strong>
 <p class="mb-0">{{ $funcionario->conta_bancaria ?? 'N/A' }}</p>
 </div>
 </div>
 </div>
 </div>
 <div class="card mb-4">
 <div class="card-header bg-info text-white">
 <h5 class="mb-0"><i class="fas fa-briefcase"></i> Informações Profissionais</h5>
 </div>
 <div class="card-body">
 <div class="row mb-3">
 <div class="col-md-4">
 <strong>Cargo:</strong>
 <p class="mb-0">{{ $funcionario->cargo->nome ?? 'N/A' }}</p>
 </div>
 <div class="col-md-4">
 <strong>Tipo de Contrato:</strong>
 <p class="mb-0">
 @if($funcionario->tipo_contrato == 'clt')
 CLT
 @elseif($funcionario->tipo_contrato == 'pj')
 PJ
 @elseif($funcionario->tipo_contrato == 'estagio')
 Estágio
 @elseif($funcionario->tipo_contrato == 'temporario')
 Temporário
 @else
 N/A
 @endif
 </p>
 </div>
 <div class="col-md-4">
 <strong>Salário:</strong>
 <p class="mb-0">R$ {{ number_format($funcionario->salario ?? 0, 2, ',', '.') }}</p>
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-4">
 <strong>Data de Admissão:</strong>
 <p class="mb-0">{{ $funcionario->data_admissao ? $funcionario->data_admissao->format('d/m/Y') : 'N/A' }}</p>
 </div>
 <div class="col-md-4">
 <strong>Data de Demissão:</strong>
 <p class="mb-0">{{ $funcionario->data_demissao ? $funcionario->data_demissao->format('d/m/Y') : 'N/A' }}</p>
 </div>
 <div class="col-md-4">
 <strong>Tempo de Casa:</strong>
 <p class="mb-0">
 @if($funcionario->data_admissao)
 {{ $funcionario->data_admissao->diffForHumans() }}
 @else
 N/A
 @endif
 </p>
 </div>
 </div>
 <div class="row">
 <div class="col-md-6">
 <strong>Tipo de Comissão:</strong>
 <p class="mb-0">{{ $funcionario->tipo_comissao ? ucfirst($funcionario->tipo_comissao) : 'Nenhuma' }}</p>
 </div>
 <div class="col-md-6">
 <strong>Percentual de Comissão:</strong>
 <p class="mb-0">{{ $funcionario->percentual_comissao ? $funcionario->percentual_comissao . '%' : 'N/A' }}</p>
 </div>
 </div>
 </div>
 </div>
 @if($funcionario->observacoes)
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-sticky-note"></i> Observações</h5>
 </div>
 <div class="card-body">
 <p class="mb-0">{{ $funcionario->observacoes }}</p>
 </div>
 </div>
 @endif
 </div>
 <div class="col-md-4">
 <div class="card mb-4">
 <div class="card-header bg-success text-white">
 <h5 class="mb-0"><i class="fas fa-chart-line"></i> Resumo Financeiro</h5>
 </div>
 <div class="card-body">
 <div class="mb-3">
 <strong>Comissões (Total):</strong>
 <h4 class="text-success">R$ {{ number_format($funcionario->comissoes->sum('valor_comissao'), 2, ',', '.') }}</h4>
 </div>
 <div class="mb-3">
 <strong>Bônus (Total):</strong>
 <h4 class="text-info">R$ {{ number_format($funcionario->bonus->sum('valor'), 2, ',', '.') }}</h4>
 </div>
 <hr>
 <div>
 <strong>Total de Comissões:</strong>
 <p class="mb-0">{{ $funcionario->comissoes->count() }} registros</p>
 </div>
 <div>
 <strong>Total de Bônus:</strong>
 <p class="mb-0">{{ $funcionario->bonus->count() }} registros</p>
 </div>
 </div>
 </div>
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-dollar-sign"></i> Últimas Comissões</h5>
 </div>
 <div class="card-body">
 @forelse($funcionario->comissoes->take(5) as $comissao)
 <div class="mb-2 pb-2 border-bottom">
 <div class="d-flex justify-content-between">
 <small>{{ $comissao->tipo }}</small>
 <strong class="text-success">R$ {{ number_format($comissao->valor_comissao, 2, ',', '.') }}</strong>
 </div>
 <small class="text-muted">{{ $comissao->data_referencia ? $comissao->data_referencia->format('d/m/Y') : '-' }}</small>
 </div>
 @empty
 <p class="text-muted mb-0">Nenhuma comissão registrada</p>
 @endforelse
 </div>
 </div>
 <div class="card">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-gift"></i> Últimos Bônus</h5>
 </div>
 <div class="card-body">
 @forelse($funcionario->bonus->take(5) as $bonus)
 <div class="mb-2 pb-2 border-bottom">
 <div class="d-flex justify-content-between">
 <small>{{ $bonus->titulo }}</small>
 <strong class="text-info">R$ {{ number_format($bonus->valor, 2, ',', '.') }}</strong>
 </div>
 <small class="text-muted">{{ $bonus->data_referencia ? $bonus->data_referencia->format('d/m/Y') : '-' }}</small>
 </div>
 @empty
 <p class="text-muted mb-0">Nenhum bônus registrado</p>
 @endforelse
 </div>
 </div>
 </div>
 </div>
</div>
<style>
@media print {
 .no-print,
 .sidebar,
 nav,
 .navbar,
 footer {
 display: none !important;
 }
 body {
 background: white !important;
 color: black !important;
 font-size: 11pt;
 }
 .container-fluid {
 width: 100% !important;
 max-width: 100% !important;
 padding: 0 !important;
 margin: 0 !important;
 }
 .card {
 border: 1px solid #000 !important;
 box-shadow: none !important;
 page-break-inside: avoid;
 margin-bottom: 15px !important;
 }
 .card-header {
 background-color: #f0f0f0 !important;
 color: #000 !important;
 border-bottom: 2px solid #000 !important;
 padding: 10px !important;
 -webkit-print-color-adjust: exact;
 print-color-adjust: exact;
 }
 .card-body {
 padding: 10px !important;
 }
 .col-md-8,
 .col-md-4 {
 width: 100% !important;
 max-width: 100% !important;
 }
 h2 {
 font-size: 18pt !important;
 margin-bottom: 20px !important;
 text-align: center;
 }
 .badge {
 border: 1px solid #000 !important;
 padding: 3px 8px !important;
 -webkit-print-color-adjust: exact;
 print-color-adjust: exact;
 }
 .row {
 page-break-inside: avoid;
 }
 strong {
 font-weight: bold !important;
 }
 p {
 margin-bottom: 5px !important;
 }
 .mb-4 {
 margin-bottom: 10px !important;
 }
 .mb-3 {
 margin-bottom: 8px !important;
 }
 @page {
 margin: 1cm;
 }
 body::before {
 content: "FICHA DE FUNCIONÁRIO";
 display: block;
 text-align: center;
 font-size: 16pt;
 font-weight: bold;
 margin-bottom: 10px;
 padding-bottom: 10px;
 border-bottom: 2px solid #000;
 }
}
</style>
@endsection