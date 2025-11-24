<!-- 🚀 ENDPOINTS DE API DISPONÍVEIS -->
<div class="section-card" style="grid-column: 1 / -1; margin-top: 30px;">
 <div class="section-header">
 <h3 class="section-title">
 <i class="fas fa-code"></i>
 APIs Disponíveis
 </h3>
 <button class="quick-btn" onclick="toggleApiEndpoints()">
 <i class="fas fa-chevron-down" id="endpoints-toggle"></i>
 </button>
 </div>
 <div class="section-content" id="apiEndpointsContent" style="display: none;">
 <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
 <!-- Categorias -->
 <div class="api-group">
 <h4 style="color: #333; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #667eea; display: flex; align-items: center;">
 <i class="fas fa-tags" style="margin-right: 8px;"></i>
 Categorias
 </h4>
 <div class="api-endpoint">
 <span class="method get">GET</span> /api/categorias
 </div>
 <div class="api-endpoint">
 <span class="method post">POST</span> /api/categorias
 </div>
 <div class="api-endpoint">
 <span class="method put">PUT</span> /api/categorias/{id}
 </div>
 <div class="api-endpoint">
 <span class="method delete">DEL</span> /api/categorias/{id}
 </div>
 </div>
 <!-- Produtos -->
 <div class="api-group">
 <h4 style="color: #333; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #ff6b35; display: flex; align-items: center;">
 <i class="fas fa-box" style="margin-right: 8px;"></i>
 Produtos
 </h4>
 <div class="api-endpoint">
 <span class="method get">GET</span> /api/produtos
 </div>
 <div class="api-endpoint">
 <span class="method post">POST</span> /api/produtos
 </div>
 <div class="api-endpoint">
 <span class="method put">PUT</span> /api/produtos/{id}
 </div>
 <div class="api-endpoint">
 <span class="method delete">DEL</span> /api/produtos/{id}
 </div>
 </div>
 <!-- Mesas -->
 <div class="api-group">
 <h4 style="color: #333; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #20c997; display: flex; align-items: center;">
 <i class="fas fa-chair" style="margin-right: 8px;"></i>
 Mesas
 </h4>
 <div class="api-endpoint">
 <span class="method get">GET</span> /api/mesas
 </div>
 <div class="api-endpoint">
 <span class="method post">POST</span> /api/mesas
 </div>
 <div class="api-endpoint">
 <span class="method put">PUT</span> /api/mesas/{id}
 </div>
 <div class="api-endpoint">
 <span class="method delete">DEL</span> /api/mesas/{id}
 </div>
 </div>
 <!-- Usuários -->
 <div class="api-group">
 <h4 style="color: #333; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #dc3545; display: flex; align-items: center;">
 <i class="fas fa-users" style="margin-right: 8px;"></i>
 Usuários
 </h4>
 <div class="api-endpoint">
 <span class="method get">GET</span> /api/usuarios
 </div>
 <div class="api-endpoint">
 <span class="method post">POST</span> /api/usuarios
 </div>
 <div class="api-endpoint">
 <span class="method put">PUT</span> /api/usuarios/{id}
 </div>
 <div class="api-endpoint">
 <span class="method delete">DEL</span> /api/usuarios/{id}
 </div>
 </div>
 <!-- Pedidos -->
 <div class="api-group">
 <h4 style="color: #333; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #007bff; display: flex; align-items: center;">
 <i class="fas fa-receipt" style="margin-right: 8px;"></i>
 Pedidos
 </h4>
 <div class="api-endpoint">
 <span class="method get">GET</span> /api/pedidos
 </div>
 <div class="api-endpoint">
 <span class="method post">POST</span> /api/pedidos
 </div>
 <div class="api-endpoint">
 <span class="method put">PUT</span> /api/pedidos/{id}
 </div>
 <div class="api-endpoint">
 <span class="method delete">DEL</span> /api/pedidos/{id}
 </div>
 </div>
 <!-- Dashboard -->
 <div class="api-group">
 <h4 style="color: #333; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #6f42c1; display: flex; align-items: center;">
 <i class="fas fa-chart-bar" style="margin-right: 8px;"></i>
 Dashboard
 </h4>
 <div class="api-endpoint">
 <span class="method get">GET</span> /api/dashboard/stats
 </div>
 <div class="api-endpoint">
 <span class="method get">GET</span> /api/dashboard/pedidos-status
 </div>
 <div class="api-endpoint">
 <span class="method get">GET</span> /api/dashboard/produtos-vendidos
 </div>
 </div>
 </div>
 </div>
</div>
<style>
.api-endpoint {
 background: #f8f9fa;
 padding: 8px 12px;
 margin: 6px 0;
 border-radius: 6px;
 border-left: 3px solid #667eea;
 font-family: 'Courier New', monospace;
 font-size: 0.85rem;
 transition: all 0.2s ease;
}
.api-endpoint:hover {
 background: #e9ecef;
 transform: translateX(2px);
}
.method {
 font-weight: bold;
 padding: 2px 6px;
 border-radius: 3px;
 color: white;
 margin-right: 8px;
 font-size: 0.75rem;
}
.method.get { background-color: #28a745; }
.method.post { background-color: #007bff; }
.method.put { background-color: #ffc107; color: #333; }
.method.delete { background-color: #dc3545; }
.api-group {
 background: white;
 padding: 15px;
 border-radius: 8px;
 border: 1px solid #e9ecef;
}
</style>
<script>
function toggleApiEndpoints() {
 const content = document.getElementById('apiEndpointsContent');
 const icon = document.getElementById('endpoints-toggle');
 if (content.style.display === 'none') {
 content.style.display = 'block';
 icon.className = 'fas fa-chevron-up';
 } else {
 content.style.display = 'none';
 icon.className = 'fas fa-chevron-down';
 }
}
</script>