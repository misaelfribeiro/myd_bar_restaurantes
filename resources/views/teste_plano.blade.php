<!DOCTYPE html>
<html>
<head>
 <title>Teste Alterar Plano</title>
 <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
 <h1>Teste de Alteração de Plano</h1>
 <form id="testForm">
 <label>Empresa ID:</label>
 <input type="number" id="empresa_id" value="2" /><br><br>
 <label>Plano:</label>
 <select id="plano">
 <option value="basico">Básico</option>
 <option value="profissional" selected>Profissional</option>
 <option value="premium">Premium</option>
 <option value="enterprise">Enterprise</option>
 </select><br><br>
 <label>Valor Mensalidade:</label>
 <input type="number" id="valor_mensalidade" value="199.90" step="0.01" /><br><br>
 <label>Max Usuários:</label>
 <input type="number" id="max_usuarios" value="15" /><br><br>
 <label>Max Produtos:</label>
 <input type="number" id="max_produtos" value="500" /><br><br>
 <label>Max Pedidos/Mês:</label>
 <input type="number" id="max_pedidos_mes" value="2000" /><br><br>
 <label>Max Filiais:</label>
 <input type="number" id="max_filiais" value="3" /><br><br>
 <button type="button" onclick="testarAlteracao()">Testar Alteração</button>
 </form>
 <div id="resultado" style="margin-top: 20px; padding: 10px; border: 1px solid #ccc;"></div>
 <script>
 async function testarAlteracao() {
 const empresaId = document.getElementById('empresa_id').value;
 const resultado = document.getElementById('resultado');
 const data = {
 plano: document.getElementById('plano').value,
 valor_mensalidade: parseFloat(document.getElementById('valor_mensalidade').value),
 max_usuarios: parseInt(document.getElementById('max_usuarios').value),
 max_produtos: parseInt(document.getElementById('max_produtos').value),
 max_pedidos_mes: parseInt(document.getElementById('max_pedidos_mes').value),
 max_filiais: parseInt(document.getElementById('max_filiais').value)
 };
 resultado.innerHTML = '<strong>Enviando...</strong><br>' + JSON.stringify(data, null, 2);
 try {
 const response = await fetch(`/admin/planos/change/${empresaId}`, {
 method: 'POST',
 headers: {
 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
 'Accept': 'application/json',
 'Content-Type': 'application/json'
 },
 body: JSON.stringify(data)
 });
 const result = await response.json();
 resultado.innerHTML = '<strong>Status: ' + response.status + '</strong><br>' +
 '<strong>Resposta:</strong><br>' +
 '<pre>' + JSON.stringify(result, null, 2) + '</pre>';
 if (response.ok && result.success) {
 resultado.style.backgroundColor = '#d4edda';
 resultado.style.color = '#155724';
 } else {
 resultado.style.backgroundColor = '#f8d7da';
 resultado.style.color = '#721c24';
 }
 } catch (error) {
 resultado.innerHTML = '<strong>Erro:</strong><br>' + error.message;
 resultado.style.backgroundColor = '#f8d7da';
 resultado.style.color = '#721c24';
 }
 }
 </script>
</body>
</html>