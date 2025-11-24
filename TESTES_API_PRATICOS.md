# Testes e Exemplos Práticos - APIs MyD Bar & Restaurantes

## 🧪 Collection Postman/Insomnia

### Configuração Base
- **Base URL**: `http://localhost:8000`
- **Content-Type**: `application/json`
- **Authorization**: `Bearer {{token}}`

---

## 🔐 Testes de Autenticação

### 1. Registro de Usuário
```http
POST /api/auth/register
Content-Type: application/json

{
    "nome": "Teste Garcom",
    "email": "garcom@teste.com",
    "password": "123456",
    "tipo": "garcom"
}
```

**Resposta Esperada (201):**
```json
{
    "user": {
        "nome": "Teste Garcom",
        "email": "garcom@teste.com",
        "tipo": "garcom",
        "updated_at": "2025-11-11T14:30:00.000000Z",
        "created_at": "2025-11-11T14:30:00.000000Z",
        "id": 2
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

### 2. Login
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "garcom@teste.com",
    "password": "123456"
}
```

### 3. Verificar Usuário Atual
```http
GET /api/auth/me
Authorization: Bearer {{token}}
```

---

## 📦 Testes de Produtos

### 1. Listar Produtos (Público)
```http
GET /api/produtos-public
```

### 2. Buscar Produtos por Nome
```http
GET /api/produtos-public?search=hamburguer
```

### 3. Criar Produto (Requer Auth)
```http
POST /api/produtos
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "nome": "Pizza Margherita",
    "descricao": "Pizza com molho de tomate, mozzarella e manjericão",
    "preco": 32.90,
    "categoria_id": 1,
    "disponivel": true
}
```

### 4. Alternar Status do Produto
```http
PATCH /api/produtos/1/toggle-status
Authorization: Bearer {{token}}
```

---

## 🏷️ Testes de Categorias

### 1. Listar Categorias
```http
GET /api/categorias-public
```

### 2. Criar Categoria
```http
POST /api/categorias
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "nome": "Pizzas",
    "descricao": "Pizzas tradicionais e especiais"
}
```

---

## 🪑 Testes de Mesas

### 1. Listar Todas as Mesas
```http
GET /api/mesas
Authorization: Bearer {{token}}
```

### 2. Criar Mesa
```http
POST /api/mesas
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "numero": 10,
    "capacidade": 6,
    "status": "livre"
}
```

### 3. Atualizar Status da Mesa
```http
PUT /api/mesas/1
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "numero": 1,
    "capacidade": 4,
    "status": "ocupada"
}
```

---

## 📝 Testes de Pedidos

### 1. Criar Pedido
```http
POST /api/pedidos
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "mesa_id": 1,
    "observacoes": "Cliente alérgico a amendoim"
}
```

### 2. Adicionar Item ao Pedido
```http
POST /api/item-pedidos
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "pedido_id": 1,
    "produto_id": 1,
    "quantidade": 2,
    "observacoes": "Sem cebola"
}
```

### 3. Adicionar Múltiplos Itens
```http
POST /api/item-pedidos/multiplos
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "pedido_id": 1,
    "itens": [
        {
            "produto_id": 1,
            "quantidade": 2,
            "observacoes": "Bem passado"
        },
        {
            "produto_id": 3,
            "quantidade": 1,
            "observacoes": "Gelado"
        }
    ]
}
```

### 4. Listar Itens do Pedido
```http
GET /api/pedidos/1/itens
Authorization: Bearer {{token}}
```

### 5. Finalizar Pedido
```http
POST /api/pedidos/1/finalizar
Authorization: Bearer {{token}}
```

### 6. Listar Pedidos com Filtros
```http
GET /api/pedidos?status=aberto&mesa_id=1
Authorization: Bearer {{token}}
```

---

## 💰 Testes de Caixa e Pagamentos

### 1. Verificar Status do Caixa
```http
GET /caixa/api/totais-tempo-real
```

### 2. Processar Pagamento Simples
```http
POST /caixa/processar-pagamento/1
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "forma_pagamento": "dinheiro",
    "valor_recebido": 50.00,
    "observacoes": "Troco de R$ 15,50"
}
```

### 3. Processar Múltiplos Pagamentos
```http
POST /caixa/processar-pagamento/1
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "multiplos_pagamentos": "[{\"forma_pagamento\":\"dinheiro\",\"valor\":20.00},{\"forma_pagamento\":\"cartao_credito\",\"valor\":15.50}]"
}
```

### 4. Consultar Pagamentos de um Pedido
```http
GET /pagamentos/api/pedido/1
Authorization: Bearer {{token}}
```

---

## 👨‍🍳 Testes do Modo Garçom

### 1. Dashboard do Garçom
```http
GET /garcom/dashboard-data
Authorization: Bearer {{token}}
```

### 2. Buscar Produtos
```http
GET /garcom/buscar-produtos?q=hamburguer
Authorization: Bearer {{token}}
```

### 3. Informações para Pagamento da Mesa
```http
GET /garcom/mesas/1/info-pagamento
Authorization: Bearer {{token}}
```

### 4. Processar Pagamento no Modo Garçom
```http
POST /garcom/processar-pagamento/1
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "forma_pagamento": "cartao_debito",
    "valor_recebido": 35.50
}
```

---

## 📊 Testes de Dashboard e Relatórios

### 1. Estatísticas Gerais
```http
GET /api/dashboard/stats
Authorization: Bearer {{token}}
```

### 2. Pedidos por Status
```http
GET /api/dashboard/pedidos-status
Authorization: Bearer {{token}}
```

