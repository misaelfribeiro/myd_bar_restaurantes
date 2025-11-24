<div class="top-bar">
 <div class="logo-section">
 <button class="quick-btn" id="sidebarToggle">
 <i class="fas fa-bars"></i>
 </button>
 <i class="fas fa-utensils" style="font-size: 1.3rem;"></i>
 <h1>MyD Bar & Restaurantes</h1>
 </div>
 <div class="system-info">
 <div class="quick-actions">
 <button class="quick-btn" onclick="atualizarDados()" title="Atualizar Dados">
 <i class="fas fa-sync-alt" id="refresh-icon"></i>
 </button>
 <button class="quick-btn" onclick="toggleNotifications()" title="Notificações">
 <i class="fas fa-bell"></i>
 </button>
 <button class="quick-btn" onclick="toggleDarkMode()" title="Modo Escuro">
 <i class="fas fa-moon" id="dark-mode-icon"></i>
 </button>
 </div>
 <div class="user-dropdown">
 <div class="user-info" onclick="toggleUserDropdown()">
 <div class="user-avatar">
 <i class="fas fa-user"></i>
 </div>
 <div>
 <div style="font-weight: 600; font-size: 0.9rem;" id="topbarUserName">
 @auth('admin')
 {{ auth('admin')->user()->name ?? 'Admin' }}
 @elseauth('web')
 {{ auth('web')->user()->nome ?? 'Usuário' }}
 @else
 Visitante
 @endauth
 </div>
 <div style="font-size: 0.75rem; opacity: 0.8;" id="topbarUserRole">
 @auth('admin')
 @php
 $isMaster = auth('admin')->user()->tenant_code === 'EATSFOOD' && 
 \App\Models\Empresa::where('tenant_code', 'EATSFOOD')->where('is_master', true)->exists();
 @endphp
 {{ $isMaster ? '👑 Master EatsFood' : 'Administrador' }}
 @elseauth('web')
 @php
 $roles = [
 'admin' => 'Administrador',
 'gerente' => 'Gerente', 
 'garcom' => 'Garçom',
 'caixa' => 'Caixa',
 'cliente' => 'Cliente'
 ];
 $userRole = auth('web')->user()->role ?? 'cliente';
 @endphp
 {{ $roles[$userRole] ?? ucfirst($userRole) }}
 @else
 Visitante
 @endauth
 </div>
 </div>
 <i class="fas fa-chevron-down" style="margin-left: 5px; font-size: 0.8rem;"></i>
 </div>
 <!-- Dropdown do usuário -->
 <div class="user-dropdown-content">
 <a href="#" class="user-dropdown-item">
 <i class="fas fa-user-circle"></i>
 Meu Perfil
 </a>
 <a href="#" class="user-dropdown-item">
 <i class="fas fa-cog"></i>
 Configurações
 </a>
 <div class="user-dropdown-divider"></div>
 <a href="#" class="user-dropdown-item" onclick="logout(event)">
 <i class="fas fa-sign-out-alt"></i>
 Sair
 </a>
 </div>
 </div>
 </div>
</div>
<!-- Overlay para mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
<!-- Formulário de Logout (oculto) -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
 @csrf
</form>
<script>
function performLogout(event) {
 event.preventDefault();
 if (confirm('Deseja realmente fazer logout?')) {
 const form = document.getElementById('logout-form');
 if (form) {
 console.log('Tentando logout via formulário...');
 form.submit();
 return;
 }
 const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
 if (token) {
 console.log('Tentando logout via AJAX...');
 fetch('/logout', {
 method: 'POST',
 headers: {
 'Content-Type': 'application/json',
 'X-CSRF-TOKEN': token,
 'Accept': 'application/json'
 },
 credentials: 'same-origin'
 })
 .then(response => {
 if (response.ok) {
 console.log('Logout successful');
 window.location.href = '/login';
 } else {
 console.error('Erro no logout:', response.status);
 window.location.href = '/login';
 }
 })
 .catch(error => {
 console.error('Erro na requisição de logout:', error);
 window.location.href = '/login';
 });
 } else {
 console.error('Token CSRF não encontrado');
 window.location.href = '/login';
 }
 }
}
</script>