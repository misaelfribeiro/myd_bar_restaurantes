#!/bin/bash

# Script de teste das notificações push
# Use este script para testar as APIs de notificação

SERVER="http://192.168.15.9"

echo "🔔 Teste de Notificações Push - MyD Bar & Restaurantes"
echo "=================================================="
echo ""

# Cor para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Salvar Token
echo -e "${YELLOW}1. Salvando Token FCM...${NC}"
curl -X POST "$SERVER/api/notificacao/salvar-token" \
  -H "Content-Type: application/json" \
  -d '{
    "token": "SEU_TOKEN_FCM_AQUI",
    "usuario_id": 1,
    "cliente_id": 1
  }' \
  -w "\nStatus: %{http_code}\n\n"

# 2. Enviar Notificação Simples
echo -e "${YELLOW}2. Enviando Notificação Simples...${NC}"
curl -X POST "$SERVER/api/notificacao/enviar" \
  -H "Content-Type: application/json" \
  -d '{
    "token": "SEU_TOKEN_FCM_AQUI",
    "titulo": "Teste",
    "mensagem": "Isso é uma notificação de teste!",
    "pedido_id": "123",
    "action": "teste"
  }' \
  -w "\nStatus: %{http_code}\n\n"

# 3. Enviar Notificação - Pedido Pronto
echo -e "${YELLOW}3. Notificando Pedido Pronto...${NC}"
curl -X POST "$SERVER/api/notificacao/pedido-pronto" \
  -H "Content-Type: application/json" \
  -d '{
    "token": "SEU_TOKEN_FCM_AQUI",
    "pedido_id": 123,
    "numero_mesa": 5
  }' \
  -w "\nStatus: %{http_code}\n\n"

# 4. Enviar Notificação - Delivery Aceito
echo -e "${YELLOW}4. Notificando Delivery Aceito...${NC}"
curl -X POST "$SERVER/api/notificacao/delivery-aceito" \
  -H "Content-Type: application/json" \
  -d '{
    "token": "SEU_TOKEN_FCM_AQUI",
    "pedido_id": 123,
    "motorista": "João"
  }' \
  -w "\nStatus: %{http_code}\n\n"

# 5. Enviar Notificação - Delivery Entregue
echo -e "${YELLOW}5. Notificando Delivery Entregue...${NC}"
curl -X POST "$SERVER/api/notificacao/delivery-entregue" \
  -H "Content-Type: application/json" \
  -d '{
    "token": "SEU_TOKEN_FCM_AQUI",
    "pedido_id": 123
  }' \
  -w "\nStatus: %{http_code}\n\n"

# 6. Enviar para Múltiplos Tokens
echo -e "${YELLOW}6. Enviando para Múltiplos Dispositivos...${NC}"
curl -X POST "$SERVER/api/notificacao/enviar-multipla" \
  -H "Content-Type: application/json" \
  -d '{
    "tokens": [
      "TOKEN_1",
      "TOKEN_2",
      "TOKEN_3"
    ],
    "titulo": "Oferta Especial!",
    "mensagem": "Novo coupon disponível",
    "action": "promocao"
  }' \
  -w "\nStatus: %{http_code}\n\n"

echo -e "${GREEN}✅ Testes Concluídos!${NC}"
