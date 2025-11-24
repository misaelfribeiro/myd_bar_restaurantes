# 🎯 Sistema de Ações da Carla - Arquitetura

## 🔄 Fluxo Completo

```
┌─────────────────────────────────────────────────────────────────┐
│                         USUÁRIO                                  │
│                  "rastrear minha entrega"                        │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                    VOICE ASSISTANT                               │
│              (public/js/voice-assistant.js)                      │
│                                                                  │
│  • Captura voz/texto                                            │
│  • Envia para API: POST /api/ai/process                        │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                      LARAVEL API                                 │
│             (app/Http/Controllers/Api/AIController.php)          │
│                                                                  │
│  • Recebe: { "message": "rastrear minha entrega" }             │
│  • Chama: AILearningService->processMessage()                  │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                    AI LEARNING SERVICE                           │
│               (app/Services/AILearningService.php)               │
│                                                                  │
│  1. Converte texto em vetor (textToVector)                     │
│  2. Forward propagation (rede neural)                           │
│  3. Busca contextos matching (findMatchingContexts)             │
│  4. Calcula confiança (calculateConfidence)                     │
│  5. Seleciona melhor resposta (selectBestResponse)              │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                      AI CONTEXTS                                 │
│                 (database: ai_contexts)                          │
│                                                                  │
│  • Padrão: *(rastrear)*(entrega)*                              │
│  • Match encontrado! ✅                                         │
│  • Retorna:                                                     │
│    - response: "Vou verificar sua entrega!"                    │
│    - action: "trackDelivery"                                    │
│    - confidence: 0.85                                           │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                    RESPOSTA DA API                               │
│                                                                  │
│  {                                                              │
│    "response": "Vou verificar sua entrega!",                   │
│    "action": "trackDelivery",                                   │
│    "confidence": 0.85,                                          │
│    "intent": "track_delivery"                                   │
│  }                                                              │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                   VOICE ASSISTANT                                │
│                 (handleAction function)                          │
│                                                                  │
│  switch(action) {                                               │
│    case 'trackDelivery':                                        │
│      window.location.href = '/rastrear-entrega';               │
│      break;                                                     │
│  }                                                              │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                         APP                                      │
│                 Navega para tela de rastreamento                │
│                 Usuário vê status da entrega 🚚                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🗂️ Estrutura de Arquivos

### 1. **Frontend** (JavaScript)
```
public/js/voice-assistant.js
├── startVoiceRecognition()     → Captura voz
├── sendToAI(message)           → Envia para API
├── handleResponse(response)    → Processa resposta
└── handleAction(action, params) → EXECUTA AÇÃO
    ├── searchProduct
    ├── showMenu
    ├── showCart
    ├── trackDelivery       ← NOVA AÇÃO
    ├── showPromotions      ← NOVA AÇÃO
    └── ... (20+ ações)
```

### 2. **Backend** (Laravel)
```
app/
├── Http/Controllers/Api/
│   └── AIController.php
│       ├── process()           → Endpoint principal
│       ├── feedback()          → Aprendizado
│       ├── contexts()          → Lista contextos
│       └── train()             → Treina rede
│
├── Services/
│   └── AILearningService.php
│       ├── processMessage()    → Pipeline completo
│       ├── forwardPropagation() → Rede neural
│       ├── findMatchingContexts() → Busca padrões
│       └── calculateConfidence() → Calcula certeza
│
└── Models/
    ├── AIContext.php           → Armazena ações
    ├── AINeuron.php           → Rede neural
    ├── AISynapse.php          → Conexões
    └── AITrainingData.php     → Histórico
```

### 3. **Database**
```
ai_contexts (tabela principal de ações)
├── id
├── category         → "delivery", "menu", "cart"...
├── key             → "track_delivery" (único)
├── pattern         → "*(rastrear)*(entrega)*" (regex)
├── response_template → "Vou verificar sua entrega!"
├── action          → "trackDelivery" ← EXECUTADA NO APP
├── parameters      → {"extra": "data"}
├── confidence_threshold → 0.7
├── usage_count     → Quantas vezes foi usada
├── success_rate    → Taxa de sucesso
└── active          → true/false
```

---

## 🎯 Como Adicionar Nova Ação (Passo a Passo)

### Exemplo: Adicionar ação "Repetir Pedido"

#### **Passo 1: Frontend** (`voice-assistant.js`)
```javascript
// Adicione no switch da função handleAction():

