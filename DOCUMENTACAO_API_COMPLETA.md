# Documentação API - Sistema MyD Bar & Restaurantes

## Visão Geral

Este documento detalha todas as APIs REST disponíveis no sistema de gestão para bares e restaurantes MyD. O sistema oferece endpoints para autenticação, gestão de produtos, pedidos, mesas, pagamentos e relatórios.

**Base URL:** `http://localhost:8000/api`

## Autenticação

### 🔐 AuthController

Gerencia autenticação de usuários com JWT tokens.

#### `POST /api/auth/register`
Registra um novo usuário no sistema.

**Request Body:**
```json
{
    "nome": "string",
    "email": "string",
    "password": "string",
    "tipo": "admin|garcom|caixa"
}
```

**Response (201):**
```json
{
    "user": {
        "id": 1,
        "nome": "João Silva",
        "email": "joao@email.com",
        "tipo": "admin"
    },
    "token": "jwt_token_here"
}
```

#### `POST /api/auth/login`
Autentica usuário e retorna token JWT.

**Request Body:**
```json
{
    "email": "string",
    "password": "string"
}
```

**Response (200):**
```json
{
    "token": "jwt_token_here",
    "user": {
        "id": 1,
        "nome": "João Silva",
        "email": "joao@email.com",
        "tipo": "admin"
    }
}
```

#### `POST /api/auth/logout`
Faz logout do usuário atual.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "message": "Logout realizado com sucesso"
}
```

#### `GET /api/auth/me`
Retorna informações do usuário autenticado.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "id": 1,
    "nome": "João Silva",
    "email": "joao@email.com",
    "tipo": "admin"
}
```

#### `POST /api/auth/refresh`
Renova o token JWT.

**Headers:** `Authorization: Bearer {token}`

#### `POST /api/auth/revoke-all`
Revoga todos os tokens do usuário.

---

## 📦 Produtos

### ProdutoController

Gerencia o catálogo de produtos.

#### `GET /api/produtos`
Lista todos os produtos.

**Query Parameters:**
- `categoria_id` (optional): Filtrar por categoria
- `disponivel` (optional): true/false
- `search` (optional): Buscar por nome

**Response (200):**
```json
[
    {
        "id": 1,
        "nome": "Hambúrguer Clássico",
        "descricao": "Hambúrguer com carne, queijo e salada",
        "preco": 18.50,
        "categoria_id": 1,
        "disponivel": true,
        "categoria": {
            "id": 1,
            "nome": "Lanches"
        }
    }
]
```

#### `GET /api/produtos-public`
Lista produtos públicos (não requer autenticação).