### 3. Produtos Mais Vendidos
```http
GET /api/dashboard/produtos-vendidos
Authorization: Bearer {{token}}
```

### 4. Relatório de Vendas
```http
GET /api/relatorios/vendas?data_inicio=2025-11-01&data_fim=2025-11-11
Authorization: Bearer {{token}}
```

### 5. Itens Mais Vendidos
```http
GET /api/relatorios/itens-mais-vendidos
Authorization: Bearer {{token}}
```

### 6. Mesas Mais Populares
```http
GET /api/relatorios/mesas-populares
Authorization: Bearer {{token}}
```

---

## 🧪 Testes de Debug e Utilitários

### 1. Teste Simples da API
```http
GET /api/test-simple
```

### 2. Debug de Pedido Específico
```http
GET /api/debug-pedido/1
Authorization: Bearer {{token}}
```

### 3. Teste de Itens
```http
GET /api/test-itens/1
Authorization: Bearer {{token}}
```

---

## 🔄 Cenários de Teste Completos

### Cenário 1: Fluxo Completo de Pedido

1. **Autenticar:**
```http
POST /api/auth/login
{
    "email": "garcom@teste.com",
    "password": "123456"
}
```

2. **Criar pedido para mesa 1:**
```http
POST /api/pedidos
{
    "mesa_id": 1,
    "observacoes": "Mesa próxima à janela"
}
```

3. **Adicionar itens:**
```http
POST /api/item-pedidos/multiplos
{
    "pedido_id": {pedido_id},
    "itens": [
        {"produto_id": 1, "quantidade": 2},
        {"produto_id": 3, "quantidade": 1, "observacoes": "Sem gelo"}
    ]
}
```

4. **Finalizar pedido:**
```http
POST /api/pedidos/{pedido_id}/finalizar
```

5. **Processar pagamento:**
```http
POST /caixa/processar-pagamento/{pedido_id}
{
    "forma_pagamento": "dinheiro",
    "valor_recebido": 50.00
}
```

### Cenário 2: Pagamento Múltiplo

1. **Criar pedido com valor alto (R$ 85,50)**
2. **Processar com múltiplas formas:**
```http
POST /caixa/processar-pagamento/{pedido_id}
{
    "multiplos_pagamentos": "[{\"forma_pagamento\":\"dinheiro\",\"valor\":30.00},{\"forma_pagamento\":\"cartao_credito\",\"valor\":35.50},{\"forma_pagamento\":\"vale_refeicao\",\"valor\":20.00}]"
}
```

### Cenário 3: Gestão de Produtos

1. **Criar categoria:**
```http
POST /api/categorias
{
    "nome": "Sobremesas",
    "descricao": "Doces e sobremesas"
}
```

2. **Criar produto:**
```http
POST /api/produtos
{
    "nome": "Pudim de Leite",
    "descricao": "Pudim tradicional com calda",
    "preco": 12.50,
    "categoria_id": {categoria_id},
    "disponivel": true
}
```

3. **Desabilitar produto:**
```http
PATCH /api/produtos/{produto_id}/toggle-status
```

---

## ⚠️ Casos de Erro Comuns

### 1. Produto Não Disponível
```http
POST /api/item-pedidos
{
    "pedido_id": 1,
    "produto_id": 999,
    "quantidade": 1
}
```

**Resposta (404):**
```json
{
    "error": "Produto não encontrado"
}
```

### 2. Mesa Inexistente
```http
POST /api/pedidos
{
    "mesa_id": 999
}
```

**Resposta (422):**
```json
{
    "message": "Mesa não encontrada",
    "errors": {
        "mesa_id": ["Mesa inválida"]
    }
}
```

### 3. Pagamento com Valor Incorreto
```http
POST /caixa/processar-pagamento/1
{
    "multiplos_pagamentos": "[{\"forma_pagamento\":\"dinheiro\",\"valor\":10.00}]"
}
```

**Resposta (400):**
```json
{
    "error": "Total dos pagamentos (R$ 10,00) não confere com o valor do pedido (R$ 35,50)."
}
```

---

## 📋 Checklist de Testes

### ✅ Autenticação
- [ ] Registro de usuário
- [ ] Login com credenciais válidas
- [ ] Login com credenciais inválidas
- [ ] Acesso a endpoint protegido sem token
- [ ] Acesso a endpoint protegido com token válido
- [ ] Logout
- [ ] Refresh token

### ✅ Produtos
- [ ] Listar produtos públicos
- [ ] Buscar produtos por nome
- [ ] Criar produto (autenticado)
- [ ] Atualizar produto
- [ ] Alternar status de disponibilidade
- [ ] Deletar produto

### ✅ Pedidos
- [ ] Criar pedido
- [ ] Adicionar item individual
- [ ] Adicionar múltiplos itens
- [ ] Listar itens do pedido
- [ ] Atualizar item
- [ ] Remover item
- [ ] Finalizar pedido
- [ ] Filtrar pedidos por status

### ✅ Pagamentos
- [ ] Pagamento simples em dinheiro
- [ ] Pagamento simples em cartão
- [ ] Múltiplos pagamentos
- [ ] Pagamento com valor incorreto
- [ ] Verificar totais do caixa

### ✅ Relatórios
- [ ] Estatísticas do dashboard
- [ ] Produtos mais vendidos
- [ ] Relatório de vendas por período
- [ ] Mesas mais populares

---

**Dica:** Use variáveis de ambiente no Postman/Insomnia para gerenciar tokens e URLs base automaticamente.
