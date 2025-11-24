# 🔧 Ferramentas de Diagnóstico - Sistema Offline

## 🚀 Links Rápidos

Todas as ferramentas estão em: `http://localhost:8000/`

### 1. 🧪 **Teste SQL.js** 
**URL:** http://localhost:8000/test-sqljs.html

**O que faz:**
- Testa se SQL.js está carregando corretamente
- Executa bateria completa de testes
- Cria banco de teste, insere dados, consulta
- Verifica persistência no localStorage
- **USE PRIMEIRO** para diagnosticar problemas

**Quando usar:**
- ✅ Banco não está carregando
- ✅ Erro "initSqlJs is not defined"
- ✅ Primeira vez configurando o sistema
- ✅ Após atualizar código

---

### 2. 🧹 **Limpar Cache**
**URL:** http://localhost:8000/limpar-cache.html

**O que faz:**
- Mostra uso de armazenamento (KB/MB)
- Lista todos os itens no localStorage
- Limpa banco de dados
- Limpa cache do Service Worker
- Remove Service Worker completamente

**Quando usar:**
- ✅ Sistema travado ou com dados corrompidos
- ✅ Testar do zero
- ✅ Forçar download de arquivos atualizados
- ✅ Erro "QuotaExceededError"

---

### 3. 📊 **Painel de Controle Offline**
**URL:** http://localhost:8000/offline-panel.html

**O que faz:**
- Status da conexão (online/offline)
- Status do banco local (OK/Erro)
- Estatísticas: categorias, produtos, mesas, pedidos
- Log de sincronização com filtros
- Botões de teste: criar pedido, criar pagamento
- Sincronização manual
- Auto-refresh a cada 5 segundos

**Quando usar:**
- ✅ Monitorar sincronização
- ✅ Testar criação de pedidos offline
- ✅ Ver dados no banco local
- ✅ Forçar sincronização com servidor

---

### 4. 🏠 **Sistema Principal**
**URL:** http://localhost:8000

**O que verificar:**
- Console (F12) deve mostrar:
  ```
  📦 Carregando SQL.js...
  ✅ SQL.js disponível, iniciando banco...
  ✅ Banco de dados local pronto!
  ```
- Badge no canto superior direito:
  - 🟢 Online + nº itens pendentes
  - 🔴 Offline
- Clique no badge para sincronizar manualmente

---

## 🔍 Fluxo de Diagnóstico

### Problema: "Banco não carrega"

```
1. Abra: http://localhost:8000/test-sqljs.html
   ├─ ✅ Todos os testes passam → SQL.js OK
   └─ ❌ Erro "initSqlJs not defined" → SQL.js não carregou
       └─ Solução: Verifique internet ou baixe SQL.js localmente

2. Abra: http://localhost:8000/limpar-cache.html
   ├─ Veja uso de armazenamento
   ├─ Clique "Limpar TUDO"
   └─ Recarregue a página

3. Abra: http://localhost:8000
   ├─ Abra Console (F12)
   ├─ Veja mensagens de inicialização
   └─ Teste: window.localDB.isInitialized
       ├─ true → Banco OK ✅
       └─ false → Ainda com problema

4. Abra: http://localhost:8000/offline-panel.html
   ├─ Veja status do banco
   ├─ Clique "🔄 Atualizar Dados"
   └─ Teste criação de pedido
```

---

## 📝 Comandos do Console (F12)

### Verificação Rápida
```javascript
// 1. Verificar SQL.js
typeof initSqlJs !== 'undefined'
// Deve retornar: true

// 2. Verificar banco local
window.localDB?.isInitialized
// Deve retornar: true

// 3. Ver estatísticas
window.localDB.getStats()
// Retorna: {pendentes: 0, sincronizados: 10, erros: 0}

// 4. Contar dados
console.log('Categorias:', window.localDB.getCategorias().length);
console.log('Produtos:', window.localDB.getProdutos().length);
console.log('Mesas:', window.localDB.getMesas().length);
```

### Forçar Inicialização
```javascript
// Limpar e reiniciar
localStorage.removeItem('myd_bar_local.db');
await window.initLocalDatabase();
```

