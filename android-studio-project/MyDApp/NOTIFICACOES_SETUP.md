# Configuração de Notificações Push - Android

## ✅ O que foi implementado:

1. **Firebase Cloud Messaging (FCM)** adicionado ao projeto
2. **Serviço de Notificações** (`MyFirebaseMessagingService.kt`) configurado
3. **Permissões** atualizadas no AndroidManifest
4. **Configuração de Segurança** preparada para HTTP local

## 📋 Próximas Etapas para Completar:

### 1️⃣ Criar Projeto Firebase
1. Acesse: https://console.firebase.google.com
2. Crie um novo projeto: "myd-bar-restaurantes"
3. Ative **Cloud Messaging**

### 2️⃣ Baixar google-services.json
1. Em Firebase Console, vá para **Configurações do Projeto**
2. Vá para a aba **Apps**
3. Selecione seu app Android
4. Baixe o arquivo `google-services.json`
5. Substitua o arquivo em: `app/google-services.json`

### 3️⃣ Obter Chave do Servidor
1. Em Firebase Console, vá para **Cloud Messaging**
2. Copie a **Chave do Servidor** (Server Key)
3. Guarde para usar na API Laravel

### 4️⃣ Criar API Laravel para Enviar Notificações

Crie um controlador em Laravel:

```php
<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificacaoController extends Controller
{
    private $fcmServerKey = 'YOUR_FCM_SERVER_KEY';
    private $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    public function enviarNotificacao(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'titulo' => 'required|string',
            'mensagem' => 'required|string',
            'pedido_id' => 'nullable|string',
            'action' => 'nullable|string',
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $this->fcmServerKey,
            'Content-Type' => 'application/json',
        ])->post($this->fcmUrl, [
            'to' => $validated['token'],
            'notification' => [
                'title' => $validated['titulo'],
                'body' => $validated['mensagem'],
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
            'data' => [
                'pedido_id' => $validated['pedido_id'] ?? '',
                'action' => $validated['action'] ?? '',
            ],
            'priority' => 'high',
        ]);

        return response()->json([
            'success' => $response->successful(),
            'response' => $response->json(),
        ]);
    }

    public function salvarToken(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'usuario_id' => 'required|integer',
        ]);

        // Salvar token no banco de dados
        // Token::updateOrCreate(
        //     ['usuario_id' => $validated['usuario_id']],
        //     ['token' => $validated['token']]
        // );

        return response()->json(['success' => true]);
    }
}
```

### 5️⃣ Adicionar Rotas Laravel

Em `routes/api.php`:

```php
Route::post('/notificacao/enviar', [App\Http\Controllers\Api\NotificacaoController::class, 'enviarNotificacao']);
Route::post('/notificacao/salvar-token', [App\Http\Controllers\Api\NotificacaoController::class, 'salvarToken']);
```

### 6️⃣ Atualizar MainActivity.kt para Salvar Token

Adicione em `MainActivity.kt`:

```kotlin
private fun saveTokenToServer() {
    FirebaseMessaging.getInstance().token.addOnCompleteListener { task ->
        if (task.isSuccessful) {
            val token = task.result
            // Enviar para o servidor Laravel
            sendTokenToServer(token)
        }
    }
}

private fun sendTokenToServer(token: String) {
    // Fazer chamada HTTP para salvar o token
    val request = """
        {
            "token": "$token",
            "usuario_id": 1
        }
    """.trimIndent()
    
    webView.evaluateJavascript("""
        fetch('http://192.168.15.9/api/notificacao/salvar-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: '$request'
        })
    """.trimIndent(), null)
}
```

## 🧪 Testando Notificações

1. **Reconstruir o APK** e instalar no tablet
2. **Abrir o app** para registrar o token
3. **Chamar a API Laravel** para enviar notificação:

```bash
curl -X POST http://localhost/api/notificacao/enviar \
  -H "Content-Type: application/json" \
  -d '{
    "token": "SEU_TOKEN_FCM",
    "titulo": "Pedido Pronto",
    "mensagem": "Seu pedido está pronto!",
    "pedido_id": "123",
    "action": "pedido"
  }'
```

## 📝 Notas Importantes

- Replace `YOUR_FCM_SERVER_KEY` com sua chave real do Firebase
- Replace `192.168.15.9` com seu IP local
- O token é único por dispositivo e deve ser salvo no servidor
- Sempre use HTTPS em produção

## ✨ Próximo: Testar No Tablet

Uma vez completada a configuração do Firebase, reconstrua o APK e teste!
