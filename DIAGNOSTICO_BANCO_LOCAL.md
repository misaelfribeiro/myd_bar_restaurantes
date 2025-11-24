# 🔍 Guia de Diagnóstico - Banco Local

## Problema: "banco não carrega"

### ✅ Solução Implementada

Foram feitas melhorias na inicialização do banco de dados local:

1. **Atributo `defer` nos scripts** - garante que SQL.js carregue antes
2. **Função `aguardarSqlJs()`** - espera até 10 segundos pelo SQL.js
3. **Retry logic** - tenta inicializar múltiplas vezes
4. **Logs detalhados** - mostra cada passo no console

---

## 🧪 Como Testar

### Teste 1: Página de Diagnóstico Dedicada

Acesse: **http://localhost:8000/test-sqljs.html**

Esta página testa **isoladamente** o SQL.js:

```
✅ Verifica se SQL.js carregou
✅ Cria banco de dados de teste
✅ Cria tabela e insere dados
✅ Consulta e exibe resultados
✅ Testa localStorage
✅ Verifica seu banco local (se existir)
```

**Passos:**
1. Abra a URL acima
2. Clique em "🔄 Testar SQL.js"
3. Aguarde os testes (15-20 segundos)
4. Veja se TODOS os testes passam ✅

**Se aparecer erro "initSqlJs não está definido":**
- ❌ Problema: SQL.js não está carregando do CDN
- ✅ Solução: Verifique sua conexão com internet
- ✅ Alternativa: Baixe SQL.js localmente (veja seção abaixo)

---

### Teste 2: Sistema Principal

Acesse: **http://localhost:8000**

Abra o **Console do Navegador** (F12 → Console) e procure por:

```javascript
// Mensagens esperadas:
📦 Carregando SQL.js...
✅ SQL.js disponível, iniciando banco...
📊 Criando estrutura do banco de dados...
✅ Banco de dados local pronto!
```

**Se aparecer:**
```
❌ SQL.js não carregou após 5 segundos
```

Execute manualmente no console:
```javascript
window.initLocalDatabase()
```

---

### Teste 3: Painel de Controle

Acesse: **http://localhost:8000/offline-panel.html**

Este painel mostra:
- 🟢 Status da conexão (online/offline)
- 💾 Status do banco local (OK/Erro)
- 📊 Estatísticas (produtos, mesas, pedidos)
- 📝 Log de sincronização
- 🧪 Botões de teste

**Passos:**
1. Abra a URL
2. Verifique se o card "💾 Banco Local" mostra "OK"
3. Se mostrar "Erro", clique em "🔄 Atualizar Dados"
4. Veja o log para detalhes

---

## 🛠️ Correções Aplicadas

### 1. Scripts com `defer`

**Arquivo:** `resources/views/layouts/app.blade.php`

```html
<!-- ANTES (carregava simultaneamente) -->
<script src="https://sql.js.org/dist/sql-wasm.js"></script>
<script src="{{ asset('js/local-database.js') }}"></script>

<!-- DEPOIS (defer garante ordem) -->
<script src="https://sql.js.org/dist/sql-wasm.js" defer></script>
<script src="{{ asset('js/local-database.js') }}" defer></script>
```

### 2. Função `aguardarSqlJs()`

**Arquivo:** `public/js/local-database.js`

```javascript
async aguardarSqlJs() {
    return new Promise((resolve, reject) => {
        let tentativas = 0;
        const maxTentativas = 100; // 10 segundos (100 * 100ms)
        
        const verificar = setInterval(() => {
            tentativas++;
            if (typeof initSqlJs !== 'undefined') {
                clearInterval(verificar);
                resolve();
            } else if (tentativas >= maxTentativas) {
                clearInterval(verificar);
                reject(new Error('SQL.js não carregou após 10 segundos'));
            }
        }, 100);
    });
}
```

### 3. Inicialização com Retry

**Arquivo:** `public/js/local-database.js` (função `initLocalDatabase`)

