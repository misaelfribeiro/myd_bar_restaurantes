@extends('layouts.app')
@section('title', 'Empresa - ' . $empresa->nome_fantasia)
@section('content')
<div class="container-fluid">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <h1><i class="fas fa-building me-2"></i>{{ $empresa->nome_fantasia }}</h1>
 <div>
 <a href="{{ route('empresas.index') }}" class="btn btn-secondary me-2">
 <i class="fas fa-arrow-left me-2"></i>Voltar
 </a>
 <a href="{{ route('empresas.edit', $empresa->id) }}" class="btn btn-primary">
 <i class="fas fa-edit me-2"></i>Editar
 </a>
 </div>
 </div>
 @if(session('success'))
 <div class="alert alert-success alert-dismissible fade show" role="alert">
 <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
 @endif
 <div class="row">
 <!-- Informações Principais -->
 <div class="col-lg-8">
 <div class="card mb-4">
 <div class="card-header d-flex justify-content-between align-items-center">
 <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações da Empresa</h5>
 {!! $empresa->status_badge !!}
 </div>
 <div class="card-body">
 <div class="row mb-3">
 <div class="col-md-6">
 <h6 class="text-primary border-bottom pb-2 mb-3">Dados Cadastrais</h6>
 <table class="table table-borderless">
 <tr>
 <th width="150">Nome Fantasia:</th>
 <td>{{ $empresa->nome_fantasia }}</td>
 </tr>
 <tr>
 <th>Razão Social:</th>
 <td>{{ $empresa->razao_social }}</td>
 </tr>
 <tr>
 <th>CNPJ:</th>
 <td>{{ preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $empresa->cnpj) }}</td>
 </tr>
 @if($empresa->inscricao_estadual)
 <tr>
 <th>Insc. Estadual:</th>
 <td>{{ $empresa->inscricao_estadual }}</td>
 </tr>
 @endif
 @if($empresa->inscricao_municipal)
 <tr>
 <th>Insc. Municipal:</th>
 <td>{{ $empresa->inscricao_municipal }}</td>
 </tr>
 @endif
 <tr>
 <th>Tipo:</th>
 <td>
 @if($empresa->tipo === 'matriz')
 <span class="badge bg-primary">
 <i class="fas fa-building me-1"></i>Matriz
 </span>
 @else
 <span class="badge bg-info">
 <i class="fas fa-store me-1"></i>Filial
 </span>
 @endif
 </td>
 </tr>
 @if($empresa->tipo === 'filial' && $empresa->matriz)
 <tr>
 <th>Matriz:</th>
 <td>
 <a href="{{ route('empresas.show', $empresa->matriz->id) }}">
 {{ $empresa->matriz->nome_fantasia }}
 </a>
 </td>
 </tr>
 @endif
 </table>
 </div>
 <div class="col-md-6">
 <h6 class="text-primary border-bottom pb-2 mb-3">Contato</h6>
 <table class="table table-borderless">
 <tr>
 <th width="100">E-mail:</th>
 <td>
 <a href="mailto:{{ $empresa->email }}">{{ $empresa->email }}</a>
 </td>
 </tr>
 <tr>
 <th>Telefone:</th>
 <td>
 <a href="tel:{{ $empresa->telefone }}">
 {{ preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '($1) $2-$3', $empresa->telefone) }}
 </a>
 </td>
 </tr>
 @if($empresa->celular)
 <tr>
 <th>Celular:</th>
 <td>
 <a href="tel:{{ $empresa->celular }}">
 {{ preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $empresa->celular) }}
 </a>
 </td>
 </tr>
 @endif
 @if($empresa->site)
 <tr>
 <th>Website:</th>
 <td>
 <a href="{{ $empresa->site }}" target="_blank">
 {{ $empresa->site }} <i class="fas fa-external-link-alt ms-1"></i>
 </a>
 </td>
 </tr>
 @endif
 </table>
 </div>
 </div>
 @if($empresa->descricao)
 <div class="row mb-3">
 <div class="col-12">
 <h6 class="text-primary border-bottom pb-2 mb-3">Descrição</h6>
 <p>{{ $empresa->descricao }}</p>
 </div>
 </div>
 @endif
 <div class="row mb-3">
 <div class="col-12">
 <h6 class="text-primary border-bottom pb-2 mb-3">
 <i class="fas fa-map-marker-alt me-2"></i>Endereço
 </h6>
 <p class="mb-2">{{ $empresa->endereco_completo }}</p>
 @if($empresa->latitude && $empresa->longitude)
 <div id="map" style="height: 300px; border-radius: 8px;" class="mt-3"></div>
 @endif
 </div>
 </div>
 @if($empresa->horario_abertura && $empresa->horario_fechamento)
 <div class="row">
 <div class="col-12">
 <h6 class="text-primary border-bottom pb-2 mb-3">
 <i class="fas fa-clock me-2"></i>Horário de Funcionamento
 </h6>
 <p class="mb-2">
 <strong>Horário:</strong> 
 {{ date('H:i', strtotime($empresa->horario_abertura)) }} às 
 {{ date('H:i', strtotime($empresa->horario_fechamento)) }}
 </p>
 @if($empresa->dias_funcionamento)
 <p class="mb-0">
 <strong>Dias:</strong>
 @php
 $dias_map = [
 'seg' => 'Segunda',
 'ter' => 'Terça',
 'qua' => 'Quarta',
 'qui' => 'Quinta',
 'sex' => 'Sexta',
 'sab' => 'Sábado',
 'dom' => 'Domingo'
 ];
 $dias = array_map(function($dia) use ($dias_map) {
 return $dias_map[$dia] ?? $dia;
 }, $empresa->dias_funcionamento);
 @endphp
 {{ implode(', ', $dias) }}
 </p>
 @endif
 </div>
 </div>
 @endif
 </div>
 </div>
 <!-- Filiais (se for matriz) -->
 @if($empresa->tipo === 'matriz' && $empresa->filiais->count() > 0)
 <div class="card">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-store me-2"></i>Filiais ({{ $empresa->filiais->count() }})</h5>
 </div>
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-hover">
 <thead class="table-light">
 <tr>
 <th>Nome</th>
 <th>Cidade/UF</th>
 <th>Telefone</th>
 <th>Status</th>
 <th width="100" class="text-center">Ações</th>
 </tr>
 </thead>
 <tbody>
 @foreach($empresa->filiais as $filial)
 <tr>
 <td>{{ $filial->nome_fantasia }}</td>
 <td>{{ $filial->endereco_cidade }}/{{ $filial->endereco_estado }}</td>
 <td>{{ preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '($1) $2-$3', $filial->telefone) }}</td>
 <td>{!! $filial->status_badge !!}</td>
 <td class="text-center">
 <a href="{{ route('empresas.show', $filial->id) }}" 
 class="btn btn-sm btn-info" 
 title="Visualizar">
 <i class="fas fa-eye"></i>
 </a>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 </div>
 @endif
 </div>
 <!-- Sidebar -->
 <div class="col-lg-4">
 <!-- Logo -->
 @if($empresa->logo)
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-image me-2"></i>Logo</h5>
 </div>
 <div class="card-body text-center">
 <img src="{{ asset('storage/' . $empresa->logo) }}" 
 alt="{{ $empresa->nome_fantasia }}" 
 class="img-fluid rounded">
 </div>
 </div>
 @endif
 <!-- Configurações de Delivery -->
 @if($empresa->aceita_delivery)
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-motorcycle me-2"></i>Delivery</h5>
 </div>
 <div class="card-body">
 <table class="table table-borderless">
 <tr>
 <th width="150">Status:</th>
 <td>
 <span class="badge bg-success">
 <i class="fas fa-check me-1"></i>Ativo
 </span>
 </td>
 </tr>
 <tr>
 <th>Taxa Padrão:</th>
 <td>R$ {{ number_format($empresa->taxa_entrega_padrao, 2, ',', '.') }}</td>
 </tr>
 @if($empresa->raio_entrega_km)
 <tr>
 <th>Raio de Entrega:</th>
 <td>{{ number_format($empresa->raio_entrega_km, 1, ',', '.') }} km</td>
 </tr>
 @endif
 @if($empresa->pedido_minimo > 0)
 <tr>
 <th>Pedido Mínimo:</th>
 <td>R$ {{ number_format($empresa->pedido_minimo, 2, ',', '.') }}</td>
 </tr>
 @endif
 </table>
 </div>
 </div>
 @endif
 <!-- Informações do Sistema -->
 <div class="card">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações do Sistema</h5>
 </div>
 <div class="card-body">
 <table class="table table-borderless table-sm">
 <tr>
 <th width="120">Cadastrado em:</th>
 <td>{{ $empresa->created_at->format('d/m/Y H:i') }}</td>
 </tr>
 <tr>
 <th>Atualizado em:</th>
 <td>{{ $empresa->updated_at->format('d/m/Y H:i') }}</td>
 </tr>
 </table>
 </div>
 </div>
 </div>
 </div>
</div>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@if($empresa->latitude && $empresa->longitude)
<script>
document.addEventListener('DOMContentLoaded', function() {
 const lat = {{ $empresa->latitude }};
 const lng = {{ $empresa->longitude }};
 const map = L.map('map').setView([lat, lng], 16);
 L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
 attribution: ' OpenStreetMap contributors'
 }).addTo(map);
 L.marker([lat, lng]).addTo(map)
 .bindPopup('<strong>{{ $empresa->nome_fantasia }}</strong><br>{{ $empresa->endereco_completo }}')
 .openPopup();
});
</script>
@endif
@endsection