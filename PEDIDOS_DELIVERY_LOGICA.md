# 📋 Lógica de Pedidos e Delivery - Sistema MyD Bar

## 🎯 Visão Geral do Fluxo

### 1. **Tipos de Pedido**
- **Mesa**: Pedidos feitos presencialmente no restaurante
- **Balcão**: Pedidos para retirada no local
- **Delivery**: Pedidos com entrega

---

## 📦 Estrutura de Dados

### **Tabela: pedidos**
```
- id
- mesa_id (nullable) - Se for pedido de mesa
- usuario_id - Garçom/atendente responsável
- entregador_id (nullable) - Entregador atribuído
- status: pendente, em_preparo, pronto, entregue, cancelado
- total
- subtotal
- taxa_entrega
- forma_pagamento
- tipo_entrega
- observacoes
- tenant_code
- created_at / updated_at
```

### **Tabela: deliveries**
```
- id
- pedido_id
- cliente_id
- cliente_nome
- cliente_telefone
- cliente_email
- endereco_rua, numero, complemento, bairro, cidade, cep
- endereco_referencia
- destino_latitude / destino_longitude
- origem_latitude / origem_longitude (coordenadas do restaurante)
- taxa_entrega
- tempo_estimado
- tempo_real
- entregador_id (nullable)
- status: pendente, aguardando_entregador, saiu_entrega, entregue, cancelado
- disponivel_plataforma (boolean) - Se está disponível para entregadores autônomos
- entregadores_notificados (JSON array)
- entregadores_recusados (JSON array)
- raio_busca_km
- valor_entregador
- observacoes
- tenant_code
```

---

## 🔄 Fluxo de Pedido Delivery

### **PASSO 1: Criação do Pedido**
```
Cliente faz pedido no app → PedidoController@storeFromCliente
```
**Lógica:**
1. Valida dados do cliente (nome, telefone, endereço)
2. Cria registro em `pedidos` com status **pendente**
3. Cria registro em `deliveries` vinculado ao pedido
4. Salva coordenadas de origem (restaurante) e destino (cliente)
5. Retorna pedido + delivery criados

**Campos importantes:**
- `pedido.status = 'pendente'`
- `delivery.status = 'pendente'`
- `delivery.disponivel_plataforma = false` (inicialmente)

---

### **PASSO 2: Preparo do Pedido**
```
Restaurante confirma pedido → status muda para 'em_preparo'
```
**Lógica:**
1. Pedido passa de **pendente** → **em_preparo**
2. **ALERTA**: Se delivery não tem entregador atribuído, mostra aviso na tela
3. Sistema pode notificar entregadores da empresa

**View:** `resources/views/pedidos/detalhes.blade.php`
```blade
@if($pedido->delivery && $pedido->status == 'em_preparo' && !$pedido->entregador_id)
    <div class="alert alert-warning">
        Atenção: Pedido sem entregador!
        <a href="{{ route('deliveries.index') }}">Atribuir Entregador</a>
    </div>
@endif
```

---

### **PASSO 3: Atribuição de Entregador**

#### **Opção A: Entregador Próprio da Empresa**
```
DeliveryController@atribuirEntregador
```
**Lógica:**
1. Seleciona entregador disponível da empresa
2. Atribui: `delivery.entregador_id = X`
3. Notifica entregador

#### **Opção B: Entregadores Autônomos (Plataforma)**
```
DeliveryController@disponibilizarPlataforma
```
**Lógica:**
1. Marca: `delivery.disponivel_plataforma = true`
2. Sistema busca entregadores próximos usando `raio_busca_km`
3. Notifica múltiplos entregadores via FCM
4. Primeiro que aceitar pega a entrega

**Método importante:** `Delivery@buscarEntregadoresProximos()`
```php
// Busca entregadores dentro do raio, excluindo já notificados/recusados
$entregadores = Entregador::where('status', 'ativo')
    ->where('disponivel', true)
    ->whereNotIn('id', $delivery->entregadores_recusados ?? [])
    ->get()
    ->filter(function($entregador) {
        // Calcula distância usando coordenadas
        return $distancia <= $this->raio_busca_km;
    });
```

---

### **PASSO 4: Pedido Pronto**
```
Cozinha finaliza → status muda para 'pronto'
```
**Lógica:**
1. Pedido passa de **em_preparo** → **pronto**
2. Se já tem entregador atribuído, notifica ele que pode buscar
3. **ALERTA** exibido: "Pedido Pronto para Entrega!"

