@extends('layouts.app')
@section('title', 'Nova Empresa')
@section('content')
<div class="container-fluid">
 <div class="d-flex justify-content-between align-items-center mb-4">
 <h1><i class="fas fa-building me-2"></i>Nova Empresa</h1>
 <a href="{{ route('empresas.index') }}" class="btn btn-secondary">
 <i class="fas fa-arrow-left me-2"></i>Voltar
 </a>
 </div>
 <form action="{{ route('empresas.store') }}" method="POST" enctype="multipart/form-data" id="formEmpresa">
 @csrf
 <div class="row">
 <div class="col-lg-8">
 <!-- Dados Principais -->
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Dados Principais</h5>
 </div>
 <div class="card-body">
 <div class="row mb-3">
 <div class="col-md-6">
 <label for="tipo" class="form-label">Tipo de Estabelecimento *</label>
 <select class="form-select @error('tipo') is-invalid @enderror" 
 id="tipo" name="tipo" required onchange="toggleMatriz()">
 <option value="matriz" {{ old('tipo') == 'matriz' ? 'selected' : '' }}>Matriz</option>
 <option value="filial" {{ old('tipo') == 'filial' ? 'selected' : '' }}>Filial</option>
 </select>
 @error('tipo')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-6" id="matrizDiv" style="display: none;">
 <label for="empresa_matriz_id" class="form-label">Matriz *</label>
 <select class="form-select @error('empresa_matriz_id') is-invalid @enderror" 
 id="empresa_matriz_id" name="empresa_matriz_id">
 <option value="">Selecione a matriz</option>
 @foreach($matrizes as $matriz)
 <option value="{{ $matriz->id }}" {{ old('empresa_matriz_id') == $matriz->id ? 'selected' : '' }}>
 {{ $matriz->nome_fantasia }}
 </option>
 @endforeach
 </select>
 @error('empresa_matriz_id')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-6">
 <label for="nome_fantasia" class="form-label">Nome Fantasia *</label>
 <input type="text" class="form-control @error('nome_fantasia') is-invalid @enderror" 
 id="nome_fantasia" name="nome_fantasia" 
 value="{{ old('nome_fantasia') }}" required>
 @error('nome_fantasia')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-6">
 <label for="razao_social" class="form-label">Razão Social *</label>
 <input type="text" class="form-control @error('razao_social') is-invalid @enderror" 
 id="razao_social" name="razao_social" 
 value="{{ old('razao_social') }}" required>
 @error('razao_social')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-4">
 <label for="cnpj" class="form-label">CNPJ *</label>
 <input type="text" class="form-control @error('cnpj') is-invalid @enderror" 
 id="cnpj" name="cnpj" 
 value="{{ old('cnpj') }}" 
 placeholder="00.000.000/0000-00" required>
 @error('cnpj')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4">
 <label for="inscricao_estadual" class="form-label">Inscrição Estadual</label>
 <input type="text" class="form-control @error('inscricao_estadual') is-invalid @enderror" 
 id="inscricao_estadual" name="inscricao_estadual" 
 value="{{ old('inscricao_estadual') }}">
 @error('inscricao_estadual')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4">
 <label for="inscricao_municipal" class="form-label">Inscrição Municipal</label>
 <input type="text" class="form-control @error('inscricao_municipal') is-invalid @enderror" 
 id="inscricao_municipal" name="inscricao_municipal" 
 value="{{ old('inscricao_municipal') }}">
 @error('inscricao_municipal')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-6">
 <label for="email" class="form-label">E-mail *</label>
 <input type="email" class="form-control @error('email') is-invalid @enderror" 
 id="email" name="email" 
 value="{{ old('email') }}" required>
 @error('email')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-6">
 <label for="site" class="form-label">Website</label>
 <input type="url" class="form-control @error('site') is-invalid @enderror" 
 id="site" name="site" 
 value="{{ old('site') }}" 
 placeholder="https://exemplo.com">
 @error('site')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-6">
 <label for="telefone" class="form-label">Telefone *</label>
 <input type="text" class="form-control @error('telefone') is-invalid @enderror" 
 id="telefone" name="telefone" 
 value="{{ old('telefone') }}" 
 placeholder="(00) 0000-0000" required>
 @error('telefone')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-6">
 <label for="celular" class="form-label">Celular</label>
 <input type="text" class="form-control @error('celular') is-invalid @enderror" 
 id="celular" name="celular" 
 value="{{ old('celular') }}" 
 placeholder="(00) 00000-0000">
 @error('celular')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <div class="col-12">
 <label for="descricao" class="form-label">Descrição</label>
 <textarea class="form-control @error('descricao') is-invalid @enderror" 
 id="descricao" name="descricao" rows="3">{{ old('descricao') }}</textarea>
 @error('descricao')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 </div>
 <!-- Endereço e Geolocalização -->
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Endereço e Localização</h5>
 </div>
 <div class="card-body">
 <div class="row mb-3">
 <div class="col-md-3">
 <label for="endereco_cep" class="form-label">CEP *</label>
 <input type="text" class="form-control @error('endereco_cep') is-invalid @enderror" 
 id="endereco_cep" name="endereco_cep" 
 value="{{ old('endereco_cep') }}" 
 placeholder="00000-000" required>
 @error('endereco_cep')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 <small class="text-muted">Preencha para buscar automaticamente</small>
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-8">
 <label for="endereco_rua" class="form-label">Rua *</label>
 <input type="text" class="form-control @error('endereco_rua') is-invalid @enderror" 
 id="endereco_rua" name="endereco_rua" 
 value="{{ old('endereco_rua') }}" required>
 @error('endereco_rua')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4">
 <label for="endereco_numero" class="form-label">Número *</label>
 <input type="text" class="form-control @error('endereco_numero') is-invalid @enderror" 
 id="endereco_numero" name="endereco_numero" 
 value="{{ old('endereco_numero') }}" required>
 @error('endereco_numero')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-6">
 <label for="endereco_complemento" class="form-label">Complemento</label>
 <input type="text" class="form-control @error('endereco_complemento') is-invalid @enderror" 
 id="endereco_complemento" name="endereco_complemento" 
 value="{{ old('endereco_complemento') }}">
 @error('endereco_complemento')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-6">
 <label for="endereco_bairro" class="form-label">Bairro *</label>
 <input type="text" class="form-control @error('endereco_bairro') is-invalid @enderror" 
 id="endereco_bairro" name="endereco_bairro" 
 value="{{ old('endereco_bairro') }}" required>
 @error('endereco_bairro')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-8">
 <label for="endereco_cidade" class="form-label">Cidade *</label>
 <input type="text" class="form-control @error('endereco_cidade') is-invalid @enderror" 
 id="endereco_cidade" name="endereco_cidade" 
 value="{{ old('endereco_cidade') }}" required>
 @error('endereco_cidade')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-4">
 <label for="endereco_estado" class="form-label">Estado *</label>
 <select class="form-select @error('endereco_estado') is-invalid @enderror" 
 id="endereco_estado" name="endereco_estado" required>
 <option value="">Selecione</option>
 <option value="AC">AC</option>
 <option value="AL">AL</option>
 <option value="AP">AP</option>
 <option value="AM">AM</option>
 <option value="BA">BA</option>
 <option value="CE">CE</option>
 <option value="DF">DF</option>
 <option value="ES">ES</option>
 <option value="GO">GO</option>
 <option value="MA">MA</option>
 <option value="MT">MT</option>
 <option value="MS">MS</option>
 <option value="MG">MG</option>
 <option value="PA">PA</option>
 <option value="PB">PB</option>
 <option value="PR">PR</option>
 <option value="PE">PE</option>
 <option value="PI">PI</option>
 <option value="RJ">RJ</option>
 <option value="RN">RN</option>
 <option value="RS">RS</option>
 <option value="RO">RO</option>
 <option value="RR">RR</option>
 <option value="SC">SC</option>
 <option value="SP">SP</option>
 <option value="SE">SE</option>
 <option value="TO">TO</option>
 </select>
 @error('endereco_estado')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row mb-3">
 <div class="col-md-6">
 <label for="latitude" class="form-label">Latitude</label>
 <input type="text" class="form-control @error('latitude') is-invalid @enderror" 
 id="latitude" name="latitude" 
 value="{{ old('latitude') }}" readonly>
 @error('latitude')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-6">
 <label for="longitude" class="form-label">Longitude</label>
 <input type="text" class="form-control @error('longitude') is-invalid @enderror" 
 id="longitude" name="longitude" 
 value="{{ old('longitude') }}" readonly>
 @error('longitude')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="mb-3">
 <button type="button" class="btn btn-info" onclick="obterGeolocalizacao()">
 <i class="fas fa-map-pin me-2"></i>Obter Localização Atual
 </button>
 <button type="button" class="btn btn-secondary" onclick="buscarPorEndereco()">
 <i class="fas fa-search me-2"></i>Buscar por Endereço
 </button>
 </div>
 <!-- Mapa -->
 <div id="map" style="height: 400px; border-radius: 8px;" class="mb-3"></div>
 <small class="text-muted">
 <i class="fas fa-info-circle me-1"></i>
 Arraste o marcador no mapa para ajustar a localização precisa
 </small>
 </div>
 </div>
 <!-- Horário de Funcionamento -->
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Horário de Funcionamento</h5>
 </div>
 <div class="card-body">
 <div class="row mb-3">
 <div class="col-md-6">
 <label for="horario_abertura" class="form-label">Horário de Abertura</label>
 <input type="time" class="form-control @error('horario_abertura') is-invalid @enderror" 
 id="horario_abertura" name="horario_abertura" 
 value="{{ old('horario_abertura') }}">
 @error('horario_abertura')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="col-md-6">
 <label for="horario_fechamento" class="form-label">Horário de Fechamento</label>
 <input type="time" class="form-control @error('horario_fechamento') is-invalid @enderror" 
 id="horario_fechamento" name="horario_fechamento" 
 value="{{ old('horario_fechamento') }}">
 @error('horario_fechamento')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 <div class="row">
 <div class="col-12">
 <label class="form-label">Dias de Funcionamento</label>
 <div class="d-flex flex-wrap gap-2">
 @foreach(['seg' => 'Segunda', 'ter' => 'Terça', 'qua' => 'Quarta', 'qui' => 'Quinta', 'sex' => 'Sexta', 'sab' => 'Sábado', 'dom' => 'Domingo'] as $dia => $nome)
 <div class="form-check">
 <input class="form-check-input" type="checkbox" 
 name="dias_funcionamento[]" value="{{ $dia }}" 
 id="dia_{{ $dia }}"
 {{ is_array(old('dias_funcionamento')) && in_array($dia, old('dias_funcionamento')) ? 'checked' : '' }}>
 <label class="form-check-label" for="dia_{{ $dia }}">
 {{ $nome }}
 </label>
 </div>
 @endforeach
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Sidebar -->
 <div class="col-lg-4">
 <!-- Logo -->
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-image me-2"></i>Logo</h5>
 </div>
 <div class="card-body text-center">
 <img id="logoPreview" 
 src="https://via.placeholder.com/300x300?text=Logo" 
 class="img-fluid rounded mb-3" 
 style="max-height: 200px; object-fit: cover;">
 <input type="file" class="form-control @error('logo') is-invalid @enderror" 
 id="logo" name="logo" accept="image/*" onchange="previewLogo(this)">
 @error('logo')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 <small class="text-muted d-block mt-2">
 Formatos aceitos: JPG, PNG, GIF. Máximo: 2MB
 </small>
 </div>
 </div>
 <!-- Configurações de Delivery -->
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-motorcycle me-2"></i>Delivery</h5>
 </div>
 <div class="card-body">
 <div class="mb-3 form-check form-switch">
 <input class="form-check-input" type="checkbox" role="switch" 
 id="aceita_delivery" name="aceita_delivery" value="1"
 {{ old('aceita_delivery', true) ? 'checked' : '' }}>
 <label class="form-check-label" for="aceita_delivery">
 Aceita Delivery
 </label>
 </div>
 <div class="mb-3">
 <label for="taxa_entrega_padrao" class="form-label">Taxa de Entrega Padrão (R$)</label>
 <input type="number" step="0.01" min="0" 
 class="form-control @error('taxa_entrega_padrao') is-invalid @enderror" 
 id="taxa_entrega_padrao" name="taxa_entrega_padrao" 
 value="{{ old('taxa_entrega_padrao', '0.00') }}">
 @error('taxa_entrega_padrao')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="mb-3">
 <label for="raio_entrega_km" class="form-label">Raio de Entrega (km)</label>
 <input type="number" step="0.1" min="0" 
 class="form-control @error('raio_entrega_km') is-invalid @enderror" 
 id="raio_entrega_km" name="raio_entrega_km" 
 value="{{ old('raio_entrega_km') }}">
 @error('raio_entrega_km')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 <div class="mb-3">
 <label for="pedido_minimo" class="form-label">Pedido Mínimo (R$)</label>
 <input type="number" step="0.01" min="0" 
 class="form-control @error('pedido_minimo') is-invalid @enderror" 
 id="pedido_minimo" name="pedido_minimo" 
 value="{{ old('pedido_minimo', '0.00') }}">
 @error('pedido_minimo')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 </div>
 </div>
 </div>
 <!-- Status -->
 <div class="card mb-4">
 <div class="card-header">
 <h5 class="mb-0"><i class="fas fa-toggle-on me-2"></i>Status</h5>
 </div>
 <div class="card-body">
 <div class="form-check form-switch">
 <input class="form-check-input" type="checkbox" role="switch" 
 id="ativo" name="ativo" value="1"
 {{ old('ativo', true) ? 'checked' : '' }}>
 <label class="form-check-label" for="ativo">
 Empresa Ativa
 </label>
 </div>
 </div>
 </div>
 <!-- Botões -->
 <div class="card">
 <div class="card-body">
 <button type="submit" class="btn btn-primary w-100 mb-2">
 <i class="fas fa-save me-2"></i>Salvar Empresa
 </button>
 <a href="{{ route('empresas.index') }}" class="btn btn-secondary w-100">
 <i class="fas fa-times me-2"></i>Cancelar
 </a>
 </div>
 </div>
 </div>
 </div>
 </form>
