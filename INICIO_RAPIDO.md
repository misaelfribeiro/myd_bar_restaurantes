# 🚀 GUIA RÁPIDO - Sistema Offline em 3 Minutos

## ✅ PASSO 1: Testar SQL.js (30 segundos)

Abra: **http://localhost:8000/teste-simples.html**

**O que deve acontecer:**
- Tela preta com texto verde
- Mensagens de "✅" aparecendo
- Última mensagem: "🎉 TODOS OS TESTES PASSARAM!"

**Se der erro:**
1. Abra Console (F12)
2. Cole e execute:
```javascript
localStorage.clear();
location.reload();
```

---

## ✅ PASSO 2: Inicializar Sistema (1 minuto)

Abra: **http://localhost:8000/inicializar-offline.html**

**Clique em:** "Inicializar Sistema Offline"

**O que deve acontecer:**
- Barra de progresso aparece
- Log mostra criação de categorias, produtos, mesas
- Estatísticas aparecem: 5 categorias, 20 produtos, 10 mesas
- Botões verdes aparecem no final

**Se der erro "Banco não inicializou":**
1. Recarregue a página (F5)
2. Abra Console (F12)
3. Veja a mensagem de erro
4. Execute:
```javascript
await initLocalDatabase();
```

---

## ✅ PASSO 3: Usar o Caixa (1 minuto)

Abra: **http://localhost:8000/caixa-offline.html**

**Passos:**
1. **Veja mesas** na coluna esquerda
   - Verde = Livre
   - Amarelo = Ocupada (tem pedidos)

2. **Clique em "Mesa 01"** (deve estar amarela/ocupada)
   - Pedidos aparecem na coluna central

3. **Clique no pedido** (R$ 82,00)
   - Itens aparecem na coluna direita
   - Total: R$ 82,00

4. **Clique em "Fechar Conta"**
   - Modal abre

5. **Escolha forma de pagamento** (ex: Dinheiro)
   - Valor recebido: 100
   - Troco calculado automaticamente: 18,00

6. **Clique em "Confirmar Pagamento"**
   - Mesa fica verde (livre)
   - Pedido finalizado

---

## 🧪 TESTE OFFLINE

1. Pressione **F12** (abrir DevTools)
2. Vá na aba **Network** (Rede)
3. Mude para **Offline**
4. Use o sistema normalmente
5. Feche mais contas
6. Volte para **Online**
7. Sistema sincroniza automaticamente

---

## 📱 URLs Importantes

```
TESTAR SQL.js:
http://localhost:8000/teste-simples.html

INICIALIZAR:
http://localhost:8000/inicializar-offline.html

MENU PRINCIPAL:
http://localhost:8000/index-offline.html

CAIXA (USAR):
http://localhost:8000/caixa-offline.html

PAINEL:
http://localhost:8000/offline-panel.html

LIMPAR:
http://localhost:8000/limpar-cache.html
```

---

## 🐛 Problemas Comuns

### ❌ "initSqlJs is not defined"

**Solução:**
```
1. Abra: http://localhost:8000/teste-simples.html
2. Aguarde 5 segundos
3. Se continuar erro, arquivos SQL.js não foram baixados
4. Execute no PowerShell:
   cd c:\xampp\htdocs\myd_bar_restaurantes
   .\baixar-sqljs.ps1
```

### ❌ "Banco não inicializou"

**Solução:**
```
1. Abra Console (F12)
2. Execute:
   localStorage.clear();
   location.reload();
3. Aguarde 10 segundos
4. Tente novamente
```

### ❌ "Nenhuma mesa encontrada"

**Solução:**
```
1. Acesse: http://localhost:8000/inicializar-offline.html
2. Clique em "Inicializar Sistema Offline"
3. Aguarde completar
```

### ❌ Página não carrega

**Solução:**
```
1. Verifique se servidor está rodando:
   cd c:\xampp\htdocs\myd_bar_restaurantes
   php artisan serve

2. Acesse: http://localhost:8000
```

---

## 🎯 Verificação Rápida

Execute no Console (F12):

```javascript
// 1. Verificar se SQL.js carregou
typeof initSqlJs !== 'undefined'
// Deve retornar: true

// 2. Verificar se banco está pronto
window.localDB?.isInitialized
// Deve retornar: true

// 3. Ver quantos produtos tem
window.localDB?.getProdutos().length
// Deve retornar: 20 (ou outro número)

// 4. Ver mesas
window.localDB?.getMesas()
// Deve retornar array com mesas
```

---

## ✅ Checklist de Sucesso

Marque conforme conseguir:

- [ ] Acessei http://localhost:8000/teste-simples.html
- [ ] Vi "🎉 TODOS OS TESTES PASSARAM!" em verde
- [ ] Acessei http://localhost:8000/inicializar-offline.html
- [ ] Cliquei em "Inicializar Sistema Offline"
- [ ] Vi "5 Categorias, 20 Produtos, 10 Mesas"
- [ ] Acessei http://localhost:8000/caixa-offline.html
- [ ] Vi 10 mesas na coluna esquerda
- [ ] Cliquei em Mesa 01 (amarela)
- [ ] Vi pedido de R$ 82,00
- [ ] Cliquei no pedido
- [ ] Vi 3 itens: Coca-Cola, Batata Frita, Feijoada
- [ ] Cliquei em "Fechar Conta"
- [ ] Escolhi forma de pagamento
- [ ] Confirmei pagamento
- [ ] Mesa 01 ficou verde
- [ ] Testei com internet desconectada (F12 → Network → Offline)
- [ ] Sistema continuou funcionando! 🎉

---

## 🎓 Como Funciona

```
1. SQL.js carrega do disco (/libs/sql-wasm.js)
   ↓
2. Cria banco SQLite na memória do navegador
   ↓
3. Cria tabelas (categorias, produtos, mesas, pedidos, etc)
   ↓
4. Salva banco no localStorage (~10MB limite)
   ↓
5. Quando faz ação (fechar conta):
   - Atualiza banco SQLite
   - Salva no localStorage
   - Se ONLINE: sincroniza com servidor
   - Se OFFLINE: marca para sincronizar depois
```

---

## 🚀 Comece AGORA

**1º:** http://localhost:8000/teste-simples.html (testar)  
**2º:** http://localhost:8000/inicializar-offline.html (criar dados)  
**3º:** http://localhost:8000/caixa-offline.html (usar!)

---

**Tempo total: ~3 minutos** ⏱️