**View:**
```blade
@if($pedido->delivery && $pedido->status == 'pronto' && $pedido->entregador_id)
    <div class="alert alert-info">
        Pedido Pronto para Entrega!
        Entregador <strong>{{ $pedido->entregador->nome }}</strong> foi notificado.
    </div>
@endif
```

---

### **PASSO 5: Saída para Entrega**
```
Entregador confirma retirada → delivery.status = 'saiu_entrega'
```
**Lógica:**
1. Entregador marca no app que pegou o pedido
2. `delivery.status = 'saiu_entrega'`
3. Cliente pode acompanhar localização em tempo real
4. App envia atualizações de localização via API

**API do App Entregador:**
```
POST /api/entregadores/localizacao
Body: { latitude, longitude }
```

---

### **PASSO 6: Entrega Concluída**
```
Entregador confirma entrega → pedido.status = 'entregue'
```
**Lógica:**
1. `pedido.status = 'entregue'`
2. `delivery.status = 'entregue'`
3. Calcula tempo real de entrega
4. Cliente pode avaliar entregador
5. Pagamento da comissão do entregador é registrado

---

## 🎛️ Controllers Principais

### **PedidoController**
- `index()` - Lista todos os pedidos (mesa + delivery)
- `storeFromCliente()` - Cria pedido de delivery pelo app
- `show($pedido)` - Detalhes do pedido (carrega delivery e entregador)
- `update()` - Atualiza status do pedido
- `cancelarPedido()` - Cancela pedido (cliente pode cancelar antes de sair para entrega)

### **DeliveryController**
- `index()` - Lista deliveries em andamento
- `atribuirEntregador()` - Atribui entregador específico
- `disponibilizarPlataforma()` - Libera para entregadores autônomos
- `aceitarEntrega()` - Entregador aceita a entrega
- `recusarEntrega()` - Entregador recusa e notifica próximos
- `iniciarEntrega()` - Marca que saiu para entrega
- `finalizarEntrega()` - Confirma entrega concluída

---

## 📱 APIs para Apps

### **App Cliente (app-cliente)**
```
GET  /api/pedidos - Lista pedidos do cliente logado
POST /api/pedidos-public - Cria novo pedido delivery
GET  /api/pedidos/{id}/status - Acompanha status do pedido
POST /api/pedidos/{id}/cancelar - Cancela pedido
GET  /api/pedidos/{id}/rastreamento - Localização do entregador
```

### **App Entregador (app-entregador)**
```
GET  /api/entregadores/entregas-disponiveis - Lista entregas disponíveis na plataforma
POST /api/entregadores/entregas/{id}/aceitar - Aceita uma entrega
POST /api/entregadores/entregas/{id}/recusar - Recusa uma entrega
GET  /api/entregadores/entregas-ativas - Lista minhas entregas em andamento
POST /api/entregadores/entregas/{id}/iniciar - Confirma que pegou o pedido
POST /api/entregadores/entregas/{id}/finalizar - Confirma entrega concluída
POST /api/entregadores/localizacao - Atualiza localização em tempo real
```

---

## 🚨 Alertas e Validações Importantes

### **1. Pedido sem Entregador**
```blade
@if($pedido->delivery && $pedido->status == 'em_preparo' && !$pedido->entregador_id)
    <!-- Alerta amarelo: atribua um entregador -->
@endif
```

### **2. Validação de Cancelamento**
```php
// Cliente SÓ pode cancelar se ainda não saiu para entrega
if ($pedido->delivery && in_array($pedido->delivery->status, ['saiu_entrega', 'entregue', 'cancelado'])) {
    return response()->json(['error' => 'Não pode cancelar pedido em rota']);
}
```

### **3. Coordenadas Obrigatórias**
```php
// Delivery SEMPRE precisa de coordenadas do restaurante e cliente
'origem_latitude' => $empresa->latitude,
'origem_longitude' => $empresa->longitude,
'destino_latitude' => $request->latitude, // Do cliente
'destino_longitude' => $request->longitude,
```

### **4. Notificação de Múltiplos Entregadores**
```php
// Quando disponibiliza na plataforma
$entregadores = $delivery->buscarEntregadoresProximos();
foreach ($entregadores as $entregador) {
    if (!in_array($entregador->id, $delivery->entregadores_notificados)) {
        // Envia notificação FCM
        // Adiciona ao array de notificados
    }
}
```