case 'repeatOrder':
    // Busca último pedido e adiciona no carrinho
    if (confirm('Deseja repetir seu último pedido?')) {
        fetch('/api/pedidos/repetir-ultimo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Pedido repetido com sucesso!');
                window.location.href = '/carrinho';
            } else {
                alert('❌ Erro ao repetir pedido');
            }
        });
    }
    break;
```

#### **Passo 2: Backend** (criar endpoint se necessário)
```php
// routes/api.php
Route::post('/pedidos/repetir-ultimo', [PedidoController::class, 'repetirUltimo']);

// app/Http/Controllers/PedidoController.php
public function repetirUltimo(Request $request)
{
    $userId = auth()->id();
    
    // Busca último pedido
    $ultimoPedido = Pedido::where('usuario_id', $userId)
        ->orderBy('created_at', 'desc')
        ->first();
    
    if (!$ultimoPedido) {
        return response()->json(['success' => false, 'message' => 'Nenhum pedido encontrado']);
    }
    
    // Adiciona itens no carrinho
    foreach ($ultimoPedido->itens as $item) {
        // Lógica para adicionar no carrinho
        CarrinhoService::adicionar($item->produto_id, $item->quantidade);
    }
    
    return response()->json(['success' => true]);
}
```

#### **Passo 3: Criar Contexto** (3 formas)

**Forma A: Via Painel Admin**
```
1. Acesse: http://localhost:8000/admin/carla
2. Clique: "Adicionar Contexto"
3. Preencha:
   - Categoria: orders
   - Key: repeat_order
   - Padrão: *(repetir|de novo)*(pedido)*
   - Resposta: Sou a Carla da EatsFood! Vou repetir seu último pedido!
   - Ação: repeatOrder ← IMPORTANTE
   - Limiar: 0.7
4. Salvar
```

**Forma B: Via Script PHP**
```php
<?php
require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\AIContext;

AIContext::create([
    'category' => 'orders',
    'key' => 'repeat_order',
    'pattern' => '*(repetir|de novo|novamente)*(pedido|último|anterior)*',
    'response_template' => 'Sou a Carla da EatsFood! Vou repetir seu último pedido! 🔄',
    'action' => 'repeatOrder',
    'confidence_threshold' => 0.7,
    'active' => true
]);

