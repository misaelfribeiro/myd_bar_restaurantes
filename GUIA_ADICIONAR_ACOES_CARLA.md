# 🎯 Sistema de Ações da Carla - Guia Completo

## 📚 O que são Ações?

As **ações** são comandos que a Carla pode executar no app além de apenas responder com texto. Quando a Carla identifica um contexto, ela pode:

1. **Responder** com texto
2. **Executar** uma ação no app (abrir tela, buscar dados, etc.)

---

## 🔧 Ações Disponíveis (Padrão)

### 1. **searchProduct** - Buscar Produto
```javascript
Uso: Quando usuário procura por um produto específico
Exemplo: "quero pizza", "tem hambúrguer?"
Efeito: Abre tela de busca e procura pelo termo
```

### 2. **showMenu** - Mostrar Cardápio
```javascript
Uso: Quando usuário quer ver o cardápio completo
Exemplo: "mostra o cardápio", "quero ver os produtos"
Efeito: Navega para tela do cardápio
```

### 3. **showCart** - Mostrar Carrinho
```javascript
Uso: Quando usuário quer ver itens do carrinho
Exemplo: "meu carrinho", "o que eu pedi?", "ver sacola"
Efeito: Abre tela do carrinho de compras
```

### 4. **showOrders** - Mostrar Pedidos
```javascript
Uso: Quando usuário quer ver histórico de pedidos
Exemplo: "meus pedidos", "onde está meu pedido?"
Efeito: Navega para tela de pedidos
```

### 5. **checkout** - Finalizar Pedido
```javascript
Uso: Quando usuário quer finalizar compra
Exemplo: "finalizar pedido", "quero pagar"
Efeito: Vai para tela de checkout/pagamento
```

---

## 🚀 Como Adicionar Novas Ações

### Passo 1: Definir a Ação no Frontend

**Arquivo**: `public/js/voice-assistant.js`

```javascript
// Localizar a função handleAction
function handleAction(action, parameters) {
    console.log('Executando ação:', action, parameters);
    
    switch(action) {
        // AÇÕES EXISTENTES
        case 'searchProduct':
            if (parameters.product) {
                searchProduct(parameters.product);
            }
            break;
        
        case 'showMenu':
            window.location.href = '/cardapio';
            break;
        
        case 'showCart':
            window.location.href = '/carrinho';
            break;
        
        case 'showOrders':
            window.location.href = '/pedidos';
            break;
        
        case 'checkout':
            window.location.href = '/checkout';
            break;
        
        // ============================================
        // ADICIONE SUAS NOVAS AÇÕES AQUI
        // ============================================
        
        case 'showPromotions':
            // Mostra promoções ativas
            window.location.href = '/promocoes';
            break;
        
        case 'trackDelivery':
            // Rastreia entrega em andamento
            if (parameters.order_id) {
                window.location.href = `/pedido/${parameters.order_id}/rastrear`;
            } else {
                // Pega último pedido
                window.location.href = '/rastrear-entrega';
            }
            break;
        
        case 'filterByCategory':
            // Filtra produtos por categoria
            if (parameters.category) {
                window.location.href = `/cardapio?categoria=${parameters.category}`;
            }
            break;
        
        case 'addToCart':
            // Adiciona produto direto no carrinho
            if (parameters.product_id) {
                addProductToCart(parameters.product_id, parameters.quantity || 1);
            }
            break;
        
        case 'removeFromCart':
            // Remove produto do carrinho
            if (parameters.product_id) {
                removeProductFromCart(parameters.product_id);
            }
            break;
        
        case 'applyDiscount':
            // Aplica cupom de desconto
            if (parameters.coupon_code) {
                applyCoupon(parameters.coupon_code);
            }
            break;
        
        case 'changeAddress':
            // Alterar endereço de entrega
            window.location.href = '/perfil/enderecos';
            break;
        
        case 'changePayment':
            // Alterar forma de pagamento
            window.location.href = '/checkout?step=pagamento';
            break;
        
        case 'repeatOrder':
            // Repetir último pedido
            if (parameters.order_id) {
                repeatOrder(parameters.order_id);
            }
            break;
        
        case 'showReviews':
            // Mostrar avaliações de produto
            if (parameters.product_id) {
                window.location.href = `/produto/${parameters.product_id}#avaliacoes`;
            }
            break;
        
        case 'contactSupport':
            // Abrir suporte/chat
            openSupportChat();
            break;
        
        case 'showProfile':
            // Abrir perfil do usuário
            window.location.href = '/perfil';
            break;
        
        case 'showFavorites':
            // Mostrar favoritos
            window.location.href = '/favoritos';
            break;
        
        case 'scheduleOrder':
            // Agendar pedido para depois
            window.location.href = '/checkout?agendar=true';
            break;
        
        case 'cancelOrder':
            // Cancelar pedido
            if (parameters.order_id) {
                cancelOrder(parameters.order_id);
            }
            break;
        
        default:
            console.log('Ação não reconhecida:', action);
    }
}
```

### Passo 2: Criar Contexto com a Nova Ação

**Opção A: Via Painel Admin**
```
1. Acesse: http://localhost:8000/admin/carla
2. Clique: "Adicionar Contexto"
3. Preencha:
   - Categoria: delivery
   - Key: track_delivery
   - Padrão: *(rastrear|rastreio|onde está|cadê)*(pedido|entrega)*
   - Resposta: Vou verificar onde está sua entrega!
   - Ação: trackDelivery
   - Limiar: 0.7
