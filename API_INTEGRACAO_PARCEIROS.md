# API de Integração para Parceiros

## EATSFOOD - LTDA
**Tecnologia que alimenta resultados**

---

## 📋 Visão Geral

Esta API permite que restaurantes parceiros integrem seus sistemas externos com a plataforma EATSFOOD, recebendo pedidos de delivery em tempo real. O parceiro mantém seu próprio catálogo de produtos e recebe apenas os pedidos realizados através do nosso app.

## 💰 Modelo de Negócio

- **Parceiro gerencia**: Produtos, preços e estoque no próprio sistema
- **Plataforma cobra**: Apenas taxa de serviço por pedido concluído
- **Parceiro recebe**: Notificação de pedidos via Webhook
- **Parceiro responde**: Aceitar/Rejeitar pedido, atualizar status

---

## 🔐 Autenticação

### 1. Geração de API Key

Cada parceiro recebe credenciais únicas:

```
API_KEY: string (64 caracteres hexadecimais)
API_SECRET: string (64 caracteres hexadecimais)
TENANT_CODE: string (código único do restaurante)
```

### 2. Autenticação de Requisições

**Header obrigatório em todas as requisições:**

```http
Authorization: Bearer {API_KEY}
X-Tenant-Code: {TENANT_CODE}
X-Signature: {HMAC_SHA256_SIGNATURE}
Content-Type: application/json
```

### 3. Geração de Assinatura HMAC

```php
// PHP
$signature = hash_hmac('sha256', $requestBody, $apiSecret);
```

```javascript
// Node.js
const crypto = require('crypto');
const signature = crypto.createHmac('sha256', apiSecret)
    .update(requestBody)
    .digest('hex');
```

```python
# Python
import hmac
import hashlib
signature = hmac.new(
    api_secret.encode(),
    request_body.encode(),
    hashlib.sha256
).hexdigest()
```

---

## 📡 Endpoints da API

### Base URL
```
Produção: https://api.eatsfood.com.br/v1/partner
Sandbox: https://sandbox.eatsfood.com.br/v1/partner
```

---

## 1️⃣ Sincronização de Produtos

### `POST /products/sync`

Sincroniza catálogo de produtos do parceiro com a plataforma.

**Request:**
```json
{
  "action": "full_sync",
  "products": [
    {
      "external_id": "PROD-001",
      "name": "Pizza Margherita",
      "description": "Molho de tomate, mussarela, manjericão",
      "price": 45.90,
      "category": "Pizzas",
      "image_url": "https://seusite.com/images/pizza-margherita.jpg",
      "available": true,
      "preparation_time": 30
    },
    {
      "external_id": "PROD-002",
      "name": "Coca-Cola 350ml",
      "description": "Refrigerante gelado",
      "price": 5.00,
      "category": "Bebidas",
      "available": true,
      "preparation_time": 0
    }
  ]
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Produtos sincronizados com sucesso",
  "data": {
    "synced": 2,
    "created": 2,
    "updated": 0,
    "errors": []
  }
}
```

---

## 2️⃣ Atualização de Disponibilidade

### `PATCH /products/{external_id}/availability`

Atualiza disponibilidade de um produto específico.

**Request:**
```json
{
  "available": false,
  "reason": "Produto em falta"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Disponibilidade atualizada",
  "data": {
    "external_id": "PROD-001",
    "available": false
  }
}
```

---

## 3️⃣ Recebimento de Pedidos (Webhook)

### `POST {seu_endpoint}/webhook/orders`

A plataforma envia pedidos para o endpoint configurado pelo parceiro.

**Payload enviado:**
```json
{
  "event": "order.created",
  "timestamp": "2025-11-24T15:30:00Z",
  "signature": "abc123...",
  "data": {
    "order_id": "ORD-12345",
    "platform_order_id": 12345,
    "customer": {
      "name": "Maria Silva",
      "phone": "11999887766",
      "email": "maria@email.com"
    },
    "delivery_address": {
      "street": "Rua das Flores",
      "number": "123",
      "complement": "Apt 45",
      "neighborhood": "Centro",
      "city": "São Paulo",
      "state": "SP",
      "zipcode": "01234-567",
      "reference": "Próximo ao mercado"
    },
    "items": [
      {
        "external_id": "PROD-001",
        "name": "Pizza Margherita",
        "quantity": 2,
        "unit_price": 45.90,
        "subtotal": 91.80,
        "notes": "Sem cebola"
      },
      {
        "external_id": "PROD-002",
        "name": "Coca-Cola 350ml",
        "quantity": 1,
        "unit_price": 5.00,
        "subtotal": 5.00,
        "notes": null
      }
    ],
    "payment": {
      "method": "credit_card",
      "status": "approved",
      "amount": 106.19
    },
    "totals": {
      "subtotal": 96.80,
      "delivery_fee": 9.00,
      "service_fee": 1.29,
      "total": 107.09
    },
    "expected_delivery_time": "2025-11-24T16:30:00Z"
  }
}
```

