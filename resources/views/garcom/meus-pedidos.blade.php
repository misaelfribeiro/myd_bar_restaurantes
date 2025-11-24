<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>📋 Meus Pedidos - Modo Garçom</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Reset e base */
        * {
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #6B73FF 100%);
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            margin: 0;
            padding: 0;
            color: #1f2937;
        }
        
        /* Navbar aprimorada */
        .navbar-glass {
            background: rgba(255, 255, 255, 0.12) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-top: none;
            border-left: none;
            border-right: none;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-glass .navbar-brand,
        .navbar-glass .nav-link {
            color: white !important;
            font-weight: 600;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        
        .navbar-glass .nav-link:hover {
            transform: translateY(-1px);
            text-shadow: 0 2px 8px rgba(255, 255, 255, 0.5);
        }
        
        /* Seção hero modernizada */
        .hero-section {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            margin: 25px 0;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.12);
        }
        
        .hero-section h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #fff, #e2e8f0);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .hero-section p {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 500;
        }
        
        /* Seções com glass effect aprimorado */
        .stats-section, .filters-section, .pedidos-section {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 35px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.08),
                0 8px 16px rgba(0, 0, 0, 0.04),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
            position: relative;
        }
        
        /* Cards de estatísticas modernos */
        .stat-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(248, 250, 252, 0.9));
            border-radius: 16px;
            padding: 28px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 5px;
            border-radius: 0 3px 3px 0;
            transition: all 0.4s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.15),
                0 8px 16px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card:hover::before {
            width: 8px;
        }
        
        .stat-card.pedidos::before { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
        .stat-card.valor::before { background: linear-gradient(135deg, #10b981, #34d399); }
        .stat-card.abertos::before { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
        .stat-card.finalizados::before { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
        
        /* Números das estatísticas */
        .stat-number {
            font-size: 2.8rem;
            font-weight: 900;
            margin-bottom: 8px;
            line-height: 1;
            background: linear-gradient(135deg, var(--stat-color), var(--stat-color-light));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-card.pedidos { --stat-color: #6366f1; --stat-color-light: #8b5cf6; }
        .stat-card.valor { --stat-color: #10b981; --stat-color-light: #34d399; }
        .stat-card.abertos { --stat-color: #f59e0b; --stat-color-light: #fbbf24; }
        .stat-card.finalizados { --stat-color: #8b5cf6; --stat-color-light: #a78bfa; }
        
        .stat-label {
            font-size: 1rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Cards de pedidos modernos */
        .pedido-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.95));
            border-radius: 18px;
            padding: 28px;
            margin-bottom: 25px;
            border: 1px solid rgba(229, 231, 235, 0.3);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 
                0 4px 12px rgba(0, 0, 0, 0.05),
                0 2px 4px rgba(0, 0, 0, 0.02);
        }
        
        .pedido-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 0 3px 3px 0;
            transition: all 0.4s ease;
        }
        
        .pedido-card:hover {
            transform: translateY(-8px);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.12),
                0 8px 16px rgba(0, 0, 0, 0.08);
            border-color: rgba(99, 102, 241, 0.2);
        }
        
        .pedido-card:hover::before {
            width: 10px;
        }
        
        /* Header do pedido */
        .pedido-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .pedido-numero {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1f2937;
            background: linear-gradient(135deg, #1f2937, #374151);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Status badges modernos */
        .pedido-status {
            padding: 10px 18px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 2px solid;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .pedido-status::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: all 0.6s ease;
        }
        
        .pedido-status:hover::before {
            left: 100%;
        }
        
        .status-aberto {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(251, 191, 36, 0.15));
            color: #92400e;
            border-color: #f59e0b;
        }
        
        .status-finalizado {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(52, 211, 153, 0.15));
            color: #047857;
            border-color: #10b981;
        }
        
        .status-cancelado {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(248, 113, 113, 0.15));
            color: #b91c1c;
            border-color: #ef4444;
        }
        
        /* Grid de informações responsivo */
        .pedido-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            padding: 8px 0;
        }
        
        /* Ícones modernos com gradientes */
        .info-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }
        
        .info-item:hover .info-icon {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }
        
        .icon-mesa { 
            background: linear-gradient(135deg, #6366f1, #8b5cf6); 
            color: white; 
        }
        .icon-delivery { 
            background: linear-gradient(135deg, #10b981, #34d399); 
            color: white; 
        }
        .icon-balcao { 
            background: linear-gradient(135deg, #f59e0b, #fbbf24); 
            color: white; 
        }
        .icon-tempo { 
            background: linear-gradient(135deg, #06b6d4, #67e8f9); 
            color: white; 
        }
        .icon-valor { 
            background: linear-gradient(135deg, #10b981, #34d399); 
            color: white; 
        }
        .icon-itens { 
            background: linear-gradient(135deg, #8b5cf6, #c084fc); 
            color: white; 
        }
        
        .info-text {
            flex: 1;
        }
        
        .info-label {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 3px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .info-value {
            font-weight: 700;
            color: #1f2937;
            font-size: 1rem;
        }
        
        /* Seção de itens melhorada */
        .pedido-itens {
            background: linear-gradient(135deg, rgba(243, 244, 246, 0.8), rgba(249, 250, 251, 0.8));
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(229, 231, 235, 0.5);
        }
        
        .item-lista {
            font-size: 0.95rem;
            color: #4b5563;
            line-height: 1.6;
        }
        
        .item-individual {
            padding: 12px 0;
            border-bottom: 1px solid rgba(229, 231, 235, 0.3);
            transition: all 0.3s ease;
        }
        
        .item-individual:hover {
            background: rgba(99, 102, 241, 0.02);
            border-radius: 8px;
            margin: 0 -8px;
            padding: 12px 8px;
        }
        
        .item-individual:last-child {
            border-bottom: none;
        }
        
        .item-produto {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }
        
        .item-preco-unitario {
            color: #10b981;
            font-weight: 700;
            font-size: 0.9em;
            background: rgba(16, 185, 129, 0.1);
            padding: 2px 8px;
            border-radius: 6px;
        }
        
        /* Observações aprimoradas */
        .item-observacoes {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.08), rgba(167, 139, 250, 0.08));
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 8px;
            padding: 10px 12px;
            margin-top: 8px;
            font-style: italic;
            font-size: 0.85em;
            color: #6b21a8;
        }
        
        .item-observacoes i {
            color: #8b5cf6;
            margin-right: 6px;
        }
        
        .pedido-observacoes-gerais {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(52, 211, 153, 0.08));
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 12px;
            padding: 16px;
            margin-top: 15px;
            color: #047857;
        }
        
        .pedido-observacoes-gerais i {
            color: #10b981;
            margin-right: 8px;
        }
        
        /* Botões de ação modernos */
        .pedido-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .action-btn {
            padding: 12px 20px;
            border-radius: 10px;
            border: none;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }
        
        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: all 0.6s ease;
        }
        
        .action-btn:hover::before {
            left: 100%;
        }
        
        .btn-ver {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }
        
        .btn-editar {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }
        
        .btn-trocar {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }
        
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .btn-ver:hover {
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        }
        
        .btn-editar:hover {
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        }
        
        .btn-trocar:hover {
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }
        
        /* Filtros */
        .filter-btn {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 20px;
            padding: 8px 16px;
            margin: 5px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .filter-btn.active,
        .filter-btn:hover {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            transform: translateY(-2px);
        }
        
        .date-input {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 12px;
            transition: border-color 0.3s ease;
        }
        
        .date-input:focus {
            border-color: #6366f1;
            outline: none;
        }
        
        /* Estados especiais */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        
        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 10px 20px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Modal moderno */
        .modal-trocar-mesa {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content-trocar {
            background: white;
            margin: 10% auto;
            padding: 25px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            position: relative;
        }
        
        .modal-header-trocar {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .modal-title-trocar {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }
        
        .close-modal {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
            background: none;
            border: none;
        }
        
        .close-modal:hover {
            color: #ef4444;
        }
        
        .form-group-trocar {
            margin-bottom: 20px;
        }
        
        .form-label-trocar {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }
        
        .form-select-trocar, .form-textarea-trocar {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.3s ease;
        }
        
        .form-select-trocar:focus, .form-textarea-trocar:focus {
            outline: none;
            border-color: #8b5cf6;
        }
        
        .form-textarea-trocar {
            resize: vertical;
            min-height: 80px;
        }
        
        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
        }
        
        .btn-modal-cancel, .btn-modal-confirm {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-modal-cancel {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            color: #374151;
        }
        
        .btn-modal-confirm {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            border: none;
            color: white;
        }
        
        .btn-modal-cancel:hover {
            background: #e5e7eb;
        }
        
        .btn-modal-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
        }
        
        /* Alerts */
        .alert-modal {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            display: none;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #059669;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #dc2626;
        }
        
        /* Responsividade aprimorada */
        @media (max-width: 768px) {
            .hero-section {
                padding: 20px;
                margin: 15px 0;
            }
            
            .hero-section h2 {
                font-size: 2rem;
            }
            
            .stats-section, .filters-section, .pedidos-section {
                padding: 20px;
                margin-bottom: 20px;
            }
            
            .pedido-card {
                padding: 20px;
            }
            
            .pedido-info {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .pedido-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .pedido-actions {
                justify-content: stretch;
            }
            
            .action-btn {
                flex: 1;
                justify-content: center;
            }
        }
        
        @media (max-width: 480px) {
            .stat-card {
                padding: 20px;
            }
            
            .stat-number {
                font-size: 2.2rem;
            }
            
            .info-icon {
                width: 35px;
                height: 35px;
                margin-right: 12px;
            }
        }
        
        /* Animações suaves */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .pedido-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Scrollbar customizada */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-glass">
        <div class="container">
            <a class="navbar-brand" href="{{ route('garcom.dashboard') }}">
                <i class="fas fa-utensils me-2"></i>
                Modo Garçom
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('garcom.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('garcom.cardapio') }}">
                            <i class="fas fa-book me-1"></i> Cardápio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('garcom.mesas') }}">
                            <i class="fas fa-chair me-1"></i> Mesas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('garcom.meus-pedidos') }}">
                            <i class="fas fa-receipt me-1"></i> Meus Pedidos
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('garcom.pedido-rapido') }}">
                            <i class="fas fa-plus-circle me-1"></i> Novo Pedido
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Hero -->
        <div class="hero-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">
                        <i class="fas fa-receipt me-2"></i>
                        Meus Pedidos
                    </h2>
                    <p class="mb-0">Acompanhe todos os seus pedidos e vendas</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('garcom.dashboard') }}" class="back-btn">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="stats-section">
            <h6 class="mb-3">📊 Estatísticas do Dia</h6>
            <div class="row">
                <div class="col-6 col-md-3">
                    <div class="stat-card pedidos">
                        <div class="stat-number pedidos">{{ $estatisticas['total_pedidos'] }}</div>
                        <div class="stat-label">Total Pedidos</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card valor">
                        <div class="stat-number valor">R$ {{ number_format($estatisticas['valor_total'], 2, ',', '.') }}</div>
                        <div class="stat-label">Valor Total</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card abertos">
                        <div class="stat-number abertos">{{ $estatisticas['pedidos_abertos'] }}</div>
                        <div class="stat-label">Em Andamento</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card finalizados">
                        <div class="stat-number finalizados">{{ $estatisticas['pedidos_finalizados'] }}</div>
                        <div class="stat-label">Finalizados</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters-section">
            <h6 class="mb-3">🔍 Filtros</h6>
            <form method="GET" id="filter-form">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Data:</label>
                        <input type="date" 
                               class="form-control date-input" 
                               name="data" 
                               value="{{ request('data', today()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status:</label>
                        <select class="form-select date-input" name="status">
                            <option value="">Todos os Status</option>
                            <option value="aberto" {{ request('status') == 'aberto' ? 'selected' : '' }}>Em Andamento</option>
                            <option value="finalizado" {{ request('status') == 'finalizado' ? 'selected' : '' }}>Finalizados</option>
                            <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelados</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
              <!-- Filtros rápidos -->
            <div class="mt-3 text-center">
                <button class="filter-btn {{ !request('data') || request('data') == today()->format('Y-m-d') ? 'active' : '' }}" 
                        onclick="filtrarData('')">Hoje</button>
                <button class="filter-btn" onclick="filtrarData('{{ \Carbon\Carbon::today()->subDay()->format('Y-m-d') }}')">Ontem</button>
                <button class="filter-btn" onclick="filtrarData('{{ \Carbon\Carbon::today()->startOfWeek()->format('Y-m-d') }}')">Esta Semana</button>
                <button class="filter-btn" onclick="filtrarData('{{ \Carbon\Carbon::today()->startOfMonth()->format('Y-m-d') }}')">Este Mês</button>
            </div>
        </div>

        <!-- Pedidos -->
        <div class="pedidos-section">
            <h5 class="mb-4">
                <i class="fas fa-list me-2"></i>
                Lista de Pedidos
                @if(request('data'))
                    <small class="text-muted">- {{ \Carbon\Carbon::parse(request('data'))->format('d/m/Y') }}</small>
                @endif
            </h5>

            @forelse($pedidos as $pedido)
                <div class="pedido-card">
                    <div class="pedido-header">
                        <div class="pedido-numero">Pedido #{{ $pedido->id }}</div>
                        <div class="pedido-status status-{{ $pedido->status }}">
                            @if($pedido->status == 'aberto')
                                <i class="fas fa-clock me-1"></i> Em andamento
                            @elseif($pedido->status == 'finalizado')
                                <i class="fas fa-check me-1"></i> Finalizado
                            @else
                                <i class="fas fa-times me-1"></i> Cancelado
                            @endif
                        </div>
                    </div>

                    <div class="pedido-info">
                        <div class="info-item">
                            @if($pedido->mesa)
                                <div class="info-icon icon-mesa">
                                    <i class="fas fa-chair"></i>
                                </div>
                                <div class="info-text">
                                    <div class="info-label">Mesa</div>
                                    <div class="info-value">{{ $pedido->mesa->identificador ?: 'Mesa ' . $pedido->mesa->numero }}</div>
                                </div>
                            @elseif($pedido->delivery)
                                <div class="info-icon icon-delivery">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <div class="info-text">
                                    <div class="info-label">Delivery</div>
                                    <div class="info-value">{{ $pedido->delivery->cliente_nome }}</div>
                                </div>
                            @else
                                <div class="info-icon icon-balcao">
                                    <i class="fas fa-takeout-box"></i>
                                </div>
                                <div class="info-text">
                                    <div class="info-label">Balcão</div>
                                    <div class="info-value">Retirada no Local</div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon icon-tempo">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Horário</div>
                                <div class="info-value">{{ $pedido->created_at->format('H:i') }}</div>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon icon-valor">
                                <i class="fas fa-dollar-sign"></i>
                            </div>                            <div class="info-text">
                                <div class="info-label">Valor Total</div>
                                <div class="info-value">R$ {{ number_format($pedido->total, 2, ',', '.') }}</div>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon icon-itens">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div class="info-text">
                                <div class="info-label">Itens</div>
                                <div class="info-value">{{ $pedido->itens->count() }} {{ $pedido->itens->count() == 1 ? 'item' : 'itens' }}</div>
                            </div>
                        </div>
                    </div>                    @if($pedido->itens->count() > 0)
                        <div class="pedido-itens">
                            <div class="item-lista">
                                <strong>Produtos:</strong><br>
                                @foreach($pedido->itens as $item)
                                    <div class="item-individual mb-2">
                                        <span class="item-produto">
                                            <strong>{{ $item->quantidade }}x {{ $item->produto->nome }}</strong>
                                            <span class="item-preco-unitario">- R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</span>
                                        </span>
                                        @if($item->observacoes)
                                            <div class="item-observacoes">
                                                <i class="fas fa-comment-dots text-muted me-1"></i>
                                                <small class="text-muted">{{ $item->observacoes }}</small>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif                    @if($pedido->observacoes)
                        <div class="pedido-observacoes-gerais">
                            <strong><i class="fas fa-sticky-note me-1"></i>Observações do Pedido:</strong> {{ $pedido->observacoes }}
                        </div>
                    @endif                    <div class="pedido-actions">
                        <a href="/pedidos/{{ $pedido->id }}" class="action-btn btn-ver">
                            <i class="fas fa-eye"></i> Ver Detalhes
                        @if($pedido->status == 'aberto' && $pedido->mesa)
                            @php
                                $mesaNome = str_replace("'", "\\'", $pedido->mesa->identificador ?: 'Mesa ' . $pedido->mesa->numero);
                            @endphp
                            <button onclick="abrirModalTrocarMesa({{ $pedido->id }}, '{{ $mesaNome }}')", class="action-btn btn-trocar">
                                <i class="fas fa-exchange-alt"></i> Trocar Mesa
                            </button>
                            <a href="/pedidos/{{ $pedido->id }}/edit" class="action-btn btn-editar">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-receipt fa-3x mb-3"></i>
                    <h5>Nenhum pedido encontrado</h5>
                    @if(request('data') || request('status'))
                        <p>Tente alterar os filtros ou selecionar outra data.</p>
                        <a href="{{ route('garcom.meus-pedidos') }}" class="btn btn-primary">
                            <i class="fas fa-refresh me-1"></i> Ver Todos
                        </a>
                    @else
                        <p>Você ainda não fez nenhum pedido hoje.</p>
                        <a href="{{ route('garcom.pedido-rapido') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Criar Primeiro Pedido
                        </a>
                    @endif
                </div>
            @endforelse

            <!-- Paginação -->
            @if($pedidos->hasPages())
                <div class="pagination-wrapper">
                    {{ $pedidos->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Modal Trocar Mesa -->
    <div id="modalTrocarMesa" class="modal-trocar-mesa">
        <div class="modal-content-trocar">
            <div class="modal-header-trocar">
                <h3 class="modal-title-trocar">🔄 Trocar Mesa do Pedido</h3>
                <button class="close-modal" onclick="fecharModalTrocarMesa()">×</button>
            </div>
            
            <div class="alert-modal alert-success" id="alertSuccess">
                <strong>Sucesso!</strong> <span id="mensagemSucesso"></span>
            </div>
            
            <div class="alert-modal alert-error" id="alertError">
                <strong>Erro!</strong> <span id="mensagemErro"></span>
            </div>
            
            <form id="formTrocarMesa">
                <div class="form-group-trocar">
                    <label class="form-label-trocar">Mesa Atual:</label>
                    <input type="text" id="mesaAtual" class="form-select-trocar" readonly>
                </div>
                
                <div class="form-group-trocar">
                    <label class="form-label-trocar">Nova Mesa:</label>
                    <select id="novaMesa" class="form-select-trocar" required>
                        <option value="">Selecione uma mesa...</option>
                    </select>
                </div>
                
                <div class="form-group-trocar">
                    <label class="form-label-trocar">Motivo da Troca (opcional):</label>
                    <textarea id="motivoTroca" class="form-textarea-trocar" placeholder="Ex: Cliente solicitou mesa mais reservada..."></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-modal-cancel" onclick="fecharModalTrocarMesa()">Cancelar</button>
                    <button type="submit" class="btn-modal-confirm">Confirmar Troca</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function filtrarData(data) {
            const form = document.getElementById('filter-form');
            const dataInput = form.querySelector('input[name="data"]');
            dataInput.value = data;
            form.submit();
        }

        // Auto submit on status change
        document.querySelector('select[name="status"]').addEventListener('change', function() {
            document.getElementById('filter-form').submit();
        });

        // Auto submit on date change
        document.querySelector('input[name="data"]').addEventListener('change', function() {
            document.getElementById('filter-form').submit();
        });

        // Animações de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.pedido-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateX(0)';
                }, index * 100);
            });
            
            // Carregar mesas disponíveis
            carregarMesasDisponiveis();
            
            // Setup do form de trocar mesa
            document.getElementById('formTrocarMesa').addEventListener('submit', function(e) {
                e.preventDefault();
                trocarMesa();
            });
        });

        let pedidoAtual = null;

        function abrirModalTrocarMesa(pedidoId, mesaAtual) {
            pedidoAtual = pedidoId;
            document.getElementById('mesaAtual').value = mesaAtual;
            document.getElementById('modalTrocarMesa').style.display = 'block';
            
            // Esconder alerts
            document.getElementById('alertSuccess').style.display = 'none';
            document.getElementById('alertError').style.display = 'none';
            
            // Limpar formulário
            document.getElementById('novaMesa').value = '';
            document.getElementById('motivoTroca').value = '';
            
            // Recarregar mesas
            carregarMesasDisponiveis();
        }

        function fecharModalTrocarMesa() {
            document.getElementById('modalTrocarMesa').style.display = 'none';
            pedidoAtual = null;
        }

        async function carregarMesasDisponiveis() {
            try {
                const response = await fetch('/garcom/dashboard-data', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Erro ao carregar mesas');
                }
                
                const data = await response.json();
                const select = document.getElementById('novaMesa');
                
                // Limpar opções
                select.innerHTML = '<option value="">Selecione uma mesa...</option>';
                
                // Adicionar mesas disponíveis
                data.mesasDisponiveisInfo.forEach(mesa => {
                    const nome = mesa.identificador || `Mesa ${mesa.numero}`;
                    const option = document.createElement('option');
                    option.value = mesa.id;
                    option.textContent = nome;
                    select.appendChild(option);
                });
                
            } catch (error) {
                console.error('Erro ao carregar mesas:', error);
                mostrarAlerta('error', 'Erro ao carregar mesas disponíveis');
            }
        }

        async function trocarMesa() {
            if (!pedidoAtual) return;
            
            const novaMesaId = document.getElementById('novaMesa').value;
            const motivo = document.getElementById('motivoTroca').value;
            
            if (!novaMesaId) {
                mostrarAlerta('error', 'Selecione uma mesa');
                return;
            }
            
            try {
                const response = await fetch('/garcom/trocar-mesa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        pedido_id: pedidoAtual,
                        nova_mesa_id: novaMesaId,
                        motivo: motivo
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarAlerta('success', data.message);
                    
                    // Recarregar a página após 2 segundos para mostrar as alterações
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    
                } else {
                    mostrarAlerta('error', data.message);
                }
                
            } catch (error) {
                console.error('Erro ao trocar mesa:', error);
                mostrarAlerta('error', 'Erro ao trocar mesa. Tente novamente.');
            }
        }

        function mostrarAlerta(tipo, mensagem) {
            const alertSuccess = document.getElementById('alertSuccess');
            const alertError = document.getElementById('alertError');
            
            // Esconder ambos os alertas primeiro
            alertSuccess.style.display = 'none';
            alertError.style.display = 'none';
            
            if (tipo === 'success') {
                document.getElementById('mensagemSucesso').textContent = mensagem;
                alertSuccess.style.display = 'block';
            } else {
                document.getElementById('mensagemErro').textContent = mensagem;
                alertError.style.display = 'block';
            }
        }

        // Fechar modal ao clicar fora dele
        window.onclick = function(event) {
            const modal = document.getElementById('modalTrocarMesa');
            if (event.target === modal) {
                fecharModalTrocarMesa();
            }
        };
    </script>
</body>
</html>