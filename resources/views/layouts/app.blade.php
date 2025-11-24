<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#667eea">
    <link rel="manifest" href="/manifest.json">
    <title>@yield('title', 'Dashboard') - MyD Bar & Restaurantes</title>
      <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS do Layout -->
    @include('layouts.partials.styles')
    
    <!-- Estilos Extras para Páginas -->
    <link href="{{ asset('css/layout-extras.css') }}" rel="stylesheet">
    
    <!-- CSS específico da página -->
    @stack('styles')

    <style>
        /* Estilos para modo offline */
        .offline-mode {
            filter: grayscale(20%);
        }

        #connection-status {
            position: fixed;
            top: 70px;
            right: 20px;
            z-index: 1050;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .pending-sync-badge {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
            cursor: pointer;
        }
    </style>
    @stack('head')
</head>
<body>
    <!-- Indicador de Status de Conexão -->
    <div id="connection-status"></div>

    <!-- Badge de Itens Pendentes -->
    <div id="pending-sync-badge" class="pending-sync-badge" style="display: none;"></div>

    <!-- Barra Superior -->
    @include('layouts.partials.topbar')

    <!-- Sidebar -->
    @include('layouts.partials.sidebar')    <!-- Conteúdo Principal -->
    <main class="main-content" id="mainContent">
        @yield('content')
    </main>

    <!-- Bootstrap JS Bundle (inclui Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SQL.js LOCAL - Banco de dados no navegador -->
    <script src="{{ asset('libs/sql-wasm.js') }}"></script>
    
    <!-- Banco de Dados Local Manager -->
    <script src="{{ asset('js/local-database.js') }}"></script>
    
    <!-- Offline Manager -->
    <script src="{{ asset('js/offline-manager.js') }}"></script>
    
    <!-- Scripts do Layout -->
    @include('layouts.partials.scripts')
    
    <!-- Scripts específicos da página -->
    @stack('scripts')

    <script>
        // Atualizar badge de itens pendentes
        async function updatePendingBadge() {
            if (window.localDB && window.localDB.isInitialized) {
                const stats = window.localDB.getStats();
                const badge = document.getElementById('pending-sync-badge');
                
                if (stats.pendentes > 0) {
                    badge.innerHTML = `
                        <div class="badge bg-warning text-dark p-3" style="cursor: pointer;">
                            <i class="fas fa-sync-alt me-2"></i>
                            <strong>${stats.pendentes}</strong> item(ns) aguardando sincronização
                            <br><small>Clique para sincronizar agora</small>
                        </div>
                    `;
                    badge.style.display = 'block';
                    badge.onclick = async () => {
                        badge.innerHTML = '<div class="badge bg-info p-3"><i class="fas fa-spinner fa-spin me-2"></i>Sincronizando...</div>';
                        const result = await window.localDB.sincronizarComServidor();
                        if (result.success) {
                            badge.innerHTML = '<div class="badge bg-success p-3"><i class="fas fa-check me-2"></i>Sincronizado!</div>';
                            setTimeout(() => badge.style.display = 'none', 3000);
                        } else {
                            badge.innerHTML = `<div class="badge bg-danger p-3"><i class="fas fa-times me-2"></i>${result.message}</div>`;
                        }
                        setTimeout(updatePendingBadge, 5000);
                    };
                } else {
                    badge.style.display = 'none';
                }
            }
        }

        // Atualizar a cada 10 segundos
        setInterval(updatePendingBadge, 10000);
        
        // Atualizar quando o banco estiver pronto
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(updatePendingBadge, 2000);
        });
    </script>
    @stack('scripts')
</body>
</html>
