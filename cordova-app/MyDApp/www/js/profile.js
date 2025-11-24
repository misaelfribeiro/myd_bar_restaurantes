// Perfil do cliente

async function showProfile() {
    if (!appState.user || !appState.token) {
        showAlert('Faça login para acessar seu perfil', 'warning');
        setTimeout(() => showLogin(), 1500);
        return;
    }

    // Buscar dados atualizados do perfil
    try {
        const response = await authFetch(`${API_BASE_URL}/app/auth/me`);
        const userData = await response.json();
        
        if (userData.success) {
            appState.user = userData.cliente;
            localStorage.setItem('app_user', JSON.stringify(userData.cliente));
        }
    } catch (error) {
        console.error('Erro ao carregar perfil:', error);
    }

    const user = appState.user;
    
    const content = `
        <div class="fade-in">
            <div class="text-center mb-4">
                <i class="fas fa-user-circle fa-4x text-primary mb-3"></i>
                <h4>Meu Perfil</h4>
                <p class="text-muted">Gerencie suas informações pessoais</p>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Informações Pessoais</h6>
                    
                    <form id="profileForm" onsubmit="updateProfile(event)">
                        <div class="mb-3">
                            <label class="form-label">Nome completo</label>
                            <input type="text" class="form-control" id="profileNome" 
                                   value="${user.nome || ''}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="tel" class="form-control" id="profileTelefone" 
                                   value="${user.telefone || ''}" required>
                            <small class="text-muted">Usado para login e contato</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="profileEmail" 
                                   value="${user.email || ''}" placeholder="seu@email.com">
                            <small class="text-muted">Opcional - para receber pedidos e promoções</small>
                        </div>

                        <hr class="my-4">

                        <h6 class="text-muted mb-3">Endereço de Entrega</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Rua / Avenida</label>
                            <input type="text" class="form-control" id="profileEnderecoRua" 
                                   value="${user.endereco_rua || ''}" 
                                   placeholder="Rua das Flores">
                            <small class="text-muted">Será usado automaticamente nos seus pedidos</small>
                        </div>

                        <div class="row">
                            <div class="col-4">
                                <div class="mb-3">
                                    <label class="form-label">Número</label>
                                    <input type="text" class="form-control" id="profileEnderecoNumero" 
                                           value="${user.endereco_numero || ''}" placeholder="123">
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="mb-3">
                                    <label class="form-label">Bairro</label>
                                    <input type="text" class="form-control" id="profileEnderecoBairro" 
                                           value="${user.endereco_bairro || ''}" placeholder="Centro">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-7">
                                <div class="mb-3">
                                    <label class="form-label">Cidade</label>
                                    <input type="text" class="form-control" id="profileEnderecoCidade" 
                                           value="${user.endereco_cidade || ''}" placeholder="São Paulo">
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="mb-3">
                                    <label class="form-label">CEP</label>
                                    <input type="text" class="form-control" id="profileEnderecoCep" 
                                           value="${user.endereco_cep || ''}" placeholder="00000-000">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100" id="saveProfileBtn">
                            <i class="fas fa-save me-2"></i>Salvar Alterações
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-danger">
                <div class="card-body">
                    <h6 class="text-danger mb-3">Zona de Perigo</h6>
                    <button onclick="confirmLogout()" class="btn btn-outline-danger w-100">
                        <i class="fas fa-sign-out-alt me-2"></i>Sair da Conta
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('mainContent').innerHTML = content;
}

async function updateProfile(event) {
    event.preventDefault();
    
    const btn = document.getElementById('saveProfileBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';
    
    const profileData = {
        nome: document.getElementById('profileNome').value.trim(),
        telefone: document.getElementById('profileTelefone').value.trim(),
        email: document.getElementById('profileEmail').value.trim() || null,
        endereco_rua: document.getElementById('profileEnderecoRua').value.trim() || null,
        endereco_numero: document.getElementById('profileEnderecoNumero').value.trim() || null,
        endereco_bairro: document.getElementById('profileEnderecoBairro').value.trim() || null,
        endereco_cidade: document.getElementById('profileEnderecoCidade').value.trim() || null,
        endereco_cep: document.getElementById('profileEnderecoCep').value.trim() || null,
    };
    
    console.log('Dados a serem enviados:', profileData);
    
    try {
        const response = await authFetch(`${API_BASE_URL}/app/auth/profile`, {
            method: 'PUT',
            body: JSON.stringify(profileData)
        });
        
        const data = await response.json();
        
        console.log('Resposta do servidor:', data);
        
        if (data.success) {
            appState.user = data.cliente;
            localStorage.setItem('app_user', JSON.stringify(data.cliente));
            
            showAlert('Perfil atualizado com sucesso!', 'success');
            
            // Recarregar perfil para mostrar dados salvos
            setTimeout(() => showProfile(), 1000);
        } else {
            throw new Error(data.message || 'Erro ao atualizar perfil');
        }
    } catch (error) {
        console.error('Erro ao atualizar perfil:', error);
        
        // Verificar erros de validação
        if (error.message.includes('já está sendo usado')) {
            showToast('Este email já está cadastrado', 'error');
        } else if (error.message.includes('mínimo')) {
            showToast('Endereço ou bairro muito curto', 'error');
        } else {
            showToast('Erro ao atualizar perfil. Tente novamente.', 'error');
        }
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

function confirmLogout() {
    if (confirm('Tem certeza que deseja sair da sua conta?')) {
        logout();
    }
}

function showToast(message, type = 'info') {
    // Criar toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} position-fixed top-0 start-50 translate-middle-x mt-3`;
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';
    toast.innerHTML = message;
    
    document.body.appendChild(toast);
    
    // Remover após 3 segundos
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
