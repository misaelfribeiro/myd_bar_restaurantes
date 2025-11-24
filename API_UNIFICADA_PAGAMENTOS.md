# API Unificada de Pagamentos - MyD Bar & Restaurantes

## 🎯 Visão Geral

A **API Unificada de Pagamentos** centraliza todo o processamento de pagamentos do sistema, eliminando a duplicação de código que existia entre o modo garçom (`GarcomController`) e o modo caixa (`CaixaController`).

## ❌ Problema Anterior

Antes tínhamos **dois métodos separados** para pagamentos:

### 1. **GarcomController::processarPagamento()**
- Usado pela interface do garçom
- Lógica específica para pagamentos únicos
- Atualização manual dos totais do caixa
- Validações básicas

### 2. **CaixaController::processarPagamento()**
- Usado pela interface do caixa/balcão
- Suporte para múltiplos pagamentos
- Logs detalhados
- Validações mais complexas

## ✅ Solução: API Unificada

### 🏗️ **Nova Arquitetura**

```
📁 App\Http\Controllers\Api\
└── PagamentoController.php (API Unificada)
    ├── processarPagamentoPedido()    → Pagamento de pedido individual
    ├── processarPagamentoMesa()      → Pagamento de mesa completa
    ├── infoParaPagamentoPedido()     → Dados para tela de pagamento
    └── infoParaPagamentoMesa()       → Dados para pagamento de mesa
```

## 🚀 Funcionalidades

### ✨ **Pagamentos Únicos**
```json
POST /api/pagamentos/pedido/{pedido}
{
    "forma_pagamento": "dinheiro",
    "valor": 50.00,
    "valor_recebido": 60.00,
    "observacoes": "Cliente João"
}
```

### ✨ **Pagamentos Múltiplos**
```json
POST /api/pagamentos/pedido/{pedido}
{
    "multiplos_pagamentos": [
        {
            "forma_pagamento": "dinheiro",
            "valor": 25.00,
            "valor_recebido": 25.00
        },
        {
            "forma_pagamento": "cartao_credito",
            "valor": 25.00
        }
    ]
}
```

### ✨ **Pagamento de Mesa Completa**
```json
POST /api/pagamentos/mesa/{mesa}
{
    "forma_pagamento": "cartao_debito",
    "valor": 150.00
}
```

### ✨ **Informações para Pagamento**
```json
GET /api/pagamentos/info/pedido/{pedido}
{
    "success": true,
    "data": {
        "pedido": {
            "id": 1,
            "mesa": "Mesa 5",
            "total": 50.00,
            "saldo_restante": 50.00
        },
        "itens": [...],
        "formas_pagamento_disponiveis": {...}
    }
}
```

## 🎯 Benefícios da Unificação

### 1. **📝 Código Único**
- ✅ Uma única implementação para todos os cenários
- ✅ Manutenção simplificada
- ✅ Menos bugs e inconsistências

### 2. **🔧 Funcionalidades Avançadas**
- ✅ Suporte completo a múltiplos pagamentos
- ✅ Pagamento proporcional de mesas
- ✅ Cálculo automático de troco
- ✅ Validações rigorosas

### 3. **📊 Logs e Debug**
- ✅ Logs detalhados para troubleshooting
- ✅ Rastreamento completo de transações
- ✅ Informações estruturadas

### 4. **💰 Gestão de Caixa**
- ✅ Atualização automática dos totais
- ✅ Separação de cartão crédito/débito
- ✅ Totalizações precisas por forma de pagamento

### 5. **🔒 Validações Robustas**
- ✅ Verificação de caixa aberto
- ✅ Validação de pedidos finalizados
- ✅ Conferência de totais
- ✅ Prevenção de pagamentos duplicados

## 🛠️ Rotas da API

### **Autenticação Necessária**: `auth:sanctum + role:admin,gerente,garcom`

| Método | Rota | Descrição |
|--------|------|-----------|
| `POST` | `/api/pagamentos/pedido/{pedido}` | Processar pagamento de pedido |
| `POST` | `/api/pagamentos/mesa/{mesa}` | Processar pagamento de mesa |
| `GET` | `/api/pagamentos/info/pedido/{pedido}` | Informações para pagamento de pedido |
| `GET` | `/api/pagamentos/info/mesa/{mesa}` | Informações para pagamento de mesa |

## 📱 Como Usar

### **1. Para o Garçom (Mobile/Tablet):**
```javascript
// Consultar informações do pedido
const info = await fetch('/api/pagamentos/info/pedido/123');

// Processar pagamento único
const resultado = await fetch('/api/pagamentos/pedido/123', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + token
    },
    body: JSON.stringify({
        forma_pagamento: 'cartao_credito',
        valor: 45.00
    })
});
```

### **2. Para o Caixa (Desktop):**
```javascript
// Processar múltiplos pagamentos
const resultado = await fetch('/api/pagamentos/pedido/123', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + token
    },
    body: JSON.stringify({
        multiplos_pagamentos: JSON.stringify([
            { forma_pagamento: 'dinheiro', valor: 30.00, valor_recebido: 30.00 },
            { forma_pagamento: 'pix', valor: 20.00 }
        ])
    })
});
```

## 🔄 Migração dos Códigos Existentes

### **Antes (GarcomController)**
```php
public function processarPagamento(Request $request, Pedido $pedido) {
    // Lógica específica do garçom...
}
```

### **Depois (API Unificada)**
```php
// No frontend do garçom, fazer chamada para:
POST /api/pagamentos/pedido/{pedido}

// No frontend do caixa, fazer chamada para:
POST /api/pagamentos/pedido/{pedido} (com multiplos_pagamentos)
```

## 🎉 Resultado Final

### ✅ **Consistência Total**
- Mesma lógica de validação para todos os contextos
- Mesma forma de atualização dos totais do caixa
- Mesmos logs e debugging

### ✅ **Flexibilidade Máxima**
- Suporte a pagamentos únicos e múltiplos
- Pagamento individual ou por mesa
- Todas as formas de pagamento suportadas

### ✅ **Manutenção Simplificada**
- Um único ponto para alterações
- Testes centralizados
- Documentação única

---

## 🚀 **Próximos Passos**

1. **✅ API criada** - `App\Http\Controllers\Api\PagamentoController`
2. **✅ Rotas configuradas** - `/routes/api.php`
3. **⏳ Atualizar frontend do garçom** - Migrar para usar a API unificada
4. **⏳ Atualizar frontend do caixa** - Migrar para usar a API unificada
5. **⏳ Deprecar métodos antigos** - Remover código duplicado dos controllers

---

**💡 A API unificada resolve o problema principal: diferentes comportamentos entre garçom e caixa, criando uma experiência consistente e confiável em todo o sistema!**
