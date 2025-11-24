@extends('layouts.app')
@section('title', 'Gerenciamento de Planos')
@section('content')
<div class="container-fluid px-4">
 <!-- Header -->
 <div class="d-flex justify-content-between align-items-center mb-4">
 <div>
 <h1 class="h3 mb-0">💎 Gerenciamento de Planos</h1>
 <p class="text-muted mb-0">Visão geral dos planos de assinatura da plataforma</p>
 </div>
 </div>
 <!-- Estatísticas por Plano -->
 <div class="row g-3 mb-4">
 <!-- Plano Básico -->
 <div class="col-md-3">
 <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #6c757d !important;">
 <div class="card-body">
 <div class="d-flex justify-content-between align-items-start mb-2">
 <div>
 <h6 class="text-muted mb-0">BÁSICO</h6>
 <h2 class="mb-0">{{ $estatisticas['basico']['total'] }}</h2>
 </div>
 <div class="bg-secondary bg-opacity-10 p-2 rounded">
 <i class="fas fa-box text-secondary fa-2x"></i>
 </div>
 </div>
 <div class="d-flex justify-content-between text-sm">
 <span><i class="fas fa-check-circle text-success me-1"></i>{{ $estatisticas['basico']['ativos'] }} ativos</span>
 <span><i class="fas fa-clock text-warning me-1"></i>{{ $estatisticas['basico']['trial'] }} trial</span>
 </div>
 <hr class="my-2">
 <div class="text-center">
 <strong class="text-success">R$ {{ number_format($estatisticas['basico']['receita_mensal'], 2, ',', '.') }}</strong>
 <small class="text-muted d-block">Receita mensal</small>
 </div>
 </div>
 </div>
 </div>
 <!-- Plano Profissional -->
 <div class="col-md-3">
 <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #17a2b8 !important;">
 <div class="card-body">
 <div class="d-flex justify-content-between align-items-start mb-2">
 <div>
 <h6 class="text-muted mb-0">PROFISSIONAL</h6>
 <h2 class="mb-0">{{ $estatisticas['profissional']['total'] }}</h2>
 </div>
 <div class="bg-info bg-opacity-10 p-2 rounded">
 <i class="fas fa-briefcase text-info fa-2x"></i>
 </div>
 </div>
 <div class="d-flex justify-content-between text-sm">
 <span><i class="fas fa-check-circle text-success me-1"></i>{{ $estatisticas['profissional']['ativos'] }} ativos</span>
 <span><i class="fas fa-clock text-warning me-1"></i>{{ $estatisticas['profissional']['trial'] }} trial</span>
 </div>
 <hr class="my-2">
 <div class="text-center">
 <strong class="text-success">R$ {{ number_format($estatisticas['profissional']['receita_mensal'], 2, ',', '.') }}</strong>
 <small class="text-muted d-block">Receita mensal</small>
 </div>
 </div>
 </div>
 </div>
 <!-- Plano Premium -->
 <div class="col-md-3">
 <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
 <div class="card-body">
 <div class="d-flex justify-content-between align-items-start mb-2">
 <div>
 <h6 class="text-muted mb-0">PREMIUM</h6>
 <h2 class="mb-0">{{ $estatisticas['premium']['total'] }}</h2>
 </div>
 <div class="bg-success bg-opacity-10 p-2 rounded">
 <i class="fas fa-crown text-success fa-2x"></i>
 </div>
 </div>
 <div class="d-flex justify-content-between text-sm">
 <span><i class="fas fa-check-circle text-success me-1"></i>{{ $estatisticas['premium']['ativos'] }} ativos</span>
 <span><i class="fas fa-clock text-warning me-1"></i>{{ $estatisticas['premium']['trial'] }} trial</span>
 </div>
 <hr class="my-2">
 <div class="text-center">
 <strong class="text-success">R$ {{ number_format($estatisticas['premium']['receita_mensal'], 2, ',', '.') }}</strong>
 <small class="text-muted d-block">Receita mensal</small>
 </div>
 </div>
 </div>
 </div>
 <!-- Plano Enterprise -->
 <div class="col-md-3">
 <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #007bff !important;">
 <div class="card-body">
 <div class="d-flex justify-content-between align-items-start mb-2">
 <div>
 <h6 class="text-muted mb-0">ENTERPRISE</h6>
 <h2 class="mb-0">{{ $estatisticas['enterprise']['total'] }}</h2>
 </div>
 <div class="bg-primary bg-opacity-10 p-2 rounded">
 <i class="fas fa-star text-primary fa-2x"></i>
 </div>
 </div>
 <div class="d-flex justify-content-between text-sm">
 <span><i class="fas fa-check-circle text-success me-1"></i>{{ $estatisticas['enterprise']['ativos'] }} ativos</span>
 <span><i class="fas fa-clock text-warning me-1"></i>{{ $estatisticas['enterprise']['trial'] }} trial</span>
 </div>
 <hr class="my-2">
 <div class="text-center">
 <strong class="text-success">R$ {{ number_format($estatisticas['enterprise']['receita_mensal'], 2, ',', '.') }}</strong>
 <small class="text-muted d-block">Receita mensal</small>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Empresas por Plano -->
 @foreach(['basico' => 'secondary', 'profissional' => 'info', 'premium' => 'success', 'enterprise' => 'primary'] as $plano => $cor)
 @if(isset($empresasPorPlano[$plano]) && $empresasPorPlano[$plano]->count() > 0)
 <div class="card border-0 shadow-sm mb-4">
 <div class="card-header bg-{{ $cor }} text-white py-3">
 <h5 class="card-title mb-0">
 <i class="fas fa-layer-group me-2"></i>Plano {{ strtoupper($plano) }} 
 <span class="badge bg-white text-{{ $cor }}">{{ $empresasPorPlano[$plano]->count() }} empresas</span>
 </h5>
 </div>
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-hover align-middle mb-0">
 <thead>
 <tr>
 <th>#Identificação</th>
 <th>Empresa</th>
 <th>Tenant Code</th>
 <th>Status</th>
 <th>Vencimento</th>
 <th class="text-end">Mensalidade</th>
 <th class="text-center">Ações</th>
 </tr>
 </thead>
 <tbody>
 @foreach($empresasPorPlano[$plano] as $empresa)
 <tr>
 <td><strong>{{ $empresa->id }}</strong></td>
 <td><strong>{{ $empresa->nome_fantasia }}</strong></td>
 <td><code>{{ $empresa->tenant_code }}</code></td>
 <td>
 <span class="badge 
 @if($empresa->status_contrato == 'ativo') bg-success
 @elseif($empresa->status_contrato == 'trial') bg-warning
 @else bg-danger
 @endif">
 {{ strtoupper($empresa->status_contrato) }}
 </span>
 </td>
 <td>
 @if($empresa->data_fim_contrato)
 {{ \Carbon\Carbon::parse($empresa->data_fim_contrato)->format('d/m/Y') }}
 @else
 <span class="text-muted">-</span>
 @endif
 </td>
 <td class="text-end"><strong class="text-success">R$ {{ number_format($empresa->valor_mensalidade, 2, ',', '.') }}</strong></td>
 <td class="text-center">
 <button class="btn btn-sm btn-outline-primary btn-change-plan" 
 data-empresa-id="{{ $empresa->id }}"
 data-plano="{{ $empresa->plano }}">
 <i class="fas fa-exchange-alt me-1"></i>Alterar Plano
 </button>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 </div>
 @endif
 @endforeach
