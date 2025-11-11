# Sistema de Itens de Pedido - Documentação das APIs

## Visão Geral

O sistema de itens de pedido permite o detalhamento completo dos produtos incluídos em cada pedido, com funcionalidades de adição, edição, remoção e cálculo automático de totais.

## Rotas da API

### Autenticação Necessária
Todas as rotas requerem autenticação via Bearer Token e autorização para os perfis: `admin`, `gerente`, `garcom`.

### 1. CRUD Básico de Itens

#### Listar Itens
```http
GET /api/item-pedidos
GET /api/item-pedidos?pedido_id=1
```

**Parâmetros de Query:**
- `pedido_id` (opcional): Filtrar itens por pedido específico

**Resposta:**
```json
{
    "success": true,
    "itens": [
        {
            "id": 1,
            "pedido_id": 1,
            "produto_id": 1,
            "quantidade": 2,
            "preco_unitario": "25.50",
            "subtotal": "51.00",
            "observacoes": "Sem cebola",
            "created_at": "2025-11-10T19:00:00.000000Z",
            "updated_at": "2025-11-10T19:00:00.000000Z",
            "produto": {
                "id": 1,
                "nome": "Hambúrguer Clássico",
                "preco": "25.50",
                "categoria": {
                    "id": 1,
                    "nome": "Lanches"
                }
            },
            "pedido": {
                "id": 1,
                "mesa_id": 1,
                "status": "pendente",
                "total": "51.00"
            }
        }
    ]
}
```

#### Adicionar Item ao Pedido
```http
POST /api/item-pedidos
```

**Body JSON:**
```json
{
    "pedido_id": 1,
    "produto_id": 1,
    "quantidade": 2,
    "observacoes": "Sem cebola, com batata extra"
}
```

**Validações:**
- `pedido_id`: obrigatório, deve existir na tabela pedidos
- `produto_id`: obrigatório, deve existir na tabela produtos
- `quantidade`: obrigatório, numérico, mínimo 1
- `observacoes`: opcional, máximo 500 caracteres

**Funcionalidades Especiais:**
- Se o item já existir no pedido, a quantidade será somada automaticamente
- Não é possível modificar pedidos com status "entregue" ou "cancelado"
- O total do pedido é recalculado automaticamente

**Resposta:**
```json
{
    "success": true,
    "message": "Item adicionado ao pedido com sucesso!",
    "item": {
        "id": 1,
        "pedido_id": 1,
        "produto_id": 1,
        "quantidade": 2,
        "preco_unitario": "25.50",
        "subtotal": "51.00",
        "observacoes": "Sem cebola, com batata extra",
        "produto": {
            "nome": "Hambúrguer Clássico"
        }
    }
}
```

#### Exibir Item Específico
```http
GET /api/item-pedidos/{id}
```

**Resposta:**
```json
{
    "success": true,
    "item": {
        "id": 1,
        "pedido_id": 1,
        "produto_id": 1,
        "quantidade": 2,
        "preco_unitario": "25.50",
        "subtotal": "51.00",
        "observacoes": "Sem cebola",
        "produto": {
            "nome": "Hambúrguer Clássico",
            "categoria": {
                "nome": "Lanches"
            }
        },
        "pedido": {
            "mesa": {
                "numero": "01"
            },
            "usuario": {
                "nome": "João Silva"
            }
        }
    }
}
```

#### Atualizar Item
```http
PUT /api/item-pedidos/{id}
```

**Body JSON:**
```json
{
    "quantidade": 3,
    "observacoes": "Sem cebola, bem passado"
}
```

**Validações:**
- `quantidade`: numérico, mínimo 1
- `observacoes`: opcional, máximo 500 caracteres

#### Remover Item
```http
DELETE /api/item-pedidos/{id}
```

**Resposta:**
```json
{
    "success": true,
    "message": "Item removido com sucesso!"
}
```

### 2. Rotas Especializadas

#### Listar Itens de um Pedido Específico
```http
GET /api/pedidos/{pedido}/itens
```

**Resposta:**
```json
{
    "success": true,
    "itens": [
        {
            "id": 1,
            "quantidade": 2,
            "subtotal": "51.00",
            "observacoes": "Sem cebola",
            "produto": {
                "nome": "Hambúrguer Clássico",
                "preco": "25.50"
            }
        }
    ],
    "total_pedido": "51.00",
    "quantidade_itens": 1
}
```