4. Salve!
```

**Opção B: Via Script PHP**

Crie arquivo: `adicionar_nova_acao.php`

```php
<?php
require __DIR__.'/bootstrap/app.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AIContext;

// EXEMPLO 1: Rastrear Entrega
AIContext::create([
    'category' => 'delivery',
    'key' => 'track_delivery',
    'pattern' => '*(rastrear|rastreio|onde está|cadê)*(pedido|entrega)*',
    'response_template' => 'Sou a Carla da EatsFood! Vou verificar onde está sua entrega agora mesmo!',
    'action' => 'trackDelivery',
    'parameters' => json_encode([]),
    'confidence_threshold' => 0.7,
    'active' => true
]);

// EXEMPLO 2: Ver Promoções
AIContext::create([
    'category' => 'menu',
    'key' => 'show_promotions',
    'pattern' => '*(promoção|oferta|desconto|barato)*(hoje|ativa)*',
    'response_template' => 'Sou a Carla da EatsFood! Temos promoções incríveis hoje! Vou te mostrar.',
    'action' => 'showPromotions',
    'parameters' => json_encode([]),
    'confidence_threshold' => 0.7,
    'active' => true
]);

// EXEMPLO 3: Filtrar por Categoria
AIContext::create([
    'category' => 'search',
    'key' => 'filter_bebidas',
    'pattern' => '*(bebida|refrigerante|suco|drink)*',
    'response_template' => 'Sou a Carla da EatsFood! Vou mostrar nossas bebidas!',
    'action' => 'filterByCategory',
    'parameters' => json_encode(['category' => 'bebidas']),
    'confidence_threshold' => 0.7,
    'active' => true
]);

// EXEMPLO 4: Aplicar Cupom
AIContext::create([
    'category' => 'cart',
    'key' => 'apply_discount',
    'pattern' => '*(cupom|desconto|código)*(aplicar|usar)*',
    'response_template' => 'Sou a Carla da EatsFood! Qual o código do cupom?',
    'action' => 'applyDiscount',
    'parameters' => json_encode([]),
    'confidence_threshold' => 0.7,
    'active' => true
]);

// EXEMPLO 5: Repetir Pedido
AIContext::create([
    'category' => 'orders',
    'key' => 'repeat_order',
    'pattern' => '*(repetir|de novo|novamente)*(pedido|mesmo)*',
    'response_template' => 'Sou a Carla da EatsFood! Vou repetir seu último pedido!',
    'action' => 'repeatOrder',
    'parameters' => json_encode([]),
    'confidence_threshold' => 0.7,
    'active' => true
]);

echo "✅ 5 novos contextos com ações adicionados!\n";
```

Execute:
```bash
php adicionar_nova_acao.php
```

### Passo 3: Implementar Funções Auxiliares (se necessário)

Se a ação precisa de lógica JavaScript complexa:

```javascript
// public/js/voice-assistant.js