#### `POST /api/produtos`
Cria um novo produto.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "nome": "string",
    "descricao": "string",
    "preco": "decimal",
    "categoria_id": "integer",
    "disponivel": "boolean"
}
```

#### `GET /api/produtos/{id}`
Retorna detalhes de um produto específico.

#### `PUT /api/produtos/{id}`
Atualiza um produto existente.

#### `DELETE /api/produtos/{id}`
Remove um produto.

#### `PATCH /api/produtos/{id}/toggle-status`
Alterna o status de disponibilidade do produto.

---

## 🏷️ Categorias

### CategoriaController

Gerencia categorias de produtos.

#### `GET /api/categorias`
Lista todas as categorias.

**Response (200):**
```json
[
    {
        "id": 1,
        "nome": "Lanches",
        "descricao": "Hambúrguers e sanduíches",
        "produtos_count": 5
    }
]
```

#### `GET /api/categorias-public`
Lista categorias públicas.

#### `POST /api/categorias`
Cria uma nova categoria.

**Request Body:**
```json
{
    "nome": "string",
    "descricao": "string"
}
```

#### `GET /api/categorias/{id}`
Detalhes de uma categoria específica.

#### `PUT /api/categorias/{id}`
Atualiza uma categoria.

#### `DELETE /api/categorias/{id}`
Remove uma categoria.

---

## 🪑 Mesas

### MesaController

Gerencia mesas do estabelecimento.

#### `GET /api/mesas`
Lista todas as mesas.

**Response (200):**
```json
[
    {
        "id": 1,
        "numero": 1,
        "capacidade": 4,
        "status": "livre|ocupada|reservada",
        "pedido_atual": null
    }
]
```

#### `POST /api/mesas`
Cria uma nova mesa.

**Request Body:**
```json
{
    "numero": "integer",
    "capacidade": "integer",
    "status": "livre"
}
```

#### `GET /api/mesas/{id}`
Detalhes de uma mesa específica.

#### `PUT /api/mesas/{id}`
Atualiza informações da mesa.

#### `DELETE /api/mesas/{id}`
Remove uma mesa.

---

## 📝 Pedidos

### PedidoController

Gerencia pedidos e comandas.

#### `GET /api/pedidos`
Lista todos os pedidos.

**Query Parameters:**
- `status` (optional): aberto|finalizado|pago|cancelado
- `mesa_id` (optional): Filtrar por mesa
- `data_inicio` (optional): Data início (YYYY-MM-DD)
- `data_fim` (optional): Data fim (YYYY-MM-DD)

**Response (200):**
```json
[
    {
        "id": 1,
        "mesa_id": 1,
        "usuario_id": 1,
        "status": "aberto",
        "total": 25.50,
        "observacoes": "Sem cebola",
        "created_at": "2025-11-11T10:30:00Z",
        "mesa": {
            "numero": 1
        },
        "itens": [
            {
                "id": 1,
                "produto_id": 1,
                "quantidade": 2,
                "preco_unitario": 12.75,
                "subtotal": 25.50,
                "produto": {
                    "nome": "Hambúrguer"
                }
            }
        ]
    }
]
```

#### `POST /api/pedidos`
Cria um novo pedido.

**Request Body:**
```json
{
    "mesa_id": "integer",
    "observacoes": "string (optional)"
}
```

#### `GET /api/pedidos/{id}`
Detalhes de um pedido específico.

#### `GET /api/pedidos-public/{id}`
Detalhes públicos de um pedido.

#### `PUT /api/pedidos/{id}`
Atualiza um pedido.

#### `DELETE /api/pedidos/{id}`
Remove um pedido.

#### `POST /api/pedidos/{id}/finalizar`
Finaliza um pedido.

**Response (200):**
```json
{
    "message": "Pedido finalizado com sucesso",
    "pedido": {
        "id": 1,
        "status": "finalizado",
        "total": 25.50
    }
}
```

---

## 🍽️ Itens de Pedido

### ItemPedidoController

Gerencia itens dentro dos pedidos.

#### `GET /api/item-pedidos`
Lista todos os itens de pedidos.

#### `POST /api/item-pedidos`
Adiciona um item ao pedido.

**Request Body:**
```json
{
    "pedido_id": "integer",
    "produto_id": "integer", 
    "quantidade": "integer",
    "observacoes": "string (optional)"
}
```

#### `POST /api/item-pedidos/multiplos`
Adiciona múltiplos itens ao pedido.

**Request Body:**
```json
{
    "pedido_id": "integer",
    "itens": [
        {
            "produto_id": "integer",
            "quantidade": "integer",
            "observacoes": "string (optional)"
        }
    ]
}
```

#### `GET /api/pedidos/{pedido_id}/itens`
Lista itens de um pedido específico.

#### `GET /api/item-pedidos/{id}`
Detalhes de um item específico.

#### `PUT /api/item-pedidos/{id}`
Atualiza um item do pedido.

#### `DELETE /api/item-pedidos/{id}`
Remove um item do pedido.

---

## 👥 Usuários

### UsuarioController

Gerencia usuários do sistema.

#### `GET /api/usuarios`
Lista todos os usuários.

**Response (200):**
```json
[
    {
        "id": 1,
        "nome": "João Silva",
        "email": "joao@email.com",
        "tipo": "admin",
        "created_at": "2025-11-11T10:00:00Z"
    }
]
```

#### `POST /api/usuarios`
Cria um novo usuário.

#### `GET /api/usuarios/{id}`
Detalhes de um usuário específico.

#### `PUT /api/usuarios/{id}`
Atualiza um usuário.

#### `DELETE /api/usuarios/{id}`
Remove um usuário.

---

## 📊 Dashboard & Estatísticas

### DashboardController

Fornece dados para dashboards e estatísticas.

#### `GET /api/dashboard/stats`
Estatísticas gerais do sistema.

**Response (200):**
```json
{
    "total_pedidos": 150,
    "pedidos_hoje": 25,
    "faturamento_hoje": 580.50,
    "mesas_ocupadas": 8,
    "produtos_cadastrados": 45
}
```

#### `GET /api/dashboard/pedidos-status`
Distribuição de pedidos por status.

**Response (200):**
```json
{
    "aberto": 5,
    "finalizado": 10,
    "pago": 8,
    "cancelado": 2
}
```

#### `GET /api/dashboard/produtos-vendidos`
Produtos mais vendidos.

**Response (200):**
```json
[
    {
        "produto": "Hambúrguer Clássico",
        "quantidade_vendida": 45,
        "faturamento": 832.50
    }
]
```

---

## 💰 Pagamentos & Caixa

### CaixaController

Sistema de caixa e pagamentos.

#### `GET /caixa/api/totais-tempo-real`
Totais do caixa em tempo real.

**Response (200):**
```json
{
    "totais": {
        "total_vendas": 1250.50,
        "total_recebido": 1250.50,
        "total_troco": 25.00,
        "quantidade_vendas": 15,
        "por_forma": {
            "dinheiro": {
                "quantidade": 8,
                "total": 450.00,
                "recebido": 475.00,
                "troco": 25.00
            },
            "cartao_credito": {
                "quantidade": 7,
                "total": 800.50,
                "recebido": 800.50,
                "troco": 0
            }
        }
    },
    "caixa_aberto": true,
    "status_caixa": "aberto"
}
```

#### `POST /caixa/processar-pagamento/{pedido}`
Processa pagamento de um pedido.

**Request Body (Pagamento Simples):**
```json
{
    "forma_pagamento": "dinheiro|cartao_credito|cartao_debito|pix|vale_refeicao",
    "valor_recebido": "decimal",
    "observacoes": "string (optional)"
}
```

**Request Body (Múltiplos Pagamentos):**
```json
{
    "multiplos_pagamentos": "[{\"forma_pagamento\":\"dinheiro\",\"valor\":15.00},{\"forma_pagamento\":\"cartao_credito\",\"valor\":10.00}]"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Pagamento processado com sucesso!",
    "pagamento": {
        "id": 1,
        "forma_pagamento": "dinheiro",
        "valor": 25.50,
        "status": "confirmado"
    },
    "troco": 4.50
}
```

### PagamentoController

#### `GET /pagamentos/api/pedido/{pedido}`
Pagamentos de um pedido específico.

#### `GET /pagamentos/api/estatisticas`
Estatísticas de pagamentos.

---

## 🍽️ Modo Garçom

### GarcomController

Interface específica para garçons.

#### `GET /garcom/dashboard-data`
Dados do dashboard do garçom.

**Response (200):**
```json
{
    "pedidos_abertos": 5,
    "pedidos_finalizados": 3,
    "mesas_ocupadas": 8,
    "total_vendas_hoje": 450.75
}
```

#### `GET /garcom/buscar-produtos`
Busca produtos para o garçom.

**Query Parameters:**
- `q`: Termo de busca

#### `POST /garcom/processar-pagamento/{pedido}`
Processa pagamento no modo garçom.

#### `POST /garcom/processar-pagamento-mesa/{mesa}`
Processa pagamento de toda uma mesa.

#### `GET /garcom/mesas/{mesa}/info-pagamento`
Informações para pagamento de uma mesa.

---

## 📈 Relatórios

### RelatorioController

Gera relatórios diversos.

#### `GET /api/relatorios/vendas`
Relatório de vendas.

**Query Parameters:**
- `data_inicio`: Data início (YYYY-MM-DD)
- `data_fim`: Data fim (YYYY-MM-DD)

#### `GET /api/relatorios/status-pedidos`
Relatório de status dos pedidos.

#### `GET /api/relatorios/mesas-populares`
Mesas mais utilizadas.

#### `GET /api/relatorios/horarios-movimento`
Horários de maior movimento.

#### `GET /api/relatorios/itens-mais-vendidos`
Itens mais vendidos.

---

## 🔧 Utilitários & Debug

### Endpoints de Debug

#### `GET /api/test-simple`
Teste simples da API.

#### `GET /api/debug-all`
Debug geral do sistema.

#### `GET /api/debug-pedido/{pedido}`
Debug de pedido específico.

#### `GET /api/test-itens/{pedido}`
Teste de itens de um pedido.

---

## 📋 Códigos de Status HTTP

- **200** - Sucesso
- **201** - Criado com sucesso
- **400** - Erro de validação
- **401** - Não autorizado
- **403** - Acesso negado
- **404** - Recurso não encontrado
- **422** - Erro de validação de dados
- **500** - Erro interno do servidor

---

## 🔒 Autenticação

A maioria dos endpoints requer autenticação JWT. Inclua o header:

```
Authorization: Bearer {seu_token_jwt}
```

Tokens são obtidos através do endpoint `/api/auth/login`.

---

## 💡 Exemplos de Uso

### Fluxo Completo: Criar Pedido e Processar Pagamento

```javascript
// 1. Login
const loginResponse = await fetch('/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        email: 'garcom@email.com',
        password: '123456'
    })
});
const { token } = await loginResponse.json();

