<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class NotificacaoController extends Controller
{
    private $firebaseCredentialsPath = '/firebase-config.json';
    private $projectId = 'speedfood-b4495';
    private $fcmUrl = 'https://fcm.googleapis.com/v1/projects/speedfood-b4495/messages:send';

    /**
     * Obter Access Token do Firebase usando Service Account
     */
    private function getAccessToken()
    {
        $cachedToken = Cache::get('firebase_access_token');
        if ($cachedToken) {
            return $cachedToken;
        }

        try {
            $credentialsPath = base_path() . $this->firebaseCredentialsPath;
            
            if (!file_exists($credentialsPath)) {
                throw new \Exception("Firebase credentials not found: $credentialsPath");
            }

            $credentials = json_decode(file_get_contents($credentialsPath), true);

            $now = time();
            $expiry = $now + 3600;

            $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
            $payload = json_encode([
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/cloud-platform',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $expiry,
                'iat' => $now,
            ]);

            $headerEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $payloadEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
            $signatureInput = $headerEncoded . '.' . $payloadEncoded;

            openssl_sign($signatureInput, $signature, $credentials['private_key'], 'sha256');
            $signatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

            $jwt = $signatureInput . '.' . $signatureEncoded;

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if (!$response->successful()) {
                throw new \Exception("Error getting Access Token: " . $response->body());
            }

            $token = $response->json()['access_token'];
            Cache::put('firebase_access_token', $token, now()->addMinutes(55));

            return $token;

        } catch (\Exception $e) {
            Log::error('Firebase Access Token error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Enviar notificação genérica via Firebase
     */
    private function sendFirebaseNotification($dados)
    {
        try {
            $accessToken = $this->getAccessToken();

            $payload = [
                'message' => [
                    'token' => $dados['token'],
                    'notification' => [
                        'title' => $dados['titulo'],
                        'body' => $dados['mensagem'],
                    ],
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        ],
                    ],
                    'data' => [
                        'pedido_id' => (string)($dados['pedido_id'] ?? ''),
                        'action' => $dados['action'] ?? '',
                    ],
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, $payload);

            if ($response->successful()) {
                Log::info('Notification sent', ['pedido_id' => $dados['pedido_id'] ?? 'N/A']);
                return response()->json(['success' => true, 'mensagem' => 'Sent'], 200);
            } else {
                Log::error('Firebase error: ' . $response->status());
                return response()->json(['success' => false, 'erro' => 'Firebase error'], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Notification error: ' . $e->getMessage());
            return response()->json(['success' => false, 'erro' => $e->getMessage()], 500);
        }
    }

    /**
     * Salvar token FCM do dispositivo
     */
    public function salvarToken(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'user_id' => 'nullable|integer',
            'device_type' => 'nullable|string',
            'device_name' => 'nullable|string',
        ]);

        try {
            $userId = $validated['user_id'] ?? null;
            $newToken = $validated['token'];
            
            // Se tem user_id, desativar TODOS os outros tokens desse usuário
            if ($userId) {
                $desativados = \DB::table('fcm_tokens')
                    ->where('user_id', $userId)
                    ->where('token', '!=', $newToken)
                    ->update(['ativo' => false, 'updated_at' => now()]);
                
                if ($desativados > 0) {
                    Log::info("Desativados $desativados token(s) antigo(s) do user_id: $userId");
                }
            }
            
            // Salvar/atualizar o novo token
            \DB::table('fcm_tokens')->updateOrInsert(
                ['token' => $newToken], // Busca pelo token
                [
                    'user_id' => $userId,
                    'token' => $newToken,
                    'device_type' => $validated['device_type'] ?? 'android',
                    'ativo' => true,
                    'updated_at' => now(),
                    'created_at' => \DB::raw('IFNULL(created_at, NOW())')
                ]
            );
            
            Log::info('FCM Token saved to database', [
                'user_id' => $userId,
                'token' => substr($newToken, 0, 30) . '...',
                'device_type' => $validated['device_type'] ?? 'android',
                'tokens_desativados' => $desativados ?? 0
            ]);
            
            return response()->json([
                'success' => true, 
                'mensagem' => 'Token salvo com sucesso',
                'user_id' => $userId,
                'tokens_antigos_desativados' => $desativados ?? 0
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error saving token: ' . $e->getMessage());
            return response()->json(['success' => false, 'erro' => $e->getMessage()], 500);
        }
    }

    /**
     * Desativar token FCM no logout
     */
    public function desativarToken(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
        ]);

        try {
            $updated = \DB::table('fcm_tokens')
                ->where('user_id', $validated['user_id'])
                ->update(['ativo' => false, 'updated_at' => now()]);
            
            Log::info('FCM Token desativado no logout', [
                'user_id' => $validated['user_id'],
                'tokens_updated' => $updated
            ]);
            
            return response()->json([
                'success' => true, 
                'mensagem' => 'Token desativado com sucesso'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deactivating token: ' . $e->getMessage());
            return response()->json(['success' => false, 'erro' => $e->getMessage()], 500);
        }
    }

    /**
     * Listar todos os tokens registrados
     */
    public function listarTokens()
    {
        try {
            $allTokens = Cache::get('fcm_all_tokens', []);
            $tokens = array_values($allTokens);
            
            return response()->json([
                'success' => true,
                'tokens' => $tokens,
                'total' => count($tokens)
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error listing tokens: ' . $e->getMessage());
            return response()->json(['success' => false, 'erro' => $e->getMessage()], 500);
        }
    }

    /**
     * Enviar notificação push
     */
    public function enviarNotificacao(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'titulo' => 'required|string',
            'mensagem' => 'required|string',
            'pedido_id' => 'nullable|string',
            'action' => 'nullable|string',
        ]);

        return $this->sendFirebaseNotification($validated);
    }

    /**
     * Enviar para múltiplos dispositivos
     */
    public function enviarNotificacaoMultipla(Request $request)
    {
        $validated = $request->validate([
            'tokens' => 'required|array',
            'tokens.*' => 'string',
            'titulo' => 'required|string',
            'mensagem' => 'required|string',
            'pedido_id' => 'nullable|string',
            'action' => 'nullable|string',
        ]);

        $sucessos = 0;
        $erros = 0;

        foreach ($validated['tokens'] as $token) {
            try {
                $response = $this->sendFirebaseNotification([
                    'token' => $token,
                    'titulo' => $validated['titulo'],
                    'mensagem' => $validated['mensagem'],
                    'pedido_id' => $validated['pedido_id'] ?? '',
                    'action' => $validated['action'] ?? '',
                ]);

                if ($response->getStatusCode() === 200) {
                    $sucessos++;
                } else {
                    $erros++;
                }
            } catch (\Exception $e) {
                $erros++;
            }
        }

        return response()->json([
            'success' => $erros === 0,
            'sucessos' => $sucessos,
            'erros' => $erros,
        ], 200);
    }

    /**
     * Notificar pedido pronto
     */
    public function notificarPedidoPronto(Request $request)
    {
        $validated = $request->validate([
            'pedido_id' => 'required',
        ]);

        // Buscar pedido
        $pedido = \App\Models\Pedido::find($validated['pedido_id']);
        if (!$pedido) {
            return response()->json(['success' => false, 'erro' => 'Pedido não encontrado'], 404);
        }

        // Tentar buscar token pelo cliente_id, senão pelo usuario_id
        $userId = $pedido->cliente_id ?? $pedido->usuario_id;
        
        if (!$userId) {
            return response()->json(['success' => false, 'erro' => 'Usuário não identificado no pedido'], 404);
        }

        $fcmToken = \DB::table('fcm_tokens')
            ->where('user_id', $userId)
            ->where('ativo', true)
            ->value('token');

        if (!$fcmToken) {
            return response()->json(['success' => false, 'erro' => 'Token FCM não encontrado para user_id: ' . $userId], 404);
        }

        return $this->sendFirebaseNotification([
            'token' => $fcmToken,
            'titulo' => '✅ Pedido Pronto!',
            'mensagem' => '🍽️ Seu pedido #' . $validated['pedido_id'] . ' está prontinho para retirada! 😋',
            'pedido_id' => $validated['pedido_id'],
            'action' => 'pedido_pronto',
        ]);
    }

    /**
     * Notificar delivery aceito
     */
    public function notificarDeliveryAceito(Request $request)
    {
        $validated = $request->validate([
            'pedido_id' => 'required',
        ]);

        // Buscar pedido
        $pedido = \App\Models\Pedido::find($validated['pedido_id']);
        if (!$pedido) {
            return response()->json(['success' => false, 'erro' => 'Pedido não encontrado'], 404);
        }

        // Tentar buscar token pelo cliente_id, senão pelo usuario_id
        $userId = $pedido->cliente_id ?? $pedido->usuario_id;
        
        if (!$userId) {
            return response()->json(['success' => false, 'erro' => 'Usuário não identificado no pedido'], 404);
        }

        $fcmToken = \DB::table('fcm_tokens')
            ->where('user_id', $userId)
            ->where('ativo', true)
            ->value('token');

        if (!$fcmToken) {
            return response()->json(['success' => false, 'erro' => 'Token FCM não encontrado para user_id: ' . $userId], 404);
        }

        $entregador = $request->input('entregador', 'Entregador');
        $tempoEstimado = $request->input('tempo_estimado', 30);

        return $this->sendFirebaseNotification([
            'token' => $fcmToken,
            'titulo' => '🛵 Pedido Saiu para Entrega!',
            'mensagem' => "🚀 $entregador está levando seu pedido até você! ⏱️ Tempo estimado: $tempoEstimado min.",
            'pedido_id' => $validated['pedido_id'],
            'action' => 'delivery_aceito',
        ]);
    }

    /**
     * Notificar delivery entregue
     */
    public function notificarDeliveryEntregue(Request $request)
    {
        $validated = $request->validate([
            'pedido_id' => 'required',
        ]);

        // Buscar pedido
        $pedido = \App\Models\Pedido::find($validated['pedido_id']);
        if (!$pedido) {
            return response()->json(['success' => false, 'erro' => 'Pedido não encontrado'], 404);
        }

        // Tentar buscar token pelo cliente_id, senão pelo usuario_id
        $userId = $pedido->cliente_id ?? $pedido->usuario_id;
        
        if (!$userId) {
            return response()->json(['success' => false, 'erro' => 'Usuário não identificado no pedido'], 404);
        }

        $fcmToken = \DB::table('fcm_tokens')
            ->where('user_id', $userId)
            ->where('ativo', true)
            ->value('token');

        if (!$fcmToken) {
            return response()->json(['success' => false, 'erro' => 'Token FCM não encontrado para user_id: ' . $userId], 404);
        }

        return $this->sendFirebaseNotification([
            'token' => $fcmToken,
            'titulo' => '🎉 Pedido Entregue!',
            'mensagem' => '✅ Seu pedido #' . $validated['pedido_id'] . ' foi entregue com sucesso! Bom apetite! 😋🍴',
            'pedido_id' => $validated['pedido_id'],
            'action' => 'delivery_entregue',
        ]);
    }

}