</div>
<!-- Modal Alterar Plano -->
<div class="modal fade" id="changePlanModal" tabindex="-1">
 <div class="modal-dialog">
 <div class="modal-content">
 <div class="modal-header">
 <h5 class="modal-title">Alterar Plano da Empresa</h5>
 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
 </div>
 <div class="modal-body">
 <form id="changePlanForm">
 <input type="hidden" id="empresa_id" name="empresa_id">
 <div class="mb-3">
 <label class="form-label">Novo Plano</label>
 <select class="form-select" id="plano" name="plano" required>
 <option value="basico">Básico</option>
 <option value="profissional">Profissional</option>
 <option value="premium">Premium</option>
 <option value="enterprise">Enterprise</option>
 </select>
 </div>
 <div class="mb-3">
 <label class="form-label">Valor Mensalidade (R$)</label>
 <input type="number" class="form-control" id="valor_mensalidade" name="valor_mensalidade" step="0.01" required>
 </div>
 <div class="row">
 <div class="col-md-6 mb-3">
 <label class="form-label">Máx. Usuários</label>
 <input type="number" class="form-control" id="max_usuarios" name="max_usuarios" required>
 </div>
 <div class="col-md-6 mb-3">
 <label class="form-label">Máx. Produtos</label>
 <input type="number" class="form-control" id="max_produtos" name="max_produtos" required>
 </div>
 </div>
 <div class="row">
 <div class="col-md-6 mb-3">
 <label class="form-label">Máx. Pedidos/Mês</label>
 <input type="number" class="form-control" id="max_pedidos_mes" name="max_pedidos_mes" required>
 </div>
 <div class="col-md-6 mb-3">
 <label class="form-label">Máx. Filiais</label>
 <input type="number" class="form-control" id="max_filiais" name="max_filiais" required>
 </div>
 </div>
 </form>
 </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
 <button type="button" class="btn btn-primary" id="submitPlanButton" onclick="submitChangePlan()">Alterar Plano</button>
 </div>
 </div>
 </div>