// 2. Criar pedido
const pedidoResponse = await fetch('/api/pedidos', {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        mesa_id: 1,
        observacoes: 'Sem cebola'
    })
});
const pedido = await pedidoResponse.json();

// 3. Adicionar itens
await fetch('/api/item-pedidos/multiplos', {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        pedido_id: pedido.id,
        itens: [
            { produto_id: 1, quantidade: 2 },
            { produto_id: 3, quantidade: 1 }
        ]
    })
});

// 4. Finalizar pedido
await fetch(`/api/pedidos/${pedido.id}/finalizar`, {
    method: 'POST',
    headers: { 'Authorization': `Bearer ${token}` }
});

// 5. Processar pagamento múltiplo
const pagamentoResponse = await fetch(`/caixa/processar-pagamento/${pedido.id}`, {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        multiplos_pagamentos: JSON.stringify([
            { forma_pagamento: 'dinheiro', valor: 20.00 },
            { forma_pagamento: 'cartao_credito', valor: 15.50 }
        ])
    })
});
```

---

## 📞 Suporte

Para dúvidas sobre a API, consulte:
- Logs do sistema: `/logs`
- Documentação técnica nos controllers
- Testes de endpoints disponíveis no diretório do projeto

---

**Última atualização:** 11 de novembro de 2025
**Versão da API:** 1.0.0
