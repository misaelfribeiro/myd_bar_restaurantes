# 📱 IMPLEMENTAÇÃO DO MODO OFFLINE - MyD Bar & Restaurantes

## ✅ Arquivos Criados

1. **`public/sw.js`** - Service Worker para cache e sincronização
2. **`public/js/offline-manager.js`** - Gerenciador de funcionalidade offline
3. **`public/offline.html`** - Página exibida quando offline
4. **`public/manifest.json`** - Manifesto PWA
5. **`resources/views/layouts/app.blade.php`** - Atualizado com suporte offline

## 🔧 Próximos Passos para Completar a Implementação

### 1. Adicionar Métodos de Sincronização nos Controllers

#### **PedidoController** (`app/Http/Controllers/PedidoController.php`)

Adicione este método:

```php
/**
 * Sincronizar pedido criado offline
 */
public function syncOffline(Request $request)
{
    try {
        $validated = $request->validate([
            'mesa_id' => 'required|exists:mesas,id',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
            'itens.*.preco_unitario' => 'required|numeric|min:0',
            'itens.*.observacoes' => 'nullable|string',
            'observacoes' => 'nullable|string'
        ]);

        DB::beginTransaction();

        // Criar pedido
        $pedido = Pedido::create([
            'mesa_id' => $validated['mesa_id'],
            'usuario_id' => auth()->id(),
            'status' => 'pendente',
            'observacoes' => $validated['observacoes'] ?? null,
            'data_hora' => now()
        ]);

        // Adicionar itens
        $totalPedido = 0;
        foreach ($validated['itens'] as $item) {
            $subtotal = $item['quantidade'] * $item['preco_unitario'];
            $totalPedido += $subtotal;

            ItemPedido::create([
                'pedido_id' => $pedido->id,
                'produto_id' => $item['produto_id'],
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $item['preco_unitario'],
                'subtotal' => $subtotal,
                'observacoes' => $item['observacoes'] ?? null
            ]);
        }

        // Atualizar total do pedido
        $pedido->update(['total' => $totalPedido]);

        // Atualizar status da mesa
        $mesa = Mesa::find($validated['mesa_id']);
        if ($mesa && $mesa->status === 'livre') {
            $mesa->update(['status' => 'ocupada']);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Pedido sincronizado com sucesso',
            'pedido' => $pedido->load('itens.produto')
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erro ao sincronizar pedido offline: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Erro ao sincronizar pedido',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Obter dados para cache offline
 */
public function getCacheData()
{
    return Produto::with('categoria')
        ->where('disponivel', true)
        ->get();
}
```

#### **ApiPagamentoController** (`app/Http/Controllers/Api/PagamentoController.php`)

Adicione este método:

```php
/**
 * Sincronizar pagamento registrado offline
 */
public function syncOffline(Request $request)
{
    try {
        $validated = $request->validate([
            'pedido_id' => 'nullable|exists:pedidos,id',
            'mesa_id' => 'nullable|exists:mesas,id',
            'tipo' => 'required|in:pedido,mesa',
            'formas_pagamento' => 'required|array|min:1',
            'formas_pagamento.*.forma' => 'required|in:dinheiro,cartao_credito,cartao_debito,pix,vale_refeicao',
            'formas_pagamento.*.valor' => 'required|numeric|min:0.01',
            'valor_recebido' => 'nullable|numeric|min:0',
            'troco' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();

        if ($validated['tipo'] === 'pedido') {
            $result = $this->processarPagamentoPedido(
                Pedido::findOrFail($validated['pedido_id']),
                $request
            );
        } else {
            $result = $this->processarPagamentoMesa(
                Mesa::findOrFail($validated['mesa_id']),
                $request
            );
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Pagamento sincronizado com sucesso',
            'data' => $result->getData()
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erro ao sincronizar pagamento offline: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Erro ao sincronizar pagamento',
            'error' => $e->getMessage()
        ], 500);
    }
}
```

#### **MesaController** (`app/Http/Controllers/MesaController.php`)

Adicione este método:

```php
/**
 * Obter dados de mesas para cache offline
 */
public function getCacheData()
{
    return Mesa::all();
}
```