#### Adicionar Múltiplos Itens
```http
POST /api/item-pedidos/multiplos
```

**Body JSON:**
```json
{
    "pedido_id": 1,
    "itens": [
        {
            "produto_id": 1,
            "quantidade": 2,
            "observacoes": "Sem cebola"
        },
        {
            "produto_id": 5,
            "quantidade": 2
        }
    ]
}
```

**Resposta:**
```json
{
    "success": true,
    "message": "2 itens adicionados com sucesso!",
    "itens_criados": [
        {
            "id": 1,
            "produto_id": 1,
            "quantidade": 2,
            "subtotal": "51.00"
        },
        {
            "id": 2,
            "produto_id": 5,
            "quantidade": 2,
            "subtotal": "9.00"
        }
    ],
    "total_pedido": "60.00"
}
```

#### Relatório de Itens Mais Vendidos
```http
GET /api/relatorios/itens-mais-vendidos
```

**Parâmetros de Query:**
- `limite` (opcional): número de itens no ranking (padrão: 10)
- `periodo` (opcional): filtro por período (hoje, semana, mes, ano)

**Resposta:**
```json
{
    "success": true,
    "periodo": "todos",
    "ranking": [
        {
            "produto_id": 1,
            "produto_nome": "Hambúrguer Clássico",
            "categoria": "Lanches",
            "total_quantidade": 15,
            "total_vendas": "382.50",
            "numero_pedidos": 8,
            "ticket_medio": "47.81"
        },
        {
            "produto_id": 4,
            "produto_nome": "Cerveja Pilsen",
            "categoria": "Bebidas",
            "total_quantidade": 12,
            "total_vendas": "66.00",
            "numero_pedidos": 6,
            "ticket_medio": "11.00"
        }
    ],
    "estatisticas": {
        "total_itens_vendidos": 47,
        "receita_total": "623.50",
        "ticket_medio_geral": "31.18"
    }
}
```

## Códigos de Status HTTP

- **200 OK**: Operação realizada com sucesso
- **201 Created**: Item criado com sucesso
- **400 Bad Request**: Dados inválidos ou pedido não pode ser modificado
- **401 Unauthorized**: Token de autenticação inválido
- **403 Forbidden**: Usuário sem permissão para esta operação
- **404 Not Found**: Item, pedido ou produto não encontrado
- **422 Unprocessable Entity**: Erros de validação
- **500 Internal Server Error**: Erro interno do servidor

## Exemplos de Erro

### Validação
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "produto_id": [
            "O campo produto id é obrigatório."
        ],
        "quantidade": [
            "O campo quantidade deve ser pelo menos 1."
        ]
    }
}
```

### Pedido Não Modificável
```json
{
    "success": false,
    "message": "Não é possível modificar pedido com status: entregue"
}
```

### Item Não Encontrado
```json
{
    "success": false,
    "message": "Item não encontrado"
}
```

## Fluxo de Uso Recomendado

### 1. Novo Pedido
1. Criar pedido via `/api/pedidos`
2. Adicionar itens via `/api/item-pedidos` ou `/api/item-pedidos/multiplos`
3. Consultar detalhes via `/api/pedidos/{id}/itens`

### 2. Modificação de Pedido
1. Verificar status do pedido
2. Atualizar itens existentes via `PUT /api/item-pedidos/{id}`
3. Adicionar novos itens se necessário
4. O total é recalculado automaticamente

### 3. Relatórios
1. Consultar itens mais vendidos para análises
2. Usar filtros de período para relatórios específicos

## Observações Importantes

- **Transações**: Todas as operações que modificam o total do pedido usam transações de banco
- **Duplicatas**: O sistema detecta automaticamente produtos duplicados e soma as quantidades
- **Preços**: O preço unitário é sempre capturado do produto no momento da adição do item
- **Status**: Pedidos com status "entregue" ou "cancelado" não podem ser modificados
- **Performance**: Relacionamentos são carregados automaticamente (eager loading)

## Interface Web

Acesse `/pedidos/{id}/detalhes` para uma interface completa de gestão de itens do pedido, com funcionalidades de:
- Visualização de todos os itens
- Adição de novos itens
- Edição e remoção de itens existentes
- Cálculo de totais em tempo real
- Interface responsiva e moderna
