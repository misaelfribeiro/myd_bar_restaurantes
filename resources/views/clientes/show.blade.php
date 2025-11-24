@extends('layouts.app')
@section('content')
<div class="container">
 <div class="row">
 <div class="col-md-12">
 <div class="card">
 <div class="card-header d-flex justify-content-between align-items-center">
 <h5 class="mb-0"><i class="fas fa-user"></i> {{ $cliente->nome }}</h5>
 <div>
 <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-warning">
 <i class="fas fa-edit"></i> Editar
 </a>
 <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
 <i class="fas fa-arrow-left"></i> Voltar
 </a>
 </div>
 </div>
 <div class="card-body">
 <div class="row">
 <!-- Informações Pessoais -->
 <div class="col-md-6">
 <div class="card">
 <div class="card-header">
 <h6 class="mb-0"><i class="fas fa-user"></i> Dados Pessoais</h6>
 </div>
 <div class="card-body">
 <table class="table table-borderless">
 <tr>
 <td><strong>Nome:</strong></td>
 <td>{{ $cliente->nome }}</td>
 </tr>
 <tr>
 <td><strong>Telefone:</strong></td>
 <td>{{ $cliente->telefone_formatado }}</td>
 </tr>
 <tr>
 <td><strong>Email:</strong></td>
 <td>{{ $cliente->email ?: '-' }}</td>
 </tr>
 <tr>
 <td><strong>Status:</strong></td>
 <td>
 @if($cliente->ativo)
 <span class="badge badge-success">Ativo</span>
 @else
 <span class="badge badge-secondary">Inativo</span>
 @endif
 </td>
 </tr>
 <tr>
 <td><strong>Cadastrado em:</strong></td>
 <td>{{ $cliente->created_at->format('d/m/Y H:i') }}</td>
 </tr>
 </table>
 </div>
 </div>
 </div>
 <!-- Informações Adicionais -->
 <div class="col-md-6">
 <div class="card">
 <div class="card-header">
 <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informações Adicionais</h6>
 </div>
 <div class="card-body">
 <table class="table table-borderless">
 <tr>
 <td><strong>CPF:</strong></td>
 <td>{{ $cliente->cpf ? $cliente->cpf_formatado : '-' }}</td>
 </tr>
 <tr>
 <td><strong>Data de Nascimento:</strong></td>
 <td>{{ $cliente->data_nascimento ? $cliente->data_nascimento->format('d/m/Y') : '-' }}</td>
 </tr>
 @if($cliente->data_nascimento)
 <tr>
 <td><strong>Idade:</strong></td>
 <td>{{ $cliente->idade }} anos</td>
 </tr>
 @endif
 </table>
 @if($cliente->observacoes)
 <div class="mt-3">
 <strong>Observações:</strong>
 <p class="mb-0">{{ $cliente->observacoes }}</p>
 </div>
 @endif
 </div>
 </div>
 </div>
 </div>
 <div class="row mt-4">
 <!-- Endereço -->
 <div class="col-md-12">
 <div class="card">
 <div class="card-header">
 <h6 class="mb-0"><i class="fas fa-map-marker-alt"></i> Endereço para Entrega</h6>
 </div>
 <div class="card-body">
 @if($cliente->endereco_rua)
 <table class="table table-borderless">
 <tr>
 <td><strong>Endereço:</strong></td>
 <td>{{ $cliente->endereco_completo }}</td>
 </tr>
 <tr>
 <td><strong>Rua:</strong></td>
 <td>{{ $cliente->endereco_rua }}</td>
 </tr>
 <tr>
 <td><strong>Número:</strong></td>
 <td>{{ $cliente->endereco_numero ?: '-' }}</td>
 </tr>
 @if($cliente->endereco_complemento)
 <tr>
 <td><strong>Complemento:</strong></td>
 <td>{{ $cliente->endereco_complemento }}</td>
 </tr>
 @endif
 <tr>
 <td><strong>Bairro:</strong></td>
 <td>{{ $cliente->endereco_bairro ?: '-' }}</td>
 </tr>
 <tr>
 <td><strong>Cidade:</strong></td>
 <td>{{ $cliente->endereco_cidade ?: '-' }}</td>
 </tr>
 <tr>
 <td><strong>CEP:</strong></td>
 <td>{{ $cliente->endereco_cep ?: '-' }}</td>
 </tr>
 </table>
 @else
 <p class="text-muted mb-0">
 <i class="fas fa-info-circle"></i> 
 Nenhum endereço cadastrado para entrega.
 </p>
 @endif
 </div>
 </div>
 </div>
 </div>
 <div class="row mt-4">
 <!-- Estatísticas -->
 <div class="col-md-12">
 <div class="card">
 <div class="card-header">
 <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Estatísticas</h6>
 </div>
 <div class="card-body">
 <div class="row">
 <div class="col-md-3">
 <div class="text-center">
 <h4 class="text-primary">{{ $estatisticas['total_pedidos'] }}</h4>
 <p class="text-muted">Total de Pedidos</p>
 </div>
 </div>
 <div class="col-md-3">
 <div class="text-center">
 <h4 class="text-info">{{ $estatisticas['total_deliveries'] }}</h4>
 <p class="text-muted">Total de Deliveries</p>
 </div>
 </div>
 <div class="col-md-3">
 <div class="text-center">
 <h4 class="text-success">R$ {{ number_format($estatisticas['valor_total_pedidos'], 2, ',', '.') }}</h4>
 <p class="text-muted">Valor Total</p>
 </div>
 </div>
 <div class="col-md-3">
 <div class="text-center">
 @if($estatisticas['ultimo_pedido'])
 <h6 class="text-warning">{{ $estatisticas['ultimo_pedido']->created_at->format('d/m/Y') }}</h6>
 <p class="text-muted">Último Pedido</p>
 @else
 <h6 class="text-muted">-</h6>
 <p class="text-muted">Último Pedido</p>
 @endif
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Últimos Pedidos -->
 @if($ultimosPedidos->count() > 0)
 <div class="row mt-4">
 <div class="col-md-12">
 <div class="card">
 <div class="card-header">
 <h6 class="mb-0"><i class="fas fa-shopping-cart"></i> Últimos Pedidos</h6>
 </div>
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-sm">
 <thead>
 <tr>
 <th>#</th>
 <th>Data</th>
 <th>Tipo</th>
 <th>Status</th>
 <th>Valor</th>
 <th>Ações</th>
 </tr>
 </thead>
 <tbody>
 @foreach($ultimosPedidos as $pedido)
 <tr>
 <td>{{ $pedido->id }}</td>
 <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
 <td>
 @if($pedido->mesa_id)
 <span class="badge badge-primary">Mesa {{ $pedido->mesa->numero ?? $pedido->mesa_id }}</span>
 @else
 <span class="badge badge-info">Delivery</span>
 @endif
 </td>
 <td>
 <span class="badge badge-{{ $pedido->status === 'finalizado' ? 'success' : ($pedido->status === 'cancelado' ? 'danger' : 'warning') }}">
 {{ ucfirst($pedido->status) }}
 </span>
 </td>
 <td>R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
 <td>
 <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-sm btn-info">
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
 </div>
 </div>
 @endif
 <!-- Últimas Deliveries -->
 @if($ultimasDeliveries->count() > 0)
 <div class="row mt-4">
 <div class="col-md-12">
 <div class="card">
 <div class="card-header">
 <h6 class="mb-0"><i class="fas fa-truck"></i> Últimas Deliveries</h6>
 </div>
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-sm">
 <thead>
 <tr>
 <th>#</th>
 <th>Data</th>
 <th>Status</th>
 <th>Valor Entrega</th>
 </tr>
 </thead>
 <tbody>
 @foreach($ultimasDeliveries as $delivery)
 <tr>
 <td>{{ $delivery->id }}</td>
 <td>{{ $delivery->created_at->format('d/m/Y H:i') }}</td>
 <td>
 <span class="badge badge-{{ $delivery->status === 'entregue' ? 'success' : ($delivery->status === 'cancelado' ? 'danger' : 'warning') }}">
 {{ ucfirst($delivery->status) }}
 </span>
 </td>
 <td>R$ {{ number_format($delivery->taxa_entrega ?? 0, 2, ',', '.') }}</td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 </div>
 </div>
 </div>
 @endif
 </div>
 </div>
 </div>
 </div>
</div>
@endsection