function addProductToCart(productId, quantity) {
    fetch('/api/carrinho/adicionar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Produto adicionado ao carrinho!');
            updateCartCount();
        }
    });
}

function repeatOrder(orderId) {
    fetch(`/api/pedidos/${orderId}/repetir`, {
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
        }
    });
}

function applyCoupon(couponCode) {
    fetch('/api/cupom/aplicar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ code: couponCode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`✅ Cupom aplicado! Desconto: R$ ${data.discount}`);
        } else {
            alert('❌ Cupom inválido ou expirado');
        }
    });
}

function openSupportChat() {
    // Abre chat do suporte (pode ser WhatsApp, Zendesk, etc.)
    window.open('https://wa.me/5511999999999?text=Olá, preciso de ajuda', '_blank');
}

function cancelOrder(orderId) {
    if (!confirm('Tem certeza que deseja cancelar este pedido?')) {
        return;
    }
    
    fetch(`/api/pedidos/${orderId}/cancelar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Pedido cancelado com sucesso!');
            window.location.reload();
        }
    });
}
```

---

## 🎓 Exemplos de Contextos com Ações

### 1. Busca por Categoria
```javascript
Categoria: search
Key: search_pizzas
Padrão: *(pizza|pizzaria)*
Resposta: "Sou a Carla da EatsFood! Vou mostrar nossas pizzas!"
Ação: filterByCategory
Parâmetros: {"category": "pizzas"}
```

### 2. Rastreamento
```javascript
Categoria: delivery
Key: track_delivery
Padrão: *(rastrear|onde está)*(entrega|pedido)*
Resposta: "Sou a Carla da EatsFood! Vou verificar o status da sua entrega!"
Ação: trackDelivery
Parâmetros: {}
```

### 3. Aplicar Desconto
```javascript
Categoria: cart
Key: apply_discount
Padrão: *(cupom|desconto|código)*
Resposta: "Sou a Carla da EatsFood! Qual o código do cupom?"
Ação: applyDiscount
Parâmetros: {}
```

### 4. Repetir Pedido
```javascript
Categoria: orders
Key: repeat_order
Padrão: *(repetir|de novo)*(pedido)*
Resposta: "Sou a Carla da EatsFood! Vou repetir seu último pedido!"
Ação: repeatOrder
Parâmetros: {}
```

### 5. Contatar Suporte
```javascript
Categoria: help
Key: contact_support
Padrão: *(ajuda|suporte|atendimento|falar com)*(alguém|pessoa|atendente)*
Resposta: "Sou a Carla da EatsFood! Vou te conectar com nosso suporte!"
Ação: contactSupport
Parâmetros: {}
```

---

## 📊 Ações com Parâmetros Dinâmicos

### Como funciona:

1. **Carla detecta** a intenção
2. **Extrai parâmetros** do texto (usando regex)
3. **Passa parâmetros** para a ação
4. **Ação executa** com os dados

### Exemplo: Buscar Produto Específico

**Contexto:**
```javascript
Padrão: *(quero|buscar|procurar) (pizza|hambúrguer|refrigerante)*
```

**AIContext Model** (já implementado):
```php
public function extractParameters($message)
{
    // Extrai parâmetros do padrão
    preg_match_all($this->pattern, $message, $matches);
    return $matches;
}
```

**Uso no Frontend:**
```javascript
case 'searchProduct':
    // parameters.product vem da extração
    if (parameters.product) {
        searchProduct(parameters.product);
    }
    break;
```

---

## 🎯 Lista de Ações Sugeridas

### Ações de Navegação
- ✅ `showMenu` - Cardápio
- ✅ `showCart` - Carrinho
- ✅ `showOrders` - Pedidos
- ✅ `checkout` - Finalizar
- 🆕 `showPromotions` - Promoções
- 🆕 `showProfile` - Perfil
- 🆕 `showFavorites` - Favoritos
- 🆕 `showReviews` - Avaliações

### Ações de Busca
- ✅ `searchProduct` - Buscar produto
- 🆕 `filterByCategory` - Filtrar categoria
- 🆕 `filterByPrice` - Filtrar por preço
- 🆕 `sortProducts` - Ordenar produtos

### Ações de Carrinho
- 🆕 `addToCart` - Adicionar ao carrinho
- 🆕 `removeFromCart` - Remover do carrinho
- 🆕 `clearCart` - Limpar carrinho
- 🆕 `updateQuantity` - Atualizar quantidade

### Ações de Pedido
- 🆕 `repeatOrder` - Repetir pedido
- 🆕 `cancelOrder` - Cancelar pedido
- 🆕 `trackDelivery` - Rastrear entrega
- 🆕 `scheduleOrder` - Agendar pedido

### Ações de Pagamento
- 🆕 `applyDiscount` - Aplicar cupom
- 🆕 `changePayment` - Mudar pagamento
- 🆕 `changeAddress` - Mudar endereço

### Ações de Suporte
- 🆕 `contactSupport` - Contatar suporte
- 🆕 `showFAQ` - Ver perguntas frequentes
- 🆕 `reportProblem` - Reportar problema

---

## 🔥 Implementação Rápida - Copy & Paste

### 1. Adicione no `voice-assistant.js`:

```javascript
// Adicione estas ações no switch da função handleAction()

case 'showPromotions':
    window.location.href = '/promocoes';
    break;

case 'trackDelivery':
    window.location.href = '/rastrear-entrega';
    break;

case 'filterByCategory':
    if (parameters.category) {
        window.location.href = `/cardapio?categoria=${parameters.category}`;
    }
    break;

case 'repeatOrder':
    if (confirm('Deseja repetir seu último pedido?')) {
        fetch('/api/pedidos/repetir-ultimo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(r => r.json()).then(data => {
            if (data.success) window.location.href = '/carrinho';
        });
    }
    break;

case 'contactSupport':
    window.open('https://wa.me/5511999999999', '_blank');
    break;
```

### 2. Execute este script PHP:

Crie: `adicionar_acoes_rapidas.php`

```php
<?php
require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\AIContext;

$acoes = [
    ['delivery', 'track_delivery', '*(rastrear|onde está)*(entrega|pedido)*', 
     'Vou verificar sua entrega!', 'trackDelivery'],
    ['menu', 'show_promotions', '*(promoção|oferta|desconto)*', 
     'Vou te mostrar nossas promoções!', 'showPromotions'],
    ['orders', 'repeat_order', '*(repetir|de novo)*(pedido)*', 
     'Vou repetir seu último pedido!', 'repeatOrder'],
    ['help', 'contact_support', '*(ajuda|suporte|atendimento)*', 
     'Vou te conectar com o suporte!', 'contactSupport'],
];

foreach ($acoes as $acao) {
    AIContext::create([
        'category' => $acao[0],
        'key' => $acao[1],
        'pattern' => $acao[2],
        'response_template' => "Sou a Carla da EatsFood! {$acao[3]}",
        'action' => $acao[4],
        'confidence_threshold' => 0.7,
        'active' => true
    ]);
}

echo "✅ 4 novas ações adicionadas!\n";
```

Execute: `php adicionar_acoes_rapidas.php`

---

## 📈 Como Testar as Novas Ações

### 1. Via Painel Admin
```
1. Acesse: http://localhost:8000/admin/carla
2. Veja a tabela de "Contextos Ativos"
3. Procure pelas novas ações
4. Confira se estão ativas
```

### 2. Via Voice Assistant
```
1. Abra o app
2. Clique no microfone
3. Diga: "rastrear minha entrega"
4. Veja se executa a ação
```

### 3. Via Console
```javascript
// Abra DevTools (F12) e teste:
handleAction('trackDelivery', {});
handleAction('showPromotions', {});
handleAction('repeatOrder', {});
```

---

## 🎓 Próximos Passos

1. ✅ Defina quais ações seu app precisa
2. ✅ Implemente no `voice-assistant.js`
3. ✅ Crie contextos via painel ou script
4. ✅ Teste com voz e texto
5. ✅ Treine a Carla com interações reais
6. ✅ Monitore taxa de sucesso no painel
7. ✅ Ajuste padrões que não funcionam bem

---

**🚀 Pronto! Agora você sabe como adicionar quantas ações quiser para a Carla aprender!**
