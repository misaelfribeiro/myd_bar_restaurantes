@extends('layouts.app')

@section('title', 'Detalhes do Entregador')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">
            <i class="fas fa-user-circle mr-2"></i>{{ $entregador->nome }}
        </h1>
        <div>
            <a href="{{ route('entregadores.edit', $entregador) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
            <a href="{{ route('entregadores.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Dados Pessoais -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user mr-2"></i>Dados Pessoais
                    </h6>
                </div>
                <div class="card-body text-center">
                    @if($entregador->foto_entregador)
                        <img src="{{ asset('storage/' . $entregador->foto_entregador) }}" 
                             class="rounded-circle mb-3" width="120" height="120" alt="Foto">
                    @else
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                             style="width: 120px; height: 120px;">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                    @endif

                    <h5>{{ $entregador->nome }}</h5>
                    <p class="text-muted mb-3">{{ $entregador->email }}</p>

                    <div class="row text-left">
                        <div class="col-12">
                            <strong>Status:</strong>
                            <span class="badge badge-{{ $entregador->status == 'ativo' ? 'success' : ($entregador->status == 'aprovado' ? 'primary' : ($entregador->status == 'pendente' ? 'warning' : 'danger')) }} ml-2">
                                {{ ucfirst($entregador->status) }}
                            </span>
                        </div>
                        <div class="col-12 mt-2">
                            <strong>Tipo:</strong>
                            <span class="badge {{ $entregador->tipo == 'interno' ? 'badge-success' : 'badge-info' }} ml-2">
                                {{ $entregador->tipo == 'interno' ? 'Interno' : 'App Externo' }}
                            </span>
                        </div>
                        <div class="col-12 mt-2">
                            <strong>Disponível:</strong>
                            @if($entregador->disponivel)
                                <span class="badge badge-success ml-2">Sim</span>
                            @else
                                <span class="badge badge-danger ml-2">Não</span>
                            @endif
                        </div>
                        @if($entregador->data_nascimento)
                            <div class="col-12 mt-2">
                                <strong>Idade:</strong> {{ $entregador->idade ?? 'N/A' }} anos
                            </div>
                        @endif
                        @if($entregador->telefone)
                            <div class="col-12 mt-2">
                                <strong>Telefone:</strong> {{ $entregador->telefone }}
                            </div>
                        @endif
                        @if($entregador->whatsapp)
                            <div class="col-12 mt-2">
                                <strong>WhatsApp:</strong> 
                                <a href="https://wa.me/55{{ $entregador->whatsapp }}" target="_blank" class="text-success">
                                    {{ $entregador->whatsapp }}
                                </a>
                            </div>
                        @endif
                        @if($entregador->cpf)
                            <div class="col-12 mt-2">
                                <strong>CPF:</strong> {{ $entregador->cpf }}
                            </div>
                        @endif
                    </div>

                    @if(auth()->check())
                        <div class="mt-4">
                            @if($entregador->status == 'pendente')
                                <button type="button" class="btn btn-success btn-sm mr-2" 
                                        data-bs-toggle="modal" data-bs-target="#aprovarModal">
                                    <i class="fas fa-check mr-1"></i>Aprovar
                                </button>
                                <button type="button" class="btn btn-danger btn-sm mr-2" 
                                        data-bs-toggle="modal" data-bs-target="#reprovarModal">
                                    <i class="fas fa-times mr-1"></i>Reprovar
                                </button>
                            @elseif($entregador->status == 'aprovado')
                                <form method="POST" action="{{ route('entregadores.ativar', $entregador) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm mr-2">
                                        <i class="fas fa-play mr-1"></i>Ativar
                                    </button>
                                </form>
                            @elseif($entregador->status == 'ativo')
                                <form method="POST" action="{{ route('entregadores.desativar', $entregador) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm mr-2">
                                        <i class="fas fa-pause mr-1"></i>Desativar
                                    </button>
                                </form>
                                <button type="button" class="btn btn-danger btn-sm mr-2" 
                                        data-bs-toggle="modal" data-bs-target="#suspenderModal">
                                    <i class="fas fa-ban mr-1"></i>Suspender
                                </button>
                            @elseif($entregador->status == 'suspenso')
                                <form method="POST" action="{{ route('entregadores.ativar', $entregador) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm mr-2">
                                        <i class="fas fa-play mr-1"></i>Reativar
                                    </button>
                                </form>
                            @endif
                            
                            <form method="POST" action="{{ route('entregadores.destroy', $entregador) }}" 
                                  class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este entregador?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash mr-1"></i>Excluir
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar mr-2"></i>Estatísticas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">{{ $estatisticas['total_entregas'] ?? 0 }}</h4>
                            <small class="text-muted">Total de Entregas</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info">{{ $estatisticas['entregas_mes'] ?? 0 }}</h4>
                            <small class="text-muted">Este Mês</small>
                        </div>
                        <div class="col-6 mt-3">
                            <h4 class="text-warning">{{ number_format($estatisticas['media_avaliacoes'] ?? 0, 1) }}</h4>
                            <small class="text-muted">Média de Avaliações</small>
                        </div>
                        <div class="col-6 mt-3">
                            <h4 class="text-success">{{ number_format($estatisticas['taxa_sucesso'] ?? 0, 1) }}%</h4>
                            <small class="text-muted">Taxa de Sucesso</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações Detalhadas -->
        <div class="col-lg-8">
            <!-- Endereço -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-map-marker-alt mr-2"></i>Endereço
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>CEP:</strong> {{ $entregador->cep }}<br>
                            <strong>Endereço:</strong> {{ $entregador->endereco }}, {{ $entregador->numero }}<br>
                            @if($entregador->complemento)
                                <strong>Complemento:</strong> {{ $entregador->complemento }}<br>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Bairro:</strong> {{ $entregador->bairro }}<br>
                            <strong>Cidade:</strong> {{ $entregador->cidade }}<br>
                            <strong>Estado:</strong> {{ $entregador->estado }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Veículo -->
            @if($entregador->tipo_veiculo && $entregador->tipo_veiculo != 'pe')
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-{{ $entregador->tipo_veiculo == 'moto' ? 'motorcycle' : ($entregador->tipo_veiculo == 'carro' ? 'car' : 'bicycle') }} mr-2"></i>
                            Dados do Veículo
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Tipo:</strong> {{ ucfirst($entregador->tipo_veiculo) }}<br>
                                @if($entregador->marca_veiculo)
                                    <strong>Marca:</strong> {{ $entregador->marca_veiculo }}<br>
                                @endif
                                @if($entregador->modelo_veiculo)
                                    <strong>Modelo:</strong> {{ $entregador->modelo_veiculo }}<br>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if($entregador->ano_veiculo)
                                    <strong>Ano:</strong> {{ $entregador->ano_veiculo }}<br>
                                @endif
                                @if($entregador->placa_veiculo)
                                    <strong>Placa:</strong> {{ $entregador->placa_veiculo }}<br>
                                @endif
                                @if($entregador->cor_veiculo)
                                    <strong>Cor:</strong> {{ $entregador->cor_veiculo }}
                                @endif
                            </div>
                        </div>

                        @if(in_array($entregador->tipo_veiculo, ['moto', 'carro']))
                            <hr>
                            <h6 class="font-weight-bold">CNH</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Número:</strong> {{ $entregador->cnh_numero ?? 'N/A' }}<br>
                                    <strong>Categoria:</strong> {{ $entregador->cnh_categoria ?? 'N/A' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Validade:</strong> 
                                    @if($entregador->cnh_validade)
                                        {{ $entregador->cnh_validade->format('d/m/Y') }}
                                        @if($entregador->cnh_validade->isPast())
                                            <span class="badge badge-danger ml-2">Vencida</span>
                                        @elseif($entregador->cnh_validade->diffInDays() <= 30)
                                            <span class="badge badge-warning ml-2">Próximo do Vencimento</span>
                                        @else
                                            <span class="badge badge-success ml-2">Válida</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Dados Bancários -->
            @if($entregador->banco || $entregador->pix)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-credit-card mr-2"></i>Dados Bancários
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($entregador->banco)
                                <div class="col-md-6">
                                    <strong>Banco:</strong> {{ $entregador->banco }}<br>
                                    @if($entregador->agencia)
                                        <strong>Agência:</strong> {{ $entregador->agencia }}<br>
                                    @endif
                                    @if($entregador->conta)
                                        <strong>Conta:</strong> {{ $entregador->conta }}
                                    @endif
                                </div>
                            @endif
                            @if($entregador->pix)
                                <div class="col-md-6">
                                    <strong>PIX:</strong> {{ $entregador->pix }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Documentos -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt mr-2"></i>Documentos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $documentos = [
                                'foto_rg' => 'RG',
                                'foto_cpf' => 'CPF', 
                                'foto_cnh' => 'CNH',
                                'foto_comprovante_endereco' => 'Comprovante de Endereço'
                            ];
                        @endphp
                        
                        @foreach($documentos as $campo => $nome)
                            @if($entregador->$campo)
                                <div class="col-md-3 text-center mb-3">
                                    <h6>{{ $nome }}</h6>
                                    <a href="{{ route('entregadores.documento', [$entregador, $campo]) }}" 
                                       class="btn btn-outline-primary btn-sm" target="_blank">
                                        <i class="fas fa-download mr-1"></i>Baixar
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Observações -->
            @if($entregador->observacoes_aprovacao)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-comment mr-2"></i>Observações
                        </h6>
                    </div>
                    <div class="card-body">
                        {{ $entregador->observacoes_aprovacao }}
                        @if($entregador->data_aprovacao)
                            <hr>
                            <small class="text-muted">
                                Data: {{ $entregador->data_aprovacao->format('d/m/Y H:i') }}
                                @if($entregador->aprovador)
                                    - Por: {{ $entregador->aprovador->nome }}
                                @endif
                            </small>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Avaliações Recentes -->
    @if($avaliacoesRecentes->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-star mr-2"></i>Avaliações Recentes
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($avaliacoesRecentes as $avaliacao)
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $avaliacao->cliente->nome ?? 'Cliente' }}</strong>
                                    <div>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $avaliacao->nota ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                @if($avaliacao->comentario)
                                    <p class="mt-2 mb-1">{{ $avaliacao->comentario }}</p>
                                @endif
                                <small class="text-muted">{{ $avaliacao->created_at->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Pedidos Recentes -->
    @if($pedidosRecentes->count() > 0)
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-truck mr-2"></i>Pedidos Recentes
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedidosRecentes as $pedido)
                                <tr>
                                    <td>#{{ $pedido->id }}</td>
                                    <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $pedido->status == 'entregue' ? 'success' : ($pedido->status == 'pendente' ? 'warning' : 'info') }}">
                                            {{ ucfirst($pedido->status) }}
                                        </span>
                                    </td>
                                    <td>R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-sm btn-info">
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

<!-- Modais de Aprovação/Reprovação/Suspensão -->
@if(auth()->check())
    @if($entregador->status == 'pendente')
        <!-- Modal Aprovação -->
        <div class="modal fade" id="aprovarModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('entregadores.aprovar', $entregador) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Aprovar Entregador</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Deseja aprovar o entregador <strong>{{ $entregador->nome }}</strong>?</p>
                            <div class="form-group">
                                <label for="observacoes">Observações (opcional)</label>
                                <textarea name="observacoes" class="form-control" rows="3" 
                                          placeholder="Observações sobre a aprovação..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Aprovar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Reprovação -->
        <div class="modal fade" id="reprovarModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('entregadores.reprovar', $entregador) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Reprovar Entregador</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Deseja reprovar o entregador <strong>{{ $entregador->nome }}</strong>?</p>
                            <div class="form-group">
                                <label for="observacoes">Motivo da reprovação *</label>
                                <textarea name="observacoes" class="form-control" rows="3" required
                                          placeholder="Descreva o motivo da reprovação..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Reprovar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($entregador->status == 'ativo')
        <!-- Modal Suspensão -->
        <div class="modal fade" id="suspenderModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('entregadores.suspender', $entregador) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Suspender Entregador</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Deseja suspender o entregador <strong>{{ $entregador->nome }}</strong>?</p>
                            <div class="form-group">
                                <label for="observacoes">Motivo da suspensão *</label>
                                <textarea name="observacoes" class="form-control" rows="3" required
                                          placeholder="Descreva o motivo da suspensão..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Suspender</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endif
@endsection