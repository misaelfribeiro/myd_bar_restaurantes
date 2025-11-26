// Modal Customizado Profissional

function showRestaurantChangeModal(currentRestaurant, newRestaurant) {
    return new Promise((resolve) => {
        // Criar overlay
        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay-custom';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease-out;
        `;
        
        // Criar modal
        const modal = document.createElement('div');
        modal.className = 'custom-modal';
        modal.style.cssText = `
            background: white;
            border-radius: 16px;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.3s ease-out;
        `;
        
        modal.innerHTML = `
            <div style="background: linear-gradient(135deg, #ff6b6b, #ee5a6f); padding: 24px; text-align: center;">
                <div style="background: rgba(255, 255, 255, 0.2); border-radius: 50%; width: 64px; height: 64px; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-exclamation-triangle" style="color: white; font-size: 32px;"></i>
                </div>
                <h4 style="color: white; margin: 0; font-weight: 600; font-size: 20px;">
                    Trocar de Restaurante?
                </h4>
            </div>
            
            <div style="padding: 24px;">
                <p style="color: #666; margin: 0 0 20px; font-size: 15px; line-height: 1.6;">
                    Seu carrinho possui produtos de:
                </p>
                
                <div style="background: #f8f9fa; border-radius: 12px; padding: 16px; margin-bottom: 16px; border-left: 4px solid #6366f1;">
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <i class="fas fa-store" style="color: #6366f1; margin-right: 10px;"></i>
                        <strong style="color: #2d3748; font-size: 15px;">${currentRestaurant}</strong>
                    </div>
                    <small style="color: #718096;">Restaurante atual</small>
                </div>
                
                <div style="text-align: center; margin: 16px 0;">
                    <i class="fas fa-arrow-down" style="color: #cbd5e0; font-size: 20px;"></i>
                </div>
                
                <div style="background: #f0fdf4; border-radius: 12px; padding: 16px; margin-bottom: 20px; border-left: 4px solid #10b981;">
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <i class="fas fa-store" style="color: #10b981; margin-right: 10px;"></i>
                        <strong style="color: #2d3748; font-size: 15px;">${newRestaurant}</strong>
                    </div>
                    <small style="color: #059669;">Novo restaurante</small>
                </div>
                
                <div style="background: #fff3cd; border-radius: 8px; padding: 12px; margin-bottom: 24px; border-left: 3px solid #ffc107;">
                    <div style="display: flex; align-items: start;">
                        <i class="fas fa-info-circle" style="color: #ff9800; margin-right: 8px; margin-top: 2px;"></i>
                        <small style="color: #856404; line-height: 1.5;">
                            Ao confirmar, todos os produtos do carrinho atual serão removidos.
                        </small>
                    </div>
                </div>
                
                <div style="display: flex; gap: 12px;">
                    <button id="btnCancelChange" style="
                        flex: 1;
                        padding: 14px 20px;
                        border: 2px solid #e2e8f0;
                        background: white;
                        color: #4a5568;
                        border-radius: 10px;
                        font-size: 15px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.2s;
                    ">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button id="btnConfirmChange" style="
                        flex: 1;
                        padding: 14px 20px;
                        border: none;
                        background: linear-gradient(135deg, #10b981, #059669);
                        color: white;
                        border-radius: 10px;
                        font-size: 15px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.2s;
                        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
                    ">
                        <i class="fas fa-check me-2"></i>Confirmar Troca
                    </button>
                </div>
            </div>
        `;
        
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        
        // Adicionar estilos de animação
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            @keyframes slideUp {
                from { 
                    transform: translateY(30px);
                    opacity: 0;
                }
                to { 
                    transform: translateY(0);
                    opacity: 1;
                }
            }
            
            #btnCancelChange:hover {
                background: #f7fafc;
                transform: translateY(-1px);
            }
            
            #btnConfirmChange:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
            }
            
            #btnCancelChange:active,
            #btnConfirmChange:active {
                transform: translateY(0);
            }
        `;
        document.head.appendChild(style);
        
        // Event handlers
        const btnCancel = document.getElementById('btnCancelChange');
        const btnConfirm = document.getElementById('btnConfirmChange');
        
        const closeModal = (result) => {
            overlay.style.animation = 'fadeOut 0.2s ease-out';
            modal.style.animation = 'slideDown 0.2s ease-out';
            
            setTimeout(() => {
                document.body.removeChild(overlay);
                document.head.removeChild(style);
                resolve(result);
            }, 200);
        };
        
        btnCancel.onclick = () => closeModal(false);
        btnConfirm.onclick = () => closeModal(true);
        overlay.onclick = (e) => {
            if (e.target === overlay) closeModal(false);
        };
        
        // Adicionar animação de saída
        const exitStyle = document.createElement('style');
        exitStyle.textContent = `
            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; }
            }
            
            @keyframes slideDown {
                from { 
                    transform: translateY(0);
                    opacity: 1;
                }
                to { 
                    transform: translateY(20px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(exitStyle);
    });
}

// Modal de confirmação genérico
function showConfirmModal(title, message, confirmText = 'OK', cancelText = 'Cancelar') {
    return new Promise((resolve) => {
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        const modal = document.createElement('div');
        modal.style.cssText = `
            background: white;
            border-radius: 16px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transform: scale(0.9) translateY(20px);
            opacity: 0;
            transition: all 0.3s ease;
        `;
        
        modal.innerHTML = `
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; text-align: center;">
                <div style="background: rgba(255, 255, 255, 0.2); border-radius: 50%; width: 56px; height: 56px; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-question-circle" style="color: white; font-size: 28px;"></i>
                </div>
                <h5 style="color: white; margin: 0; font-weight: 600;">${title}</h5>
            </div>
            <div style="padding: 24px;">
                <p style="margin: 0 0 24px; color: #666; font-size: 15px; line-height: 1.6;">${message}</p>
                <div style="display: flex; gap: 12px;">
                    <button class="modal-cancel-btn" style="
                        flex: 1;
                        padding: 12px;
                        border: 2px solid #ddd;
                        background: white;
                        color: #555;
                        border-radius: 8px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.2s;
                    ">${cancelText}</button>
                    <button class="modal-confirm-btn" style="
                        flex: 1;
                        padding: 12px;
                        border: none;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        border-radius: 8px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.2s;
                    ">${confirmText}</button>
                </div>
            </div>
        `;
        
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        
        // Animar entrada
        setTimeout(() => {
            overlay.style.opacity = '1';
            modal.style.transform = 'scale(1) translateY(0)';
            modal.style.opacity = '1';
        }, 10);
        
        const confirmBtn = modal.querySelector('.modal-confirm-btn');
        const cancelBtn = modal.querySelector('.modal-cancel-btn');
        
        confirmBtn.onmouseenter = () => confirmBtn.style.transform = 'translateY(-2px)';
        confirmBtn.onmouseleave = () => confirmBtn.style.transform = 'translateY(0)';
        cancelBtn.onmouseenter = () => cancelBtn.style.borderColor = '#999';
        cancelBtn.onmouseleave = () => cancelBtn.style.borderColor = '#ddd';
        
        const closeModal = (result) => {
            overlay.style.opacity = '0';
            modal.style.transform = 'scale(0.9) translateY(20px)';
            modal.style.opacity = '0';
            
            setTimeout(() => {
                document.body.removeChild(overlay);
                resolve(result);
            }, 300);
        };
        
        confirmBtn.onclick = () => closeModal(true);
        cancelBtn.onclick = () => closeModal(false);
        overlay.onclick = (e) => {
            if (e.target === overlay) closeModal(false);
        };
    });
}