**Resposta esperada do parceiro (em até 30 segundos):**
```json
{
  "accepted": true,
  "estimated_preparation_time": 30,
  "message": "Pedido aceito com sucesso"
}
```

**OU em caso de rejeição:**
```json
{
  "accepted": false,
  "reason": "items_unavailable",
  "message": "Pizza Margherita indisponível no momento",
  "unavailable_items": ["PROD-001"]
}
```

---

## 4️⃣ Atualização de Status do Pedido

### `POST /orders/{order_id}/status`

Parceiro atualiza status do pedido conforme preparo/entrega.

**Request:**
```json
{
  "status": "preparing",
  "message": "Pedido em preparo",
  "estimated_ready_time": "2025-11-24T16:00:00Z"
}
```

**Status disponíveis:**
- `confirmed` - Pedido confirmado pelo restaurante
- `preparing` - Em preparo
- `ready` - Pronto para retirada/entrega
- `out_for_delivery` - Saiu para entrega
- `delivered` - Entregue ao cliente
- `cancelled` - Cancelado

**Response 200:**
```json
{
  "success": true,
  "message": "Status atualizado",
  "data": {
    "order_id": "ORD-12345",
    "status": "preparing",
    "updated_at": "2025-11-24T15:35:00Z"
  }
}
```

---

## 5️⃣ Consulta de Pedidos

### `GET /orders`

Lista pedidos do parceiro com filtros.

**Query Parameters:**
- `status` - Filtrar por status
- `date_from` - Data inicial (YYYY-MM-DD)
- `date_to` - Data final (YYYY-MM-DD)
- `page` - Página (padrão: 1)
- `per_page` - Itens por página (padrão: 20, máx: 100)

**Response 200:**
```json
{
  "success": true,
  "data": {
    "orders": [...],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 150,
      "total_pages": 8
    }
  }
}
```

---

## 🔔 Webhooks

### Configuração

Parceiro deve fornecer URL para receber notificações:
```
https://seu-sistema.com/webhooks/myd-delivery
```

### Eventos disponíveis:

1. `order.created` - Novo pedido recebido
2. `order.payment.confirmed` - Pagamento confirmado
3. `order.cancelled` - Pedido cancelado pelo cliente
4. `delivery.assigned` - Entregador atribuído
5. `delivery.picked_up` - Pedido coletado pelo entregador

### Validação de Webhook

Toda requisição webhook inclui assinatura HMAC no header:

```http
X-Webhook-Signature: {HMAC_SHA256}
X-Webhook-Timestamp: {UNIX_TIMESTAMP}
```

**Validação obrigatória:**
```php
$payload = file_get_contents('php://input');
$receivedSignature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'];
$timestamp = $_SERVER['HTTP_X_WEBHOOK_TIMESTAMP'];

// Verificar se timestamp não está muito antigo (replay attack)
if (time() - $timestamp > 300) { // 5 minutos
    throw new Exception('Webhook expirado');
}

$expectedSignature = hash_hmac('sha256', $timestamp . $payload, $apiSecret);

if (!hash_equals($expectedSignature, $receivedSignature)) {
    throw new Exception('Assinatura inválida');
}
```

---

## 🛡️ Segurança

### Rate Limiting
- **Endpoints de consulta**: 100 requisições/minuto
- **Endpoints de atualização**: 60 requisições/minuto
- **Webhooks recebidos**: Sem limite (controle do parceiro)

### IP Whitelist (Opcional)
Parceiro pode configurar IPs permitidos no painel administrativo.

### Retry Policy (Webhooks)
Em caso de falha ao enviar webhook:
- Tentativa 1: Imediato
- Tentativa 2: Após 1 minuto
- Tentativa 3: Após 5 minutos
- Tentativa 4: Após 15 minutos
- Tentativa 5: Após 1 hora

Após 5 falhas, webhook é marcado como "failed" e gera alerta.

---

## 📊 Monitoramento e Logs

### Dashboard do Parceiro

Acesso web em: `https://partner.eatsfood.com.br`

**Métricas disponíveis:**
- Pedidos recebidos (tempo real)
- Taxa de aceitação de pedidos
- Tempo médio de preparo
- Avaliações dos clientes
- Faturamento e comissões
- Status de sincronização
- Logs de requisições (últimos 30 dias)