</div>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map;
let marker;
document.addEventListener('DOMContentLoaded', function() {
 const defaultLat = -23.5505;
 const defaultLng = -46.6333;
 map = L.map('map').setView([defaultLat, defaultLng], 13);
 L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
 attribution: ' OpenStreetMap contributors'
 }).addTo(map);
 marker = L.marker([defaultLat, defaultLng], {
 draggable: true
 }).addTo(map);
 marker.on('dragend', function(e) {
 const position = marker.getLatLng();
 document.getElementById('latitude').value = position.lat.toFixed(8);
 document.getElementById('longitude').value = position.lng.toFixed(8);
 });
 const lat = document.getElementById('latitude').value;
 const lng = document.getElementById('longitude').value;
 if (lat && lng) {
 atualizarMapa(parseFloat(lat), parseFloat(lng));
 }
 toggleMatriz();
});
function obterGeolocalizacao() {
 if (navigator.geolocation) {
 navigator.geolocation.getCurrentPosition(function(position) {
 const lat = position.coords.latitude;
 const lng = position.coords.longitude;
 atualizarMapa(lat, lng);
 }, function(error) {
 alert('Erro ao obter localização: ' + error.message);
 });
 } else {
 alert('Geolocalização não é suportada pelo seu navegador');
 }
}
async function buscarPorEndereco() {
 const rua = document.getElementById('endereco_rua').value;
 const numero = document.getElementById('endereco_numero').value;
 const bairro = document.getElementById('endereco_bairro').value;
 const cidade = document.getElementById('endereco_cidade').value;
 const estado = document.getElementById('endereco_estado').value;
 if (!rua || !cidade || !estado) {
 alert('Preencha pelo menos Rua, Cidade e Estado');
 return;
 }
 const endereco = `${rua}, ${numero}, ${bairro}, ${cidade}, ${estado}, Brasil`;
 try {
 const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(endereco)}`);
 const data = await response.json();
 if (data && data.length > 0) {
 const lat = parseFloat(data[0].lat);
 const lng = parseFloat(data[0].lon);
 atualizarMapa(lat, lng);
 } else {
 alert('Endereço não encontrado. Tente ajustar o marcador no mapa manualmente.');
 }
 } catch (error) {
 console.error('Erro ao buscar endereço:', error);
 alert('Erro ao buscar endereço. Tente novamente.');
 }
}
function atualizarMapa(lat, lng) {
 map.setView([lat, lng], 16);
 marker.setLatLng([lat, lng]);
 document.getElementById('latitude').value = lat.toFixed(8);
 document.getElementById('longitude').value = lng.toFixed(8);
}
document.getElementById('endereco_cep').addEventListener('blur', async function() {
 const cep = this.value.replace(/\D/g, '');
 if (cep.length === 8) {
 try {
 const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
 const data = await response.json();
 if (!data.erro) {
 document.getElementById('endereco_rua').value = data.logradouro;
 document.getElementById('endereco_bairro').value = data.bairro;
 document.getElementById('endereco_cidade').value = data.localidade;
 document.getElementById('endereco_estado').value = data.uf;
 setTimeout(buscarPorEndereco, 500);
 }
 } catch (error) {
 console.error('Erro ao buscar CEP:', error);
 }
 }
});
function previewLogo(input) {
 if (input.files && input.files[0]) {
 const reader = new FileReader();
 reader.onload = function(e) {
 document.getElementById('logoPreview').src = e.target.result;
 };
 reader.readAsDataURL(input.files[0]);
 }
}
function toggleMatriz() {
 const tipo = document.getElementById('tipo').value;
 const matrizDiv = document.getElementById('matrizDiv');
 const matrizSelect = document.getElementById('empresa_matriz_id');
 if (tipo === 'filial') {
 matrizDiv.style.display = 'block';
 matrizSelect.required = true;
 } else {
 matrizDiv.style.display = 'none';
 matrizSelect.required = false;
 matrizSelect.value = '';
 }
}
document.getElementById('cnpj').addEventListener('input', function(e) {
 let value = e.target.value.replace(/\D/g, '');
 if (value.length <= 14) {
 value = value.replace(/^(\d{2})(\d)/, '$1.$2');
 value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
 value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
 value = value.replace(/(\d{4})(\d)/, '$1-$2');
 }
 e.target.value = value;
});
document.getElementById('telefone').addEventListener('input', function(e) {
 let value = e.target.value.replace(/\D/g, '');
 if (value.length <= 10) {
 value = value.replace(/^(\d{2})(\d)/, '($1) $2');
 value = value.replace(/(\d{4})(\d)/, '$1-$2');
 }
 e.target.value = value;
});
document.getElementById('celular').addEventListener('input', function(e) {
 let value = e.target.value.replace(/\D/g, '');
 if (value.length <= 11) {
 value = value.replace(/^(\d{2})(\d)/, '($1) $2');
 value = value.replace(/(\d{5})(\d)/, '$1-$2');
 }
 e.target.value = value;
});
document.getElementById('endereco_cep').addEventListener('input', function(e) {
 let value = e.target.value.replace(/\D/g, '');
 if (value.length <= 8) {
 value = value.replace(/^(\d{5})(\d)/, '$1-$2');
 }
 e.target.value = value;
});
</script>
@endsection