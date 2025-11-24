<script>
 (function() {
 window.toggleUserDropdown = function() {
 const dropdown = document.querySelector('.user-dropdown');
 let userDropdownOpen = window.userDropdownOpen || false;
 userDropdownOpen = !userDropdownOpen;
 window.userDropdownOpen = userDropdownOpen;
 if (userDropdownOpen) {
 dropdown.classList.add('active');
 } else {
 dropdown.classList.remove('active');
 }
 };
 window.logout = function(event) {
 if (event && event.preventDefault) {
 event.preventDefault();
 }
 if (confirm('Tem certeza que deseja sair?')) {
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = '/logout';
 const csrfInput = document.createElement('input');
 csrfInput.type = 'hidden';
 csrfInput.name = '_token';
 csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
 form.appendChild(csrfInput);
 document.body.appendChild(form);
 form.submit();
 }
 };
 window.closeUserDropdown = function() {
 const dropdown = document.querySelector('.user-dropdown');
 if (dropdown) {
 dropdown.classList.remove('active');
 }
 window.userDropdownOpen = false;
 };
 })();
 let sidebarCollapsed = false;
 let darkMode = localStorage.getItem('darkMode') === 'true';
 let userDropdownOpen = false;
 document.addEventListener('DOMContentLoaded', function() {
 initializeLayout();
 // loadUserData() removido - dados já vêm do Blade via sessão
 setupEventListeners();
 if (darkMode) {
 document.body.classList.add('dark-mode');
 updateDarkModeIcon();
 }
 });
 
 // Função updateTopbarUser não é mais necessária - dados vêm do Blade
 // Mantida apenas para compatibilidade se algum código chamar
 function updateTopbarUser(user) {
 console.log('🔄 updateTopbarUser chamada com:', user);
 
 const userNameElement = document.getElementById('topbarUserName');
 const userRoleElement = document.getElementById('topbarUserRole');
 
 // Se os elementos não existirem (área admin), sair silenciosamente
 if (!userNameElement && !userRoleElement) {
 console.log('ℹ️ Topbar de usuário não encontrado (provavelmente área admin)');
 return;
 }
 
 const roles = {
 'admin': 'Administrador',
 'gerente': 'Gerente',
 'garcom': 'Garçom',
 'caixa': 'Caixa',
 'cliente': 'Cliente',
 'guest': 'Visitante'
 };
 
 if (userNameElement) {
 userNameElement.textContent = user.nome || 'Usuário';
 console.log('✅ Nome atualizado para:', user.nome);
 }
 
 if (userRoleElement) {
 userRoleElement.textContent = roles[user.role] || user.role || 'Usuário';
 console.log('✅ Role atualizado para:', roles[user.role]);
 }
 }
 
 function initializeLayout() {
 if (window.innerWidth <= 768) {
 const sidebar = document.getElementById('sidebar');
 sidebar.classList.add('collapsed');
 sidebarCollapsed = true;
 }
 }
 function setupEventListeners() {
 document.getElementById('sidebarToggle').addEventListener('click', toggleSidebar);
 document.getElementById('sidebarToggleBtn').addEventListener('click', toggleSidebar);
 document.addEventListener('click', function(event) {
 if (!event.target.closest('.user-dropdown')) {
 window.closeUserDropdown();
 }
 });
 window.addEventListener('resize', handleResize);
 document.addEventListener('keydown', handleKeyboardShortcuts);
 }
 function toggleSidebar() {
 const sidebar = document.getElementById('sidebar');
 const mainContent = document.getElementById('mainContent');
 const toggleBtn = document.getElementById('sidebarToggleBtn').querySelector('i');
 const overlay = document.getElementById('sidebarOverlay');
 if (window.innerWidth <= 768) {
 sidebar.classList.toggle('mobile-open');
 overlay.classList.toggle('active');
 } else {
 sidebar.classList.toggle('collapsed');
 mainContent.classList.toggle('expanded');
 sidebarCollapsed = !sidebarCollapsed;
 if (sidebarCollapsed) {
 toggleBtn.className = 'fas fa-chevron-right';
 } else {
 toggleBtn.className = 'fas fa-chevron-left';
 }
 }
 }
 function closeSidebar() {
 const sidebar = document.getElementById('sidebar');
 const overlay = document.getElementById('sidebarOverlay');
 sidebar.classList.remove('mobile-open');
 overlay.classList.remove('active');
 }
 function closeUserDropdown() {
 const dropdown = document.querySelector('.user-dropdown');
 dropdown.classList.remove('active');
 userDropdownOpen = false;
 }
 function toggleDarkMode() {
 darkMode = !darkMode;
 document.body.classList.toggle('dark-mode');
 localStorage.setItem('darkMode', darkMode);
 updateDarkModeIcon();
 }
 function updateDarkModeIcon() {
 const icon = document.getElementById('dark-mode-icon');
 if (darkMode) {
 icon.className = 'fas fa-sun';
 } else {
 icon.className = 'fas fa-moon';
 }
 }
 function loadGlobalData() {
 if (typeof carregarEstatisticas === 'function') {
 carregarEstatisticas();
 }
 updatePedidosBadge();
 updateDeliveryBadge();
 }
 function atualizarDados() {
 const refreshIcon = document.getElementById('refresh-icon');
 if (refreshIcon) {
 refreshIcon.classList.add('fa-spin');
 }
 
 if (typeof carregarEstatisticas === 'function') {
 carregarEstatisticas();
 }
 
 // Só atualiza badges se existirem
 if (document.getElementById('pedidos-pendentes-badge')) {
 updatePedidosBadge();
 }
 if (document.getElementById('delivery-ativos-badge')) {
 updateDeliveryBadge();
 }
 
 window.dispatchEvent(new CustomEvent('dataRefresh'));
 
 if (refreshIcon) {
 setTimeout(() => {
 refreshIcon.classList.remove('fa-spin');
 }, 2000);
 }
 }
 function updatePedidosBadge() {
 const badge = document.getElementById('pedidos-pendentes-badge');
 // Se o badge não existe (área admin), não fazer requisição
 if (!badge) {
 return;
 }
 
 fetch('/api/dashboard/stats', {
 method: 'GET',
 credentials: 'same-origin', // CRÍTICO: Envia cookies de sessão
 headers: {
 'Accept': 'application/json',
 'X-Requested-With': 'XMLHttpRequest'
 }
 })
 .then(response => {
 console.log('=== STATS RESPONSE ===');
 console.log('Status:', response.status);
 console.log('Headers:', response.headers);
 console.log('Content-Type:', response.headers.get('content-type'));
 
 if (!response.ok) {
 throw new Error('Resposta não OK: ' + response.status);
 }
 return response.text();
 })
 .then(text => {
 console.log('=== STATS TEXT ===');
 console.log('Tamanho:', text.length);
 console.log('Primeiros 100 chars:', text.substring(0, 100));
 console.log('Últimos 50 chars:', text.substring(text.length - 50));
 
 // Mostra primeiros bytes em código
 var codes = [];
 for (var i = 0; i < Math.min(10, text.length); i++) {
 codes.push(text.charCodeAt(i).toString(16).padStart(2, '0'));
 }
 console.log('Primeiros 10 bytes (hex):', codes.join(' '));
 
 if (!text || text.trim() === '') {
 throw new Error('Resposta vazia');
 }
 return JSON.parse(text);
 })
 .then(data => {
 const pendentes = data.pedidos_pendentes || 0;
 badge.textContent = pendentes;
 badge.style.display = pendentes > 0 ? 'block' : 'none';
 })
 .catch(error => {
 console.log('ℹ️ Badge de pedidos não disponível:', error.message);
 badge.style.display = 'none';
 });
 }
 function updateDeliveryBadge() {
 const badge = document.getElementById('delivery-ativos-badge');
 // Se o badge não existe (área admin), não fazer requisição
 if (!badge) {
 return;
 }
 
 fetch('/api/deliveries/stats/hoje', {
 method: 'GET',
 credentials: 'same-origin', // CRÍTICO: Envia cookies de sessão
 headers: {
 'Accept': 'application/json',
 'X-Requested-With': 'XMLHttpRequest'
 }
 })
 .then(response => {
 if (!response.ok) {
 throw new Error('Resposta não OK: ' + response.status);
 }
 return response.text();
 })
 .then(text => {
 if (!text || text.trim() === '') {
 throw new Error('Resposta vazia');
 }
 return JSON.parse(text);
 })
 .then(data => {
 const ativos = (data.pendentes || 0) + (data.confirmados || 0) + (data.preparando || 0) + 
 (data.prontos || 0) + (data.saiu_entrega || 0);
 badge.textContent = ativos;
 badge.style.display = ativos > 0 ? 'block' : 'none';
 })
 .catch(error => {
 console.log('ℹ️ Badge de delivery não disponível:', error.message);
 badge.style.display = 'none';
 });
 }
 function toggleNotifications() {
 showToast('Sistema de notificações em desenvolvimento!', 'info');
 }
 function showToast(message, type = 'info') {
 const toast = document.createElement('div');
 toast.className = `alert alert-${type}`;
 toast.style.cssText = `
 position: fixed;
 top: 90px;
 right: 20px;
 z-index: 1050;
 min-width: 300px;
 animation: slideInRight 0.3s ease;
 `;
 toast.innerHTML = `
 <i class="fas fa-info-circle"></i>
 <span>${message}</span>
 <button onclick="this.parentElement.remove()" style="margin-left: auto; background: none; border: none; color: inherit; cursor: pointer;">
 <i class="fas fa-times"></i>
 </button>
 `;
 document.body.appendChild(toast);
 setTimeout(() => {
 if (toast.parentElement) {
 toast.style.animation = 'slideOutRight 0.3s ease';
 setTimeout(() => toast.remove(), 300);
 }
 }, 3000);
 }
 function handleResize() {
 if (window.innerWidth <= 768) {
 const sidebar = document.getElementById('sidebar');
 const mainContent = document.getElementById('mainContent');
 sidebar.classList.add('collapsed');
 sidebar.classList.remove('mobile-open');
 mainContent.classList.remove('expanded');
 document.getElementById('sidebarOverlay').classList.remove('active');
 } else {
 const sidebar = document.getElementById('sidebar');
 sidebar.classList.remove('mobile-open');
 document.getElementById('sidebarOverlay').classList.remove('active');
 }
 }
 function handleKeyboardShortcuts(e) {
 if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
 e.preventDefault();
 toggleSidebar();
 }
 if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
 e.preventDefault();
 toggleDarkMode();
 }
 if (e.key === 'Escape') {
 closeUserDropdown();
 closeSidebar();
 }
 }
 function showMaintenanceMode() {
 showToast('Modo de manutenção em desenvolvimento!', 'warning');
 }
 function showApiStatus() {
 window.open('/myd_bar_restaurantes/monitor_api_unificada.html', '_blank');
 }
 function formatCurrency(value) {
 return new Intl.NumberFormat('pt-BR', {
 style: 'currency',
 currency: 'BRL'
 }).format(value);
 }
 function formatDate(date) {
 return new Intl.DateTimeFormat('pt-BR', {
 day: '2-digit',
 month: '2-digit',
 year: 'numeric',
 hour: '2-digit',
 minute: '2-digit'
 }).format(new Date(date));
 }
 const style = document.createElement('style');
 style.textContent = `
 @keyframes slideInRight {
 from { transform: translateX(100%); opacity: 0; }
 to { transform: translateX(0); opacity: 1; }
 }
 @keyframes slideOutRight {
 from { transform: translateX(0); opacity: 1; }
 to { transform: translateX(100%); opacity: 0; }
 }
 `;
 document.head.appendChild(style);
 setInterval(() => {
 if (document.visibilityState === 'visible') {
 updatePedidosBadge();
 updateDeliveryBadge();
 }
 }, 30000);
 window.addEventListener('dataRefresh', function() {
 console.log('Dados atualizados em toda a aplicação');
 });
 window.layoutFunctions = {
 toggleSidebar,
 toggleDarkMode,
 atualizarDados,
 showToast,
 formatCurrency,
 formatDate
 };
</script>