</div>
@endsection

@push('scripts')
<script>
// v2.1 - Corrigida URL da requisição e tratamento de erros
var changePlanModal;
document.addEventListener('DOMContentLoaded', function() {
 changePlanModal = new bootstrap.Modal(document.getElementById('changePlanModal'));
 var planoSelect = document.getElementById('plano');
 if (planoSelect) {
 planoSelect.addEventListener('change', function() {
 carregarDetalhesPlano(this.value);
 });
 }
 document.querySelectorAll('.btn-change-plan').forEach(function(btn) {
 btn.addEventListener('click', function() {
 console.log('Botao clicado!');
 console.log('data-empresa-id:', this.getAttribute('data-empresa-id'));
 console.log('data-plano:', this.getAttribute('data-plano'));
 var empresaId = this.getAttribute('data-empresa-id');
 var plano = this.getAttribute('data-plano');
 console.log('Chamando openChangePlanModal com empresaId:', empresaId, 'plano:', plano);
 openChangePlanModal(empresaId, plano);
 });
 });
});
window.openChangePlanModal = function(empresaId, currentPlano) {
 console.log('openChangePlanModal chamada com:', empresaId, currentPlano);
 console.log('Tipo empresaId recebido:', typeof empresaId);
 document.getElementById('empresa_id').value = empresaId;
 document.getElementById('plano').value = currentPlano;
 console.log('Valor gravado no input:', document.getElementById('empresa_id').value);
 console.log('Plano atual:', currentPlano);
 carregarDetalhesPlano(currentPlano);
 changePlanModal.show();
};

window.carregarDetalhesPlano = function(plano) {
 console.log('Carregando detalhes do plano:', plano);
 
 // Valores padrão por plano
 var planosConfig = {
 'basico': {
 valor_sugerido: 99.90,
 max_usuarios: 5,
 max_produtos: 100,
 max_pedidos_mes: 500,
 max_filiais: 1
 },
 'profissional': {
 valor_sugerido: 199.90,
 max_usuarios: 15,
 max_produtos: 500,
 max_pedidos_mes: 2000,
 max_filiais: 3
 },
 'premium': {
 valor_sugerido: 399.90,
 max_usuarios: 50,
 max_produtos: 2000,
 max_pedidos_mes: 10000,
 max_filiais: 10
 },
 'enterprise': {
 valor_sugerido: 799.90,
 max_usuarios: 999,
 max_produtos: 99999,
 max_pedidos_mes: 999999,
 max_filiais: 99
 }
 };
 
 var config = planosConfig[plano] || planosConfig['basico'];
 console.log('Config selecionada:', config);
 
 document.getElementById('valor_mensalidade').value = config.valor_sugerido;
 document.getElementById('max_usuarios').value = config.max_usuarios;
 document.getElementById('max_produtos').value = config.max_produtos;
 document.getElementById('max_pedidos_mes').value = config.max_pedidos_mes;
 document.getElementById('max_filiais').value = config.max_filiais;
 
 console.log('Campos preenchidos com sucesso');
};