### Logs de API

Todas as requisições são logadas:
```json
{
  "request_id": "uuid-v4",
  "timestamp": "2025-11-24T15:30:00Z",
  "method": "POST",
  "endpoint": "/products/sync",
  "status_code": 200,
  "response_time_ms": 145,
  "ip_address": "203.0.113.1"
}
```

---

## 🚨 Códigos de Erro

| Código | Descrição | Solução |
|--------|-----------|---------|
| 401 | Unauthorized | Verificar API_KEY e assinatura HMAC |
| 403 | Forbidden | Tenant code inválido ou suspenso |
| 404 | Not Found | Recurso não encontrado |
| 422 | Validation Error | Verificar campos obrigatórios |
| 429 | Too Many Requests | Aguardar rate limit |
| 500 | Internal Server Error | Contatar suporte técnico |
| 503 | Service Unavailable | Sistema em manutenção |

**Formato de resposta de erro:**
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Campos obrigatórios ausentes",
    "details": {
      "missing_fields": ["name", "price"]
    }
  },
  "request_id": "uuid-v4"
}
```

---

## 🧪 Ambiente de Testes (Sandbox)

### Credenciais de teste:
```
API_KEY: test_1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef
API_SECRET: test_abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890
TENANT_CODE: TEST_RESTAURANT
```

### Cartões de teste:
```
Aprovado: 4111 1111 1111 1111 | CVV: 123 | Validade: 12/30
Recusado: 4000 0000 0000 0002 | CVV: 123 | Validade: 12/30
```

---

## 📞 Suporte Técnico

- **Email**: dev-support@eatsfood.com.br
- **WhatsApp**: +55 11 98765-4321
- **Portal**: https://developers.eatsfood.com.br
- **Status da API**: https://status.eatsfood.com.br
- **SLA de Resposta**: 4 horas úteis

---

## 📦 SDKs Oficiais

### PHP
```bash
composer require eatsfood/partner-sdk-php
```

### Node.js
```bash
npm install @eatsfood/partner-sdk
```

### Python
```bash
pip install eatsfood-partner
```

---

## 🔄 Changelog

### v1.0.0 (2025-11-24)
- Lançamento inicial da API
- Endpoints de sincronização de produtos
- Sistema de webhooks para pedidos
- Autenticação HMAC SHA256
- Dashboard de monitoramento

---

## 📝 Exemplo de Integração Completa

```php
<?php
// exemplo-integracao.php

require 'vendor/autoload.php';

use Eatsfood\PartnerSDK\Client;

$client = new Client([
    'api_key' => 'seu_api_key',
    'api_secret' => 'seu_api_secret',
    'tenant_code' => 'seu_tenant_code',
    'environment' => 'production' // ou 'sandbox'
]);

// 1. Sincronizar produtos
$produtos = [
    [
        'external_id' => 'PROD-001',
        'name' => 'Pizza Margherita',
        'price' => 45.90,
        'available' => true
    ]
];

$result = $client->products()->sync($produtos);

// 2. Configurar webhook para receber pedidos
$webhookUrl = 'https://seu-sistema.com/webhooks/pedidos';
$client->webhooks()->configure($webhookUrl, [
    'order.created',
    'order.payment.confirmed'
]);

// 3. Processar pedido recebido via webhook
// webhook-receiver.php
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'];

if ($client->webhooks()->validate($payload, $signature)) {
    $pedido = json_decode($payload, true);
    
    // Processar pedido no seu sistema
    $aceito = processarPedido($pedido['data']);
    
    if ($aceito) {
        http_response_code(200);
        echo json_encode([
            'accepted' => true,
            'estimated_preparation_time' => 30
        ]);
    }
}

// 4. Atualizar status do pedido
$client->orders()->updateStatus('ORD-12345', [
    'status' => 'preparing',
    'message' => 'Pedido em preparo'
]);
```

---

## ✅ Checklist de Go-Live

- [ ] Credenciais de produção geradas
- [ ] Produtos sincronizados e testados
- [ ] Webhook configurado e validado
- [ ] Testes de pedidos completos (sandbox)
- [ ] Fluxo de aceitar/rejeitar pedidos implementado
- [ ] Atualização de status funcionando
- [ ] Monitoramento configurado
- [ ] Equipe treinada
- [ ] Contato de suporte salvo
- [ ] Plano de contingência definido

---

**Versão**: 1.0.0  
**Última atualização**: 24/11/2025  
**Licença**: Proprietário - EATSFOOD - LTDA  
**Slogan**: Tecnologia que alimenta resultados

---

