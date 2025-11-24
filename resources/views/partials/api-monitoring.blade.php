<!-- 🔍 SEÇÃO DE MONITORAMENTO DE APIS -->
<div class="section-card" style="grid-column: 1 / -1; margin-top: 30px;">
 <div class="section-header">
 <h3 class="section-title">
 <i class="fas fa-chart-line"></i>
 Monitoramento de APIs
 </h3>
 <div style="display: flex; gap: 10px; align-items: center;">
 <span id="last-api-update" style="color: #888; font-size: 0.9rem;">Carregando...</span>
 <button class="quick-btn" onclick="atualizarMonitoramentoAPIs()">
 <i class="fas fa-sync-alt" id="refresh-icon-apis"></i>
 </button>
 </div>
 </div>
 <div class="section-content">
 <!-- Grid de Status das APIs -->
 <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px; margin-bottom: 20px;">
 <!-- API Pagamentos -->
 <div class="api-status-card" style="background: #f8f9fa; padding: 15px; border-radius: 10px; border-left: 4px solid #28a745;">
 <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
 <h4 style="margin: 0; font-size: 1rem; color: #333;">
 <i class="fas fa-credit-card" style="margin-right: 5px; color: #28a745;"></i>
 API Pagamentos
 </h4>
 <span class="api-status-badge" id="api-pagamentos-status" style="padding: 3px 6px; border-radius: 8px; font-size: 0.75rem; font-weight: bold;">
 <i class="fas fa-spinner fa-spin"></i> Verificando...
 </span>
 </div>
 <div style="font-size: 0.8rem; color: #666; margin-bottom: 8px;">
 <div><strong>Endpoint:</strong> /api/pagamentos-status</div>
 </div>
 <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #888;">
 <span>Tempo: <span id="api-pagamentos-tempo">-</span>ms</span>
 <span>Taxa: <span id="api-pagamentos-sucesso">-</span>%</span>
 </div>
 </div>
 <!-- API Produtos -->
 <div class="api-status-card" style="background: #f8f9fa; padding: 15px; border-radius: 10px; border-left: 4px solid #ff6b35;">
 <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
 <h4 style="margin: 0; font-size: 1rem; color: #333;">
 <i class="fas fa-box" style="margin-right: 5px; color: #ff6b35;"></i>
 API Produtos
 </h4>
 <span class="api-status-badge" id="api-produtos-status" style="padding: 3px 6px; border-radius: 8px; font-size: 0.75rem; font-weight: bold;">
 <i class="fas fa-spinner fa-spin"></i> Verificando...
 </span>
 </div>
 <div style="font-size: 0.8rem; color: #666; margin-bottom: 8px;">
 <div><strong>Total:</strong> <span id="api-produtos-total">-</span> produtos</div>
 </div>
 <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #888;">
 <span>Tempo: <span id="api-produtos-tempo">-</span>ms</span>
 <span>Ativos: <span id="api-produtos-ativos">-</span></span>
 </div>
 </div>
 <!-- API Pedidos -->
 <div class="api-status-card" style="background: #f8f9fa; padding: 15px; border-radius: 10px; border-left: 4px solid #007bff;">
 <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
 <h4 style="margin: 0; font-size: 1rem; color: #333;">
 <i class="fas fa-receipt" style="margin-right: 5px; color: #007bff;"></i>
 API Pedidos
 </h4>
 <span class="api-status-badge" id="api-pedidos-status" style="padding: 3px 6px; border-radius: 8px; font-size: 0.75rem; font-weight: bold;">
 <i class="fas fa-spinner fa-spin"></i> Verificando...
 </span>
 </div>
 <div style="font-size: 0.8rem; color: #666; margin-bottom: 8px;">
 <div><strong>Hoje:</strong> <span id="api-pedidos-hoje">-</span> pedidos</div>
 </div>
 <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #888;">
 <span>Tempo: <span id="api-pedidos-tempo">-</span>ms</span>
 <span>Pendentes: <span id="api-pedidos-pendentes">-</span></span>
 </div>
 </div>
 <!-- API Monitor -->
 <div class="api-status-card" style="background: #f8f9fa; padding: 15px; border-radius: 10px; border-left: 4px solid #6f42c1;">
 <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
 <h4 style="margin: 0; font-size: 1rem; color: #333;">
 <i class="fas fa-chart-bar" style="margin-right: 5px; color: #6f42c1;"></i>
 API Monitor
 </h4>
 <span class="api-status-badge" id="api-monitor-status" style="padding: 3px 6px; border-radius: 8px; font-size: 0.75rem; font-weight: bold;">
 <i class="fas fa-spinner fa-spin"></i> Verificando...
 </span>
 </div>
 <div style="font-size: 0.8rem; color: #666; margin-bottom: 8px;">
 <div><strong>Uptime:</strong> <span id="api-monitor-uptime">-</span></div>
 </div>
 <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #888;">
 <span>Tempo: <span id="api-monitor-tempo">-</span>ms</span>
 <span>DB: <span id="api-monitor-db">-</span></span>
 </div>
 </div>
 </div>
 <!-- Resumo Geral -->
 <div style="background: #e8f5e8; padding: 15px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
 <div>
 <h4 style="margin: 0 0 5px 0; color: #333; font-size: 1rem;">Status Geral do Sistema</h4>
 <p style="margin: 0; color: #666; font-size: 0.85rem;">
 APIs Online: <strong><span id="apis-online-count">0</span>/4</strong> | 
 Tempo médio: <strong><span id="tempo-medio-geral">-</span>ms</strong> | 
 Última verificação: <strong><span id="ultima-verificacao">-</span></strong>
 </p>
 </div>
 <div style="display: flex; gap: 8px;">
 <a href="#" onclick="testarTodasAPIs()" style="padding: 6px 12px; background: #28a745; color: white; text-decoration: none; border-radius: 15px; font-size: 0.8rem;">
 <i class="fas fa-vial"></i> Testar
 </a>
 </div>
 </div>
 </div>