```javascript
// Espera documento carregar
await new Promise(resolve => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', resolve);
    } else {
        resolve();
    }
});

// Espera SQL.js (até 5 segundos)
let sqlJsDisponivel = false;
for (let i = 0; i < 50; i++) {
    if (typeof initSqlJs !== 'undefined') {
        sqlJsDisponivel = true;
        break;
    }
    await new Promise(r => setTimeout(r, 100));
}

if (!sqlJsDisponivel) {
    console.error('❌ SQL.js não carregou após 5 segundos');
    return;
}

// Inicializa o banco
window.localDB = new LocalDatabaseManager();
await window.localDB.init();
```

---

## 🌐 Baixar SQL.js Localmente (Offline Completo)

Se quiser que o sistema funcione **sem internet nenhuma**:

### Passo 1: Baixar SQL.js

```powershell
# No terminal do projeto
cd public
mkdir libs
cd libs

# Baixar arquivos
curl -o sql-wasm.js https://sql.js.org/dist/sql-wasm.js
curl -o sql-wasm.wasm https://sql.js.org/dist/sql-wasm.wasm
```

### Passo 2: Atualizar app.blade.php

```html
<!-- TROCAR: -->
<script src="https://sql.js.org/dist/sql-wasm.js" defer></script>

<!-- POR: -->
<script src="{{ asset('libs/sql-wasm.js') }}" defer></script>
```

### Passo 3: Atualizar local-database.js

```javascript
// Trocar no método init():
const SQL = await initSqlJs({
    locateFile: file => `https://sql.js.org/dist/${file}` // REMOVER
});

// POR:
const SQL = await initSqlJs({
    locateFile: file => `/libs/${file}` // USAR LOCAL
});
```

---

## 📊 Verificação Rápida (Console)

Cole no console do navegador (F12 → Console):

```javascript
// Verificar se SQL.js está disponível
console.log('SQL.js:', typeof initSqlJs !== 'undefined' ? '✅ OK' : '❌ NÃO CARREGADO');

// Verificar se banco local está pronto
console.log('Banco Local:', window.localDB?.isInitialized ? '✅ OK' : '❌ NÃO INICIALIZADO');

// Ver estatísticas (se banco estiver OK)
if (window.localDB?.isInitialized) {
    const stats = window.localDB.getStats();
    console.table({
        'Pendentes': stats.pendentes,
        'Sincronizados': stats.sincronizados,
        'Com Erro': stats.erros
    });
}

// Contar registros no banco
if (window.localDB?.isInitialized) {
    const categorias = window.localDB.getCategorias();
    const produtos = window.localDB.getProdutos();
    const mesas = window.localDB.getMesas();
    
    console.table({
        'Categorias': categorias.length,
        'Produtos': produtos.length,
        'Mesas': mesas.length
    });
}
```

---

## 🐛 Possíveis Erros

### Erro 1: "initSqlJs is not defined"

**Causa:** Script SQL.js não carregou

**Soluções:**
1. Verifique conexão com internet
2. Teste em http://localhost:8000/test-sqljs.html
3. Use SQL.js local (veja seção "Baixar SQL.js Localmente")
4. Verifique se CDN está acessível: https://sql.js.org/dist/sql-wasm.js

### Erro 2: "Cannot read property 'exec' of undefined"

**Causa:** Banco não foi inicializado

**Soluções:**
1. Aguarde inicialização (2-3 segundos)
2. Execute: `await window.initLocalDatabase()`
3. Limpe cache: `localStorage.removeItem('myd_bar_local.db')`
4. Recarregue a página (F5)

### Erro 3: "QuotaExceededError"

**Causa:** localStorage está cheio (limite ~10MB)

**Soluções:**
1. Limpe dados: `localStorage.clear()`
2. Use apenas dados essenciais
3. Sincronize e limpe dados antigos
4. Considere IndexedDB para mais espaço

### Erro 4: "Failed to fetch wasm"

**Causa:** Arquivo .wasm não encontrado

**Soluções:**
1. Baixe SQL.js localmente
2. Verifique `locateFile` no código
3. Confira caminho: `/libs/sql-wasm.wasm`

---

## ✅ Checklist de Sucesso

Marque cada item ao testar:

- [ ] http://localhost:8000/test-sqljs.html → Todos os testes passam ✅
- [ ] Console mostra "✅ Banco de dados local pronto!"
- [ ] http://localhost:8000/offline-panel.html → Banco Local = OK
- [ ] `window.localDB.isInitialized` retorna `true`
- [ ] `window.localDB.getCategorias()` retorna array
- [ ] Badge de sincronização aparece no sistema
- [ ] Criar pedido local funciona (teste no painel)
- [ ] Sincronização manual funciona (botão no painel)

---

## 🎯 Próximos Passos

Quando todos os testes passarem:

1. **Sincronizar dados** → `window.localDB.sincronizarComServidor()`
2. **Testar offline** → Desconecte internet e use o sistema
3. **Testar sincronização** → Reconecte e veja dados subirem
4. **Criar pedidos offline** → Use painel de controle para testar
5. **Integrar com páginas** → Use helper functions em garcom-offline-helper.js

---

## 📞 Comandos Úteis (Console)

```javascript
// Forçar reinicialização
localStorage.removeItem('myd_bar_local.db');
await window.initLocalDatabase();