echo "✅ Ação 'repeatOrder' adicionada!\n";
```

Execute: `php adicionar_repeat_order.php`

**Forma C: Usar Script Pronto**
```bash
php adicionar_acoes_novas.php
```
(Já inclui 20 ações prontas, incluindo repeatOrder!)

#### **Passo 4: Testar**
```
1. Abra o app
2. Clique no microfone 🎤
3. Diga: "repetir meu pedido"
4. Carla responde: "Vou repetir seu último pedido!"
5. App executa a ação automaticamente
6. Usuário é levado ao carrinho com itens
```

---

## 📊 Mapeamento Completo de Ações

| Ação | Categoria | O que faz | Implementado |
|------|-----------|-----------|--------------|
| `searchProduct` | search | Busca produto por nome | ✅ Padrão |
| `showMenu` | menu | Abre cardápio completo | ✅ Padrão |
| `showCart` | cart | Mostra carrinho de compras | ✅ Padrão |
| `showOrders` | orders | Lista pedidos do usuário | ✅ Padrão |
| `checkout` | cart | Vai para finalização | ✅ Padrão |
| `trackDelivery` | delivery | Rastreia entrega em andamento | 🆕 Novo |
| `showPromotions` | menu | Mostra promoções ativas | 🆕 Novo |
| `filterByCategory` | search | Filtra por categoria (bebidas, lanches...) | 🆕 Novo |
| `repeatOrder` | orders | Repete último pedido | 🆕 Novo |
| `applyDiscount` | cart | Aplica cupom de desconto | 🆕 Novo |
| `changeAddress` | delivery | Muda endereço de entrega | 🆕 Novo |
| `changePayment` | payment | Muda forma de pagamento | 🆕 Novo |
| `contactSupport` | help | Abre chat/WhatsApp suporte | 🆕 Novo |
| `showProfile` | account | Abre perfil do usuário | 🆕 Novo |
| `showFavorites` | menu | Mostra produtos favoritos | 🆕 Novo |
| `scheduleOrder` | orders | Agenda pedido para depois | 🆕 Novo |
| `cancelOrder` | orders | Cancela pedido em andamento | 🆕 Novo |
| `addToCart` | cart | Adiciona produto direto no carrinho | 🆕 Novo |
| `removeFromCart` | cart | Remove produto do carrinho | 🆕 Novo |
| `showReviews` | info | Mostra avaliações de produto | 🆕 Novo |

---

## 🎓 Exemplo Completo: Ação Complexa com Parâmetros

### Ação: Buscar Pizza Margherita

#### 1. **Contexto no Banco**
```json
{
  "key": "search_pizza_margherita",
  "pattern": "*(quero|buscar)*(pizza)*(margherita|margheritha)*",
  "action": "searchProduct",
  "parameters": {
    "product": "pizza margherita",
    "category": "pizzas"
  }
}
```

#### 2. **Carla Extrai Parâmetros**
```php
// AIContext->matches() retorna:
[
  'match' => true,
  'confidence' => 0.9,
  'parameters' => [
    'product' => 'pizza margherita',
    'category' => 'pizzas'
  ]
]
```

#### 3. **API Retorna**
```json
{
  "response": "Encontrei Pizza Margherita!",
  "action": "searchProduct",
  "parameters": {
    "product": "pizza margherita",
    "category": "pizzas"
  }
}
```

#### 4. **Frontend Executa**
```javascript
case 'searchProduct':
    if (parameters.product) {
        // Busca específica
        searchProduct(parameters.product);
        
        // Se tiver categoria, filtra também
        if (parameters.category) {
            filterByCategory(parameters.category);
        }
    }
    break;

function searchProduct(term) {
    window.location.href = `/buscar?q=${encodeURIComponent(term)}`;
}
```

---

## 🚀 Script Pronto - 20 Ações

Execute agora:
```bash
php adicionar_acoes_novas.php
```

**Adiciona:**
- ✅ 4 ações de delivery (rastrear, tempo, endereço)
- ✅ 3 ações de promoções (ver, cupom, primeira compra)
- ✅ 3 ações de filtros (bebidas, sobremesas, lanches)
- ✅ 3 ações de pedidos (repetir, cancelar, agendar)
- ✅ 3 ações de pagamento (formas, PIX, mudar)
- ✅ 2 ações de suporte (contato, problema)
- ✅ 3 ações de perfil (ver, favoritos, avaliações)

**Total: 20 novas ações** → Carla passa de 5 para 25+ ações! 🎉

---

## 📈 Monitoramento no Painel Admin

Acesse: `http://localhost:8000/admin/carla`

**Métricas para cada ação:**
- **Uso**: Quantas vezes foi ativada
- **Taxa de Sucesso**: % de vezes que funcionou
- **Confiança Média**: Quão segura a Carla está
- **Padrão**: Regex que ativa a ação

**Exemplo:**
```
Ação: trackDelivery
Uso: 45 vezes
Taxa Sucesso: 92% ✅
Confiança Média: 0.87
Padrão: *(rastrear)*(entrega)*
```

---

## 🎯 Checklist de Implementação

Para adicionar uma nova ação:

- [ ] Definir nome da ação (ex: `trackDelivery`)
- [ ] Adicionar case no `voice-assistant.js`
- [ ] Implementar lógica JavaScript (se necessário)
- [ ] Criar endpoint Laravel (se necessário)
- [ ] Adicionar contexto via painel ou script
- [ ] Testar com voz/texto
- [ ] Verificar no painel admin
- [ ] Treinar Carla: `php treinar_com_historico.php`
- [ ] Monitorar taxa de sucesso

---

**🚀 Pronto! Agora você domina o sistema de ações da Carla!**

Execute: `php adicionar_acoes_novas.php` para começar!