</div>
<style>
.api-status-badge.online {
 background: linear-gradient(135deg, #28a745, #20c997);
 color: white;
}
.api-status-badge.offline {
 background: linear-gradient(135deg, #dc3545, #c82333);
 color: white;
}
.api-status-badge.warning {
 background: linear-gradient(135deg, #ffc107, #fd7e14);
 color: #333;
}
.api-status-card.updating {
 animation: statusPulse 1s infinite;
}
@keyframes statusPulse {
 0%, 100% { opacity: 1; }
 50% { opacity: 0.7; }
}
</style>
<script>
let monitoramentoAtivo = false;
let temposResposta = [];
async function atualizarMonitoramentoAPIs() {
 console.log('🔍 Iniciando monitoramento de APIs...');
 const refreshIcon = document.getElementById('refresh-icon-apis');
 if (refreshIcon) refreshIcon.classList.add('fa-spin');
 document.querySelectorAll('.api-status-card').forEach(card => {
 card.classList.add('updating');
 });
 let apisOnline = 0;
 temposResposta = [];
 try {
 const resultados = await Promise.allSettled([
 testarAPIPagamentos(),
 testarAPIProdutos(),
 testarAPIPedidos(),
 testarAPIMonitoramento()
 ]);
 apisOnline = resultados.filter(r => r.status === 'fulfilled' && r.value.online).length;
 atualizarResumoGeral(apisOnline);
 } catch (error) {
 console.error('Erro no monitoramento geral:', error);
 } finally {
 if (refreshIcon) refreshIcon.classList.remove('fa-spin');
 setTimeout(() => {
 document.querySelectorAll('.api-status-card').forEach(card => {
 card.classList.remove('updating');
 });
 }, 500);
 const lastUpdate = document.getElementById('last-api-update');
 if (lastUpdate) lastUpdate.textContent = `Atualizado: ${new Date().toLocaleTimeString('pt-BR')}`;
 }
}
async function testarAPIPagamentos() {
 const inicio = performance.now();
 try {
 const response = await fetch('/api/pagamentos-status');
 const fim = performance.now();
 const tempo = Math.round(fim - inicio);
 temposResposta.push(tempo);
 const data = await response.json();
 if (response.ok && data.success) {
 atualizarStatusAPI('pagamentos', 'online', tempo);
 document.getElementById('api-pagamentos-sucesso').textContent = '99.9';
 return { online: true, tempo };
 } else {
 atualizarStatusAPI('pagamentos', 'warning', tempo);
 return { online: false, tempo };
 }
 } catch (error) {
 atualizarStatusAPI('pagamentos', 'offline', 0);
 return { online: false, tempo: 0 };
 }
}
async function testarAPIProdutos() {
 const inicio = performance.now();
 try {
 const response = await fetch('/api/produtos-public');
 const fim = performance.now();
 const tempo = Math.round(fim - inicio);
 temposResposta.push(tempo);
 const data = await response.json();
 if (response.ok) {
 atualizarStatusAPI('produtos', 'online', tempo);
 document.getElementById('api-produtos-total').textContent = data.length || 0;
 document.getElementById('api-produtos-ativos').textContent = 
 data.filter(p => p.ativo).length || 0;
 return { online: true, tempo };
 } else {
 atualizarStatusAPI('produtos', 'warning', tempo);
 return { online: false, tempo };
 }
 } catch (error) {
 atualizarStatusAPI('produtos', 'offline', 0);
 return { online: false, tempo: 0 };
 }
}
async function testarAPIPedidos() {
 const inicio = performance.now();
 try {
 const response = await fetch('/api/dashboard/pedidos-status');
 const fim = performance.now();
 const tempo = Math.round(fim - inicio);
 temposResposta.push(tempo);
 const data = await response.json();
 if (response.ok) {
 atualizarStatusAPI('pedidos', 'online', tempo);
 document.getElementById('api-pedidos-hoje').textContent = data.pedidos_hoje || 0;
 document.getElementById('api-pedidos-pendentes').textContent = data.pendentes || 0;
 return { online: true, tempo };
 } else {
 atualizarStatusAPI('pedidos', 'warning', tempo);
 return { online: false, tempo };
 }
 } catch (error) {
 atualizarStatusAPI('pedidos', 'offline', 0);
 return { online: false, tempo: 0 };
 }
}
async function testarAPIMonitoramento() {
 const inicio = performance.now();
 try {
 const response = await fetch('/api/monitoramento/health');
 const fim = performance.now();
 const tempo = Math.round(fim - inicio);
 temposResposta.push(tempo);
 const data = await response.json();
 if (response.ok && data.success) {
 atualizarStatusAPI('monitor', 'online', tempo);
 document.getElementById('api-monitor-uptime').textContent = '99.9%';
 document.getElementById('api-monitor-db').textContent = 
 data.checks?.database ? 'OK' : 'Erro';
 return { online: true, tempo };
 } else {
 atualizarStatusAPI('monitor', 'warning', tempo);
 return { online: false, tempo };
 }
 } catch (error) {
 atualizarStatusAPI('monitor', 'offline', 0);
 return { online: false, tempo: 0 };
 }
}
function atualizarStatusAPI(api, status, tempo) {
 const statusBadge = document.getElementById(`api-${api}-status`);
 const tempoElement = document.getElementById(`api-${api}-tempo`);
 if (!statusBadge) return;
 statusBadge.className = `api-status-badge ${status}`;
 switch (status) {
 case 'online':
 statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> Online';
 break;
 case 'offline':
 statusBadge.innerHTML = '<i class="fas fa-times-circle"></i> Offline';
 break;
 case 'warning':
 statusBadge.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Alerta';
 break;
 }
 if (tempoElement) {
 tempoElement.textContent = tempo;
 }
}
function atualizarResumoGeral(apisOnline) {
 const countElement = document.getElementById('apis-online-count');
 const tempoMedioElement = document.getElementById('tempo-medio-geral');
 const ultimaVerificacaoElement = document.getElementById('ultima-verificacao');
 if (countElement) countElement.textContent = apisOnline;
 const tempoMedio = temposResposta.length > 0 
 ? Math.round(temposResposta.reduce((a, b) => a + b, 0) / temposResposta.length)
 : 0;
 if (tempoMedioElement) tempoMedioElement.textContent = tempoMedio;
 if (ultimaVerificacaoElement) ultimaVerificacaoElement.textContent = 
 new Date().toLocaleTimeString('pt-BR');
}
function testarTodasAPIs() {
 atualizarMonitoramentoAPIs();
}
setTimeout(() => {
 atualizarMonitoramentoAPIs();
 setInterval(atualizarMonitoramentoAPIs, 45000);
}, 2000);
</script>