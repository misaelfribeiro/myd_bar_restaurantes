{{-- 
 Componente de Busca Inteligente de Clientes para Delivery
 Parâmetros:
 - modalId: ID único para o modal
 - inputId: ID único para o input de busca
 - onSelect: Função JavaScript chamada quando cliente é selecionado
 - showCreateButton: Mostrar botão de criar novo cliente (padrão: true)
--}}
@php
 $modalId = $modalId ?? 'modalBuscaCliente';
 $inputId = $inputId ?? 'inputBuscaCliente';
 $onSelect = $onSelect ?? 'selecionarCliente';
 $showCreateButton = $showCreateButton ?? true;
@endphp
<style>
.cliente-busca-container {
 position: relative;
}
.busca-input {
 border: 2px solid #e1e5e9;
 border-radius: 10px;
 padding: 0.8rem 1rem;
 font-size: 1rem;
 transition: all 0.3s ease;
 width: 100%;
}
.busca-input:focus {
 border-color: #667eea;
 box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
 outline: none;
}
.resultados-busca {
 position: absolute;
 top: 100%;
 left: 0;
 right: 0;
 background: white;
 border: 1px solid #e1e5e9;
 border-radius: 8px;
 max-height: 300px;
 overflow-y: auto;
 z-index: 1000;
 box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
 display: none;
}
.cliente-item {
 padding: 12px 16px;
 border-bottom: 1px solid #f1f5f9;
 cursor: pointer;
 transition: background 0.2s ease;
}
.cliente-item:hover {
 background: #f8fafc;
}
.cliente-item:last-child {
 border-bottom: none;
}
.cliente-nome {
 font-weight: 600;
 color: #1f2937;
 margin-bottom: 4px;
}
.cliente-info {
 font-size: 0.875rem;
 color: #6b7280;
 margin-bottom: 2px;
}
.cliente-endereco {
 font-size: 0.8rem;
 color: #10b981;
 font-style: italic;
}
.sem-endereco {
 color: #ef4444;
}
.badge-status {
 display: inline-block;
 padding: 2px 8px;
 border-radius: 12px;
 font-size: 0.75rem;
 font-weight: 500;
}
.badge-endereco-ok {
 background: #d1fae5;
 color: #065f46;
}
.badge-sem-endereco {
 background: #fee2e2;
 color: #991b1b;
}
.badge-estatistica {
 background: #ede9fe;
 color: #6b21a8;
 margin-left: 8px;
}
.loading-busca {
 padding: 16px;
 text-align: center;
 color: #6b7280;
}
.nenhum-resultado {
 padding: 16px;
 text-align: center;
 color: #9ca3af;
}
.sugestao-item {
 background: linear-gradient(45deg, #3b82f6, #1d4ed8);
 color: white;
 padding: 12px 16px;
 margin: 8px;
 border-radius: 8px;
 cursor: pointer;
 transition: transform 0.2s ease;
}
.sugestao-item:hover {
 transform: translateY(-1px);
}
</style>
<!-- Input de Busca -->
<div class="cliente-busca-container">
 <div class="input-group">
 <span class="input-group-text">
 <i class="fas fa-search"></i>
 </span>
 <input type="text" 
 id="{{ $inputId }}" 
 class="form-control busca-input" 
 placeholder="Digite telefone, nome ou endereço do cliente..."
 autocomplete="off">
 </div>
 <!-- Resultados da Busca -->
 <div id="resultados-{{ $inputId }}" class="resultados-busca"></div>
</div>
<script>
(function() {
 const inputId = '{{ $inputId }}';
 const modalId = '{{ $modalId }}';
 const onSelectCallback = window['{{ $onSelect }}'] || function(cliente) {
 console.log('Cliente selecionado:', cliente);
 };
 let timeoutBusca = null;
 document.getElementById(inputId).addEventListener('input', function(e) {
 const termo = e.target.value.trim();
 clearTimeout(timeoutBusca);
 if (termo.length >= 3) {
 timeoutBusca = setTimeout(() => {
 buscarClientesComponent(termo, inputId);
 }, 300);
 } else {
 document.getElementById(`resultados-${inputId}`).style.display = 'none';
 }
 });
 document.addEventListener('click', function(e) {
 if (!e.target.closest('.cliente-busca-container')) {
 document.getElementById(`resultados-${inputId}`).style.display = 'none';
 }
 });
 async function buscarClientesComponent(termo, inputId) {
 console.log('🔍 Buscando clientes com termo:', termo);
 const resultadosDiv = document.getElementById(`resultados-${inputId}`);
 resultadosDiv.style.display = 'block';
 resultadosDiv.innerHTML = '<div class="loading-busca"><i class="fas fa-spinner fa-spin me-2"></i>Buscando...</div>';
 try {
 const url = `/api/clientes/buscar-delivery?busca=${encodeURIComponent(termo)}&limite=10`;
 console.log('📡 URL da busca:', url);
 const response = await fetch(url);
 console.log('📡 Response status:', response.status);
 const data = await response.json();
 console.log('📦 Data recebida:', data);
 if (data.success) {
 exibirResultadosComponent(data.data, data.sugestoes, inputId);
 } else {
 console.error('❌ Erro na busca:', data.message);
 resultadosDiv.innerHTML = '<div class="nenhum-resultado">Erro na busca: ' + (data.message || 'Erro desconhecido') + '</div>';
 }
 } catch (error) {
 console.error('💥 Erro de conexão:', error);
 resultadosDiv.innerHTML = '<div class="nenhum-resultado">Erro de conexão: ' + error.message + '</div>';
 }
 }
 function exibirResultadosComponent(clientes, sugestoes, inputId) {
 console.log('📊 Exibindo resultados:', { clientes: clientes?.length || 0, sugestoes: sugestoes?.length || 0 });
 const resultadosDiv = document.getElementById(`resultados-${inputId}`);
 if (clientes.length === 0) {
 resultadosDiv.innerHTML = `
 <div class="nenhum-resultado">
 <i class="fas fa-search me-2"></i>Nenhum cliente encontrado
 <div class="mt-2">
 <small class="text-muted">Cadastre um novo cliente na página de clientes</small>
 </div>
 </div>
 `;
 return;
 }
 let html = '';
 clientes.forEach(cliente => {
 console.log('👤 Processando cliente:', cliente);
 const temEndereco = cliente.tem_endereco;
 const badgeEndereco = temEndereco 
 ? `<span class="badge badge-endereco-ok">✓ Endereço</span>`
 : `<span class="badge badge-sem-endereco">⚠ Sem endereço</span>`;
 const estatisticas = cliente.total_pedidos > 0 
 ? `<span class="badge badge-estatistica">${cliente.total_pedidos} pedidos</span>`
 : '';
 const onClickHandler = `selecionarClienteComponent{{ ucfirst($inputId) }}(${cliente.id})`;
 console.log('🔗 Handler de click:', onClickHandler);
 html += `
 <div class="cliente-item" onclick="${onClickHandler}">
 <div class="cliente-nome">
 ${cliente.nome}
 ${badgeEndereco}
 ${estatisticas}
 </div>
 <div class="cliente-info">
 <i class="fas fa-phone me-1"></i> ${cliente.telefone}
 ${cliente.email ? ` • <i class="fas fa-envelope me-1"></i> ${cliente.email}` : ''}
 </div>
 ${temEndereco ? `
 <div class="cliente-endereco">
 <i class="fas fa-map-marker-alt me-1"></i>
 ${cliente.endereco_completo}
 </div>
 ` : `
 <div class="cliente-endereco sem-endereco">
 <i class="fas fa-exclamation-triangle me-1"></i>
 Endereço não cadastrado
 </div>
 `}
 </div>
 `;
 });
 console.log('📝 HTML gerado:', html.length + ' caracteres');
 resultadosDiv.innerHTML = html;
 }
 async function selecionarClienteComponent{{ ucfirst($inputId) }}(clienteId) {
 console.log('🔍 Selecionando cliente ID:', clienteId);
 try {
 const response = await fetch(`/api/clientes/${clienteId}`);
 console.log('📡 Response status:', response.status);
 const data = await response.json();
 console.log('📦 Data recebida:', data);
 if (data.success) {
 document.getElementById(`resultados-${inputId}`).style.display = 'none';
 document.getElementById(inputId).value = data.data.nome;
 console.log('✅ Chamando callback com dados:', data.data);
 onSelectCallback(data.data);
 console.log('🎯 Callback executado com sucesso!');
 } else {
 console.error('❌ Erro na resposta da API:', data.message);
 alert('Erro: ' + data.message);
 }
 } catch (error) {
 console.error('💥 Erro ao carregar dados do cliente:', error);
 alert('Erro ao carregar dados do cliente: ' + error.message);
 }
 }
 window[`selecionarClienteComponent{{ ucfirst($inputId) }}`] = selecionarClienteComponent{{ ucfirst($inputId) }};
 window.buscarClientesComponent = buscarClientesComponent;
})();
</script>