---

## 📊 Views Principais

### **Pedidos**
- `resources/views/pedidos/index.blade.php` - Lista todos os pedidos
- `resources/views/pedidos/detalhes.blade.php` - Detalhes completos do pedido
- `resources/views/pedidos/edit.blade.php` - Edição do pedido
- `resources/views/pedidos/comanda.blade.php` - Impressão da comanda para cozinha

### **Deliveries**
- `resources/views/deliveries/index.blade.php` - Gestão de deliveries
- (Views antigas podem estar em desuso - sistema migrou para usar pedidos diretamente)

---

## 🔧 Campos Calculados e Métodos

### **Delivery Model**
```php
// Calcula endereço completo
public function getEnderecoCompletoAttribute() {
    return "{$this->endereco_rua}, {$this->endereco_numero} - {$this->endereco_bairro}";
}

// Busca entregadores próximos usando raio
public function buscarEntregadoresProximos() { ... }

// Calcula distância entre dois pontos
public function calcularDistancia($lat1, $lon1, $lat2, $lon2) { ... }

// Notifica entregadores disponíveis
public function notificarEntregadores() { ... }
```

---

## ⚠️ Problemas Conhecidos e Soluções

### **Problema 1: Pedido sem entregador fica travado**
**Solução:** Alerta visual na tela de detalhes + botão direto para atribuir

### **Problema 2: Cliente cancela com entregador a caminho**
**Solução:** Validação que impede cancelamento após status 'saiu_entrega'

### **Problema 3: Múltiplos entregadores aceitam mesma entrega**
**Solução:** 
```php
// Lock de transação no banco
DB::transaction(function() use ($delivery, $entregadorId) {
    $delivery = Delivery::lockForUpdate()->find($delivery->id);
    if ($delivery->entregador_id === null) {
        $delivery->entregador_id = $entregadorId;
        $delivery->save();
    }
});
```

### **Problema 4: Coordenadas não são salvas**
**Solução:** Sempre pegar do request ou da empresa:
```php
'origem_latitude' => $empresa->latitude ?? null,
'origem_longitude' => $empresa->longitude ?? null,
```

---

## 🎯 Estatísticas do Dashboard

```php
$estatisticas = [
    'em_preparo' => Pedido::whereHas('delivery')->where('status', 'em_preparo')->count(),
    'aguardando_entregador' => Pedido::whereHas('delivery')
        ->where('status', 'em_preparo')
        ->whereNull('entregador_id')
        ->count(),
    'prontos' => Pedido::whereHas('delivery')->where('status', 'pronto')->count(),
    'em_rota' => Delivery::where('status', 'saiu_entrega')->count(),
    'entregues' => Pedido::whereHas('delivery')
        ->where('status', 'entregue')
        ->whereDate('updated_at', today())
        ->count(),
    'faturamento' => Pedido::whereHas('delivery')
        ->where('status', 'entregue')
        ->whereDate('updated_at', today())
        ->sum('total')
];
```

---

## 🔐 Permissões e Acesso

### **Garçom/Atendente**
- Pode criar pedidos de mesa e balcão
- Pode ver todos os pedidos
- NÃO pode atribuir entregadores

### **Gerente/Admin**
- Acesso total a pedidos e deliveries
- Pode atribuir entregadores
- Pode disponibilizar na plataforma
- Pode cancelar pedidos

### **Entregador**
- Vê apenas entregas disponíveis e suas próprias
- Pode aceitar/recusar entregas
- Pode atualizar status de suas entregas

### **Cliente (App)**
- Vê apenas seus próprios pedidos
- Pode criar novos pedidos
- Pode cancelar antes de sair para entrega
- Pode rastrear entrega em tempo real

---

## 📝 Melhorias Futuras Sugeridas

1. **Agendamento de Entregas**: Cliente escolher horário futuro
2. **Estimativa Dinâmica**: Calcular tempo baseado em distância real
3. **Histórico de Rotas**: Salvar trajeto GPS do entregador
4. **Taxa Dinâmica**: Variar taxa conforme distância/horário
5. **Multi-parada**: Entregador pegar múltiplos pedidos na mesma rota
6. **Zona de Entrega**: Definir polígonos de área de cobertura
7. **Priorização**: Pedidos urgentes ou VIP
8. **Chat em Tempo Real**: Cliente ↔ Entregador

---

✅ **Última atualização:** Novembro 2025