window.submitChangePlan = function() {
 var form = document.getElementById('changePlanForm');
 var empresaId = document.getElementById('empresa_id').value;
 var submitBtn = document.getElementById('submitPlanButton');
 console.log('Empresa ID capturado', empresaId);
 console.log('Tipo do empresaId', typeof empresaId);
 if (!empresaId) {
 alert('Erro: ID da empresa nao encontrado!');
 return;
 }
 if (!form.checkValidity()) {
 form.reportValidity();
 return;
 }
 submitBtn.disabled = true;
 var originalText = submitBtn.innerHTML;
 submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Alterando...';
 var data = {
 plano: document.getElementById('plano').value,
 valor_mensalidade: parseFloat(document.getElementById('valor_mensalidade').value),
 max_usuarios: parseInt(document.getElementById('max_usuarios').value),
 max_produtos: parseInt(document.getElementById('max_produtos').value),
 max_pedidos_mes: parseInt(document.getElementById('max_pedidos_mes').value),
 max_filiais: parseInt(document.getElementById('max_filiais').value)
 };
 console.log('Enviando dados');
 console.log(data);
 console.log('Empresa ID', empresaId);
 var csrfToken = document.querySelector('meta[name="csrf-token"]');
 console.log('CSRF Token element', csrfToken);
 console.log('CSRF Token value', csrfToken ? csrfToken.content : 'NAO ENCONTRADO');
 
 // Usar URL absoluta para evitar problemas de caminho
 var url = '/admin/planos/change/' + empresaId;
 console.log('URL final', url);
 console.log('Empresa ID na URL', empresaId);
 console.log('=== INICIANDO REQUISIÇÃO ===');
 
 fetch(url, {
 method: 'POST',
 headers: {
 'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
 'Accept': 'application/json',
 'Content-Type': 'application/json'
 },
 body: JSON.stringify(data)
 })
 .then(function(response) {
 console.log('=== RESPOSTA RECEBIDA ===');
 console.log('Status da resposta:', response.status);
 console.log('Status text:', response.statusText);
 console.log('Response OK?:', response.ok);
 console.log('Response headers:', response.headers);
 console.log('Content-Type:', response.headers.get('content-type'));
 
 // Clonar a resposta para poder ler o texto E o JSON
 var clonedResponse = response.clone();
 
 // Primeiro vamos ver o texto bruto da resposta
 return clonedResponse.text().then(function(text) {
 console.log('=== TEXTO BRUTO DA RESPOSTA ===');
 console.log('Tamanho:', text.length, 'caracteres');
 console.log('Primeiros 500 caracteres:', text.substring(0, 500));
 console.log('Últimos 200 caracteres:', text.substring(Math.max(0, text.length - 200)));
 
 // Verificar se está vazio
 if (!text || text.trim() === '') {
 console.error('ERRO: Resposta vazia!');
 throw new Error('Resposta vazia do servidor');
 }
 
 // Tentar parsear como JSON
 try {
 var json = JSON.parse(text);
 console.log('=== JSON PARSEADO COM SUCESSO ===');
 console.log('JSON:', json);
 return { response: response, data: json };
 } catch (parseError) {
 console.error('=== ERRO AO PARSEAR JSON ===');
 console.error('Erro:', parseError.message);
 console.error('Texto completo que causou erro:', text);
 throw new Error('Resposta não é JSON válido: ' + parseError.message);
 }
 });
 })
 .then(function(result) {
 var response = result.response;
 var data = result.data;
 
 console.log('=== PROCESSANDO RESULTADO ===');
 console.log('Dados:', data);
 
 submitBtn.disabled = false;
 submitBtn.innerHTML = originalText;
 
 if (!response.ok) {
 console.error('Resposta com erro HTTP:', response.status);
 var errorMsg = 'Erro ao alterar plano';
 if (data.message) {
 errorMsg = errorMsg + ': ' + data.message;
 }
 if (data.errors) {
 console.log('Erros de validacao:', data.errors);
 }
 alert(errorMsg);
 return;
 }
 
 if (data.success) {
 console.log('=== SUCESSO ===');
 var mensagem = 'Plano alterado com sucesso!';
 if (data.detalhes) {
 mensagem = mensagem + '\n\nDetalhes:\n';
 mensagem = mensagem + '- Novo contrato: ' + data.detalhes.novo_contrato + '\n';
 if (data.detalhes.dias_restantes > 0) {
 mensagem = mensagem + '- Dias restantes: ' + data.detalhes.dias_restantes + '\n';
 mensagem = mensagem + '- Ajuste: R$ ' + data.detalhes.ajuste_valor + ' ';
 mensagem = mensagem + '(' + (data.detalhes.tipo_ajuste === 'cobranca' ? 'cobranca adicional' : 'credito aplicado') + ')';
 }
 }
 alert(mensagem);
 changePlanModal.hide();
 location.reload();
 } else {
 console.error('=== ERRO NA RESPOSTA ===');
 var errorMsg = 'Erro ao alterar plano';
 if (data.message) {
 errorMsg = errorMsg + ': ' + data.message;
 }
 if (data.errors) {
 console.log('Erros de validacao:', data.errors);
 }
 alert(errorMsg);
 }
 })
 .catch(function(error) {
 console.error('=== ERRO NA REQUISIÇÃO ===');
 console.error('Tipo de erro:', error.name);
 console.error('Mensagem:', error.message);
 console.error('Stack:', error.stack);
 alert('Erro de comunicação: ' + error.message);
 submitBtn.disabled = false;
 submitBtn.innerHTML = originalText;
 });
};
</script>
@endpush