#### **CategoriaController** (`app/Http/Controllers/CategoriaController.php`)

Adicione este método:

```php
/**
 * Obter dados de categorias para cache offline
 */
public function getCacheData()
{
    return Categoria::all();
}
```

### 2. Criar Ícones do PWA

Execute no terminal:

```bash
# Criar diretório de imagens
mkdir public/images

# Você precisará criar ícones nos seguintes tamanhos:
# 72x72, 96x96, 128x128, 144x144, 152x152, 192x192, 384x384, 512x512

# Use uma ferramenta online como https://www.pwabuilder.com/imageGenerator
# ou https://realfavicongenerator.net/
```

### 3. Integrar com o Modo Garçom

Atualize o arquivo `resources/views/garcom/mesas.blade.php` para usar o modo offline:

```javascript
// Adicionar ao final do arquivo, antes do </script>

// Verificar se está online antes de enviar pedido
async function enviarPedido() {
    const pedidoData = {
        mesa_id: mesaAtual,
        itens: carrinho,
        observacoes: document.getElementById('observacoes')?.value
    };

    if (navigator.onLine) {
        // Enviar normalmente via AJAX
        try {
            const response = await fetch('/api/pedidos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(pedidoData)
            });

            if (response.ok) {
                alert('Pedido enviado com sucesso!');
                limparCarrinho();
            }
        } catch (error) {
            // Se falhar, salvar offline
            await window.offlineManager.savePedidoOffline(pedidoData);
        }
    } else {
        // Salvar offline
        await window.offlineManager.savePedidoOffline(pedidoData);
    }
}
```

### 4. Testar o Modo Offline

1. **Abra o DevTools** (F12)
2. Vá para a aba **Application** (Chrome) ou **Storage** (Firefox)
3. Em **Service Workers**, verifique se o SW está registrado
4. Em **Cache Storage**, veja os recursos cacheados
5. Em **IndexedDB**, veja o banco MydBarDB

**Simular Modo Offline:**
1. DevTools > Network > Throttling > Offline
2. Ou desconecte o cabo de rede / desative Wi-Fi

### 5. Melhorias Opcionais

#### Adicionar botão de sincronização manual:

```html
<button onclick="window.offlineManager.syncPendingData()" class="btn btn-primary">
    <i class="fas fa-sync-alt"></i> Sincronizar Agora
</button>
```

#### Adicionar notificação de instalação do PWA:

```javascript
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    
    // Mostrar botão de instalação
    document.getElementById('install-button').style.display = 'block';
});

document.getElementById('install-button').addEventListener('click', async () => {
    if (deferredPrompt) {
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        console.log(`User response: ${outcome}`);
        deferredPrompt = null;
    }
});
```

## 🎯 Funcionalidades Implementadas

✅ Service Worker para cache de recursos  
✅ IndexedDB para armazenamento local  
✅ Sincronização automática quando online  
✅ Indicador visual de status (online/offline)  
✅ Fila de requisições pendentes  
✅ Badge mostrando itens a sincronizar  
✅ Página offline dedicada  
✅ PWA Manifest para instalação  
✅ Cache de produtos, mesas e categorias  
✅ Registro de pedidos offline  
✅ Registro de pagamentos offline  

## 📊 Benefícios

- ✨ Sistema continua funcionando sem internet
- 🔄 Sincronização automática ao reconectar
- 💾 Dados salvos localmente
- 📱 Pode ser instalado como app nativo
- ⚡ Performance melhorada com cache
- 🎨 UX aprimorada com feedback visual

## 🔍 Monitoramento

Verifique logs no console:
- `[SW]` - Mensagens do Service Worker
- `IndexedDB aberto com sucesso` - Banco local pronto
- `Sincronizando X pedidos pendentes...` - Sincronização em andamento

## 🚀 Deploy

Certifique-se de que:
1. HTTPS está habilitado (obrigatório para Service Workers)
2. Ícones do PWA foram criados
3. Rotas da API foram adicionadas
4. Controllers têm os métodos de sincronização

Pronto! Seu sistema agora funciona offline! 🎉
