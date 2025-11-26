# Integração Mercado Pago - App Cliente

## Visão Geral

O sistema agora suporta dois modos de recebimento de pagamento que podem ser configurados por restaurante:

- **MANUAL (Tradicional)**: Cliente paga na entrega ou no local
- **AUTOMÁTICO (Mercado Pago)**: Cliente paga online antes da entrega via PIX ou Cartão

## 1. Consultar Configuração do Restaurante

Antes de exibir as opções de pagamento, o app deve consultar qual tipo o restaurante aceita:

### Endpoint
```
GET /api/app/restaurante/{tenantCode}/configuracoes-pagamento
```

### Resposta
```json
{
  "success": true,
  "configuracoes": {
    "tipo_recebimento": "automatico",
    "aceita_mp": true,
    "formas_pagamento": [
      "mercado_pago_pix",
      "mercado_pago_cartao"
    ]
  }
}
```

**Ou para restaurante MANUAL:**
```json
{
  "success": true,
  "configuracoes": {
    "tipo_recebimento": "manual",
    "aceita_mp": false,
    "formas_pagamento": [
      "dinheiro",
      "cartao_entrega",
      "pix_manual"
    ]
  }
}
```

## 2. Fluxo no App Cliente

### MANUAL (Tradicional)

```
1. Cliente finaliza pedido
2. App mostra opções:
   - Dinheiro (com campo de troco)
   - Cartão na entrega
   - PIX (dados do restaurante)
3. Pedido enviado com status "pendente"
4. Cliente paga na entrega
5. Restaurante confirma pagamento no caixa
```

### AUTOMÁTICO (Mercado Pago)

```
1. Cliente finaliza pedido
2. App detecta tipo_recebimento = "automatico"
3. Mostra apenas opção "PIX via Mercado Pago"
4. Cliente clica em "Pagar com PIX"
5. App chama API para gerar QR Code
6. Cliente escaneia e paga
7. Webhook confirma pagamento
8. Pedido aprovado e enviado para preparo
```

## 3. Criar Pagamento PIX

### Endpoint
```
POST /api/mercadopago/pix
```

### Payload
```json
{
  "pedido_id": 123,
  "email": "cliente@email.com",
  "cpf": "12345678900"
}
```

### Resposta Sucesso
```json
{
  "success": true,
  "payment": {
    "id": 456,
    "mp_payment_id": "789012",
    "status": "pending",
    "amount": 45.50,
    "pix": {
      "qr_code": "data:image/png;base64,iVBORw0KG...",
      "qr_code_url": "https://...",
      "copy_paste": "00020126580014br.gov.bcb.pix..."
    },
    "expires_at": "2025-11-25T15:30:00Z"
  }
}
```

## 4. Exibir QR Code no App

**Layout sugerido:**

```
┌─────────────────────────────┐
│  Pague com PIX              │
│                             │
│  ┌─────────────────────┐   │
│  │                     │   │
│  │   [QR CODE IMAGE]   │   │
│  │                     │   │
│  └─────────────────────┘   │
│                             │
│  R$ 45,50                   │
│                             │
│  Válido até 15:30          │
│                             │
│  [Copiar código PIX]       │
│  [Verificar pagamento]     │
└─────────────────────────────┘
```

**Componentes:**
- Imagem do QR Code: Use `qr_code` (base64) ou `qr_code_url`
- Botão "Copiar": Copia `copy_paste` para clipboard
- Timer: Exibe countdown até `expires_at`

## 5. Verificar Status do Pagamento

Cliente pode clicar em "Verificar Pagamento" para conferir se foi aprovado.

### Endpoint
```
GET /api/mercadopago/payment/{payment_id}/status
```

### Resposta
```json
{
  "success": true,
  "payment": {
    "id": 456,
    "status": "approved",
    "amount": 45.50,
    "paid_at": "2025-11-25T14:25:30Z",
    "is_approved": true,
    "is_pending": false
  }
}
```

**Status possíveis:**
- `pending`: Aguardando pagamento
- `approved`: ✅ Pago com sucesso
- `rejected`: ❌ Rejeitado
- `cancelled`: Cancelado

## 6. Polling (Atualização Automática)