### Sincronização Manual
```javascript
// Baixar dados do servidor
await window.localDB.baixarDadosServidor();

// Enviar dados pendentes
await window.localDB.enviarDadosPendentes();

// Sincronizar tudo (download + upload)
await window.localDB.sincronizarComServidor();
```

### Testar Offline (helpers)
```javascript
// Esperar banco estar pronto
await aguardarBancoLocal();

// Carregar mesas
const mesas = await carregarMesasLocal();
console.table(mesas);

// Carregar produtos de uma categoria
const produtos = await carregarProdutosLocal(1);
console.table(produtos);

// Criar pedido com itens
const pedido = await criarPedidoLocal(1, [
    {produto_id: 1, quantidade: 2, preco_unitario: 10.00, observacoes: 'Sem cebola'}
], 'Pedido de teste');

// Registrar pagamento
const pagamento = await registrarPagamentoLocal(
    pedido.pedidoId,
    'dinheiro',
    50.00,
    50.00,
    0
);
```

---

## 🐛 Erros Comuns

### ❌ "initSqlJs is not defined"
**Causa:** SQL.js não carregou do CDN  
**Solução:** 
1. Verifique internet
2. Teste em http://localhost:8000/test-sqljs.html
3. Baixe SQL.js localmente (veja DIAGNOSTICO_BANCO_LOCAL.md)

### ❌ "Cannot read property 'exec' of undefined"
**Causa:** Banco não inicializou  
**Solução:**
1. Aguarde 2-3 segundos
2. Execute: `await window.initLocalDatabase()`
3. Limpe cache em http://localhost:8000/limpar-cache.html

### ❌ "QuotaExceededError"
**Causa:** localStorage cheio (limite ~10MB)  
**Solução:**
1. Abra http://localhost:8000/limpar-cache.html
2. Veja tamanho dos dados
3. Clique "Limpar Banco de Dados"
4. Sincronize dados antigos e limpe

### ❌ "Failed to fetch wasm"
**Causa:** Arquivo .wasm não encontrado  
**Solução:**
1. Baixe SQL.js localmente
2. Coloque em `/public/libs/`
3. Atualize caminho no código

---

## ✅ Checklist Final

Antes de considerar o sistema funcionando, verifique:

- [ ] ✅ http://localhost:8000/test-sqljs.html → Todos os testes passam
- [ ] ✅ Console mostra "Banco de dados local pronto!"
- [ ] ✅ http://localhost:8000/offline-panel.html → Banco Local = OK
- [ ] ✅ `window.localDB.isInitialized` retorna `true`
- [ ] ✅ Badge de sincronização aparece no sistema
- [ ] ✅ Criar pedido de teste funciona no painel
- [ ] ✅ Sincronização manual funciona
- [ ] ✅ Sistema funciona offline (desconecte internet e teste)
- [ ] ✅ Sincronização automática ao reconectar

---

## 📚 Documentação Completa

- **DIAGNOSTICO_BANCO_LOCAL.md** - Guia completo de diagnóstico
- **BANCO_LOCAL_GUIA.md** - Como usar o banco local no código
- **MODO_OFFLINE_IMPLEMENTACAO.md** - Detalhes de implementação

---

## 🎯 Uso em Produção

### Helpers Disponíveis (garcom-offline-helper.js)

```javascript
// Em qualquer página do sistema:

// 1. Esperar banco estar pronto
await aguardarBancoLocal();

// 2. Carregar dados
const mesas = await carregarMesasLocal();
const produtos = await carregarProdutosLocal(categoriaId);

// 3. Criar pedido
const pedido = await criarPedidoLocal(mesaId, itens, observacoes);

// 4. Registrar pagamento
const pagamento = await registrarPagamentoLocal(
    pedidoId, forma, valor, recebido, troco
);

// 5. Verificar status
const status = await verificarStatus();
console.log(status); // {online: true, bancoOk: true, pendentes: 5}

// 6. Sincronizar manualmente
const result = await sincronizarAgora();

// 7. Mostrar notificação ao usuário
mostrarNotificacao('Pedido criado com sucesso!', 'success');
```

---

**Última atualização:** Sistema com defer, aguardarSqlJs(), retry logic e ferramentas de diagnóstico