// Ver tamanho do banco
const db = localStorage.getItem('myd_bar_local.db');
console.log(`Tamanho: ${(db?.length || 0 / 1024).toFixed(2)} KB`);

// Sincronizar agora
const result = await window.localDB.sincronizarComServidor();
console.log(result);

// Baixar dados do servidor
const download = await window.localDB.baixarDadosServidor();
console.log(download);

// Ver log de sincronização
const log = window.localDB.query('SELECT * FROM sync_log ORDER BY created_at DESC LIMIT 20');
console.table(log);
```

---

## 📝 Logs Esperados (Console)

### Inicialização Bem-Sucedida:

```
📦 Aguardando documento carregar...
📦 Documento carregado
📦 Aguardando SQL.js... (tentativa 1/50)
✅ SQL.js disponível!
📦 Criando instância do banco...
📦 Inicializando banco...
📊 Criando estrutura do banco de dados...
✅ Tabela categorias criada
✅ Tabela produtos criada
✅ Tabela mesas criada
✅ Tabela pedidos criada
✅ Tabela itens_pedido criada
✅ Tabela pagamentos criada
✅ Tabela sync_log criada
✅ Banco de dados local pronto!
✅ Banco local inicializado e disponível globalmente como window.localDB
```

### Se SQL.js Demorar:

```
📦 Aguardando SQL.js... (tentativa 1/50)
📦 Aguardando SQL.js... (tentativa 2/50)
📦 Aguardando SQL.js... (tentativa 3/50)
...
✅ SQL.js disponível! (após 15 tentativas)
```

### Se Falhar:

```
❌ SQL.js não carregou após 5 segundos
Tente executar manualmente: window.initLocalDatabase()
```

---

## 🎓 Como Funciona

### Fluxo de Inicialização:

```
1. Página HTML carrega
   ↓
2. Script SQL.js carrega (defer)
   ↓
3. Script local-database.js carrega (defer)
   ↓
4. Função initLocalDatabase() executa
   ↓
5. Espera documento estar pronto (DOMContentLoaded)
   ↓
6. Espera SQL.js estar disponível (até 5 segundos)
   ↓
7. Cria instância LocalDatabaseManager
   ↓
8. Chama init() que:
   - Espera SQL.js (até 10 segundos)
   - Inicializa biblioteca SQL.js
   - Verifica localStorage
   - Carrega ou cria banco
   - Cria tabelas
   ↓
9. Banco pronto! ✅
```

---

## 📚 Referências

- **SQL.js**: https://sql.js.org/
- **LocalStorage**: https://developer.mozilla.org/en-US/docs/Web/API/Window/localStorage
- **Service Worker**: https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API

---

**Última atualização:** Sistema corrigido com `defer`, `aguardarSqlJs()` e retry logic