O app deve fazer polling a cada 3-5 segundos para verificar se o pagamento foi aprovado:

```javascript
// Pseudo-código
let checkInterval = setInterval(async () => {
  const response = await checkPaymentStatus(paymentId);
  
  if (response.payment.is_approved) {
    clearInterval(checkInterval);
    // Mostrar mensagem de sucesso
    // Redirecionar para tela de acompanhamento do pedido
  }
  
  if (isExpired(response.payment.expires_at)) {
    clearInterval(checkInterval);
    // Mostrar mensagem de expiração
    // Oferecer gerar novo QR Code
  }
}, 5000); // 5 segundos
```

## 7. Simulação de Pagamento (Teste)

Para testar sem pagar de verdade:

### Endpoint
```
POST /api/mercadopago/payment/{payment_id}/simulate-approval
```

### Resposta
```json
{
  "success": true,
  "message": "Pagamento aprovado com sucesso (simulação)",
  "payment": {
    "id": 456,
    "status": "approved",
    "paid_at": "2025-11-25T14:30:00Z"
  }
}
```

## 8. Tabelas Populadas

Quando pagamento for aprovado via Mercado Pago, o sistema popula DUAS tabelas automaticamente:

### Tabela `payments` (Financeiro)
- Controle de receitas, taxas, repasses
- `platform_fee`: Taxa da gestora (%)
- `delivery_fee`: Taxa do entregador
- `net_amount`: Líquido para o restaurante

### Tabela `pagamentos` (Operacional)
- Controle de caixa do restaurante
- `forma_pagamento`: "pix"
- `status`: "confirmado"
- `valor`: Valor total pago

## 9. Exemplo Completo de Implementação

```javascript
// 1. Verificar tipo de pagamento do restaurante
async function checkoutPedido(tenantCode) {
  const config = await fetch(`/api/app/restaurante/${tenantCode}/configuracoes-pagamento`);
  
  if (config.aceita_mp) {
    // Mostrar opção Mercado Pago
    mostrarPagamentoMercadoPago();
  } else {
    // Mostrar opções tradicionais
    mostrarPagamentoTradicional();
  }
}

// 2. Gerar QR Code PIX
async function gerarQRCodePix(pedidoId, clienteEmail, clienteCpf) {
  const response = await fetch('/api/mercadopago/pix', {
    method: 'POST',
    body: JSON.stringify({
      pedido_id: pedidoId,
      email: clienteEmail,
      cpf: clienteCpf
    })
  });
  
  const data = await response.json();
  
  if (data.success) {
    exibirQRCode(data.payment);
    iniciarPolling(data.payment.id);
  }
}

// 3. Polling de status
function iniciarPolling(paymentId) {
  const interval = setInterval(async () => {
    const status = await fetch(`/api/mercadopago/payment/${paymentId}/status`);
    const data = await status.json();
    
    if (data.payment.is_approved) {
      clearInterval(interval);
      mostrarSucesso();
      navegarParaAcompanhamento();
    }
  }, 5000);
}
```

## 10. Mensagens para o Cliente

### Aguardando Pagamento
```
⏳ Aguardando pagamento...
Escaneie o QR Code acima ou copie o código PIX
Válido até 15:30 (10 minutos restantes)
```

### Pagamento Aprovado
```
✅ Pagamento confirmado!
Seu pedido está sendo preparado.
Tempo estimado: 45 minutos
```

### Pagamento Expirado
```
⌛ QR Code expirado
Gere um novo código para continuar
[Gerar novo QR Code]
```

## 11. Configuração no Painel Admin

O gestor configura o tipo de recebimento em:

```
Admin → Empresas → Editar → Recebimento de Pagamentos
```

Opções:
- ⭕ **Manual (Tradicional)**: Cliente paga na entrega
- ⭕ **Automático via Mercado Pago**: Cliente paga online

---

## Resumo da Integração

| Tipo          | Formas de Pagamento        | Quando Paga        | Registro |
|---------------|---------------------------|-------------------|----------|
| **Manual**    | Dinheiro, Cartão, PIX     | Na entrega        | Caixa    |
| **Automático**| PIX MP, Cartão MP         | Antes da entrega  | Ambas    |

✅ **Sistema pronto para uso!**
