package com.myd.restaurante

import android.Manifest
import android.app.NotificationChannel
import android.app.NotificationManager
import android.content.Context
import android.content.pm.PackageManager
import android.os.Build
import android.os.Bundle
import android.os.VibrationEffect
import android.os.Vibrator
import android.util.Log
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.appcompat.app.AppCompatActivity
import androidx.core.app.ActivityCompat
import androidx.core.content.ContextCompat
import com.google.android.gms.tasks.OnCompleteListener
import com.google.android.material.snackbar.Snackbar
import com.google.firebase.messaging.FirebaseMessaging
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import java.net.URL
import javax.net.ssl.HttpsURLConnection

class MainActivity : AppCompatActivity() {

    private lateinit var webView: WebView
    private lateinit var vibrator: Vibrator
    private val REQUEST_NOTIFICATION_PERMISSION = 1001

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        // Remover ActionBar
        supportActionBar?.hide()
        
        setContentView(R.layout.activity_main)

        webView = findViewById(R.id.webView)
        vibrator = getSystemService(VIBRATOR_SERVICE) as Vibrator

        setupWebView()
        loadApp()
        
        // Criar canal de notificação
        createNotificationChannel()
        
        // Pedir permissão de notificação (Android 13+)
        requestNotificationPermission()
        
        // NÃO registrar token aqui - só após login via onLoginSuccess()
        Log.d("FCM_TOKEN", "ℹ️ Token será registrado após login do usuário")
    }
    
    private fun createNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channelId = "myd_notifications"
            val channelName = "MyD Notificações"
            val channelDescription = "Notificações de pedidos e entregas"
            val importance = NotificationManager.IMPORTANCE_HIGH
            
            val channel = NotificationChannel(channelId, channelName, importance).apply {
                description = channelDescription
                enableVibration(true)
                vibrationPattern = longArrayOf(0, 500, 250, 500)
                enableLights(true)
                setShowBadge(true)
            }
            
            val notificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
            notificationManager.createNotificationChannel(channel)
            
            Log.d("FCM_TOKEN", "📢 Canal de notificação criado!")
            
            // Notificação de teste removida - não precisa mais
        }
    }
    
    private fun sendTestNotification() {
        // Função mantida para compatibilidade, mas não faz nada
        Log.d("FCM_TOKEN", "ℹ️ Notificação de teste desabilitada")
    }
    
    private fun requestNotificationPermission() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            if (ContextCompat.checkSelfPermission(
                    this,
                    Manifest.permission.POST_NOTIFICATIONS
                ) != PackageManager.PERMISSION_GRANTED
            ) {
                ActivityCompat.requestPermissions(
                    this,
                    arrayOf(Manifest.permission.POST_NOTIFICATIONS),
                    REQUEST_NOTIFICATION_PERMISSION
                )
            }
        }
    }
    
    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<out String>,
        grantResults: IntArray
    ) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        
        if (requestCode == REQUEST_NOTIFICATION_PERMISSION) {
            if (grantResults.isNotEmpty() && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                Log.d("FCM_TOKEN", "✅ Permissão de notificação concedida!")
                // Snackbar removido - não precisa notificar toda vez
            } else {
                Log.w("FCM_TOKEN", "❌ Permissão de notificação negada!")
                Snackbar.make(webView, "⚠️ Ative as notificações nas configurações", Snackbar.LENGTH_LONG).show()
            }
        }
    }

    private fun setupWebView() {
        val settings: WebSettings = webView.settings

        // JavaScript
        settings.javaScriptEnabled = true
        settings.domStorageEnabled = true
        settings.databaseEnabled = true

        // Cache
        settings.cacheMode = WebSettings.LOAD_DEFAULT

        // Performance
        settings.mixedContentMode = WebSettings.MIXED_CONTENT_ALWAYS_ALLOW
        settings.useWideViewPort = true
        settings.loadWithOverviewMode = true
        settings.setSupportZoom(false)

        // User Agent
        settings.userAgentString = settings.userAgentString + " MyDApp/1.0.0"

        // WebViewClient
        webView.webViewClient = object : WebViewClient() {
            override fun onPageStarted(view: WebView?, url: String?, favicon: android.graphics.Bitmap?) {
                super.onPageStarted(view, url, favicon)
            }

            override fun onPageFinished(view: WebView?, url: String?) {
                super.onPageFinished(view, url)
                injectJavaScript()
            }

            override fun shouldOverrideUrlLoading(view: WebView?, url: String?): Boolean {
                if (url?.startsWith("http://localhost") == true ||
                    url?.startsWith("http://127.0.0.1") == true ||
                    url?.startsWith("http://myd.local") == true) {
                    view?.loadUrl(url)
                    return true
                }
                return false
            }
        }

        // JavaScript Interface para vibração
        webView.addJavascriptInterface(JavaScriptInterface(), "AndroidBridge")
    }

    private fun loadApp() {
        // Carrega a URL do servidor Laravel na rede local
        // Altere o IP conforme necessário: 192.168.15.9 é o IP da máquina com o Laravel
        webView.loadUrl("http://192.168.15.9/app-cliente/")
    }

    private fun injectJavaScript() {
        // Injetar JavaScript para melhor integração
        webView.evaluateJavascript("""
            if (window.AndroidBridge) {
                console.log('[Android] Bridge conectado');
                window.vibrate = function(pattern) {
                    if (Array.isArray(pattern)) {
                        window.AndroidBridge.vibrate(pattern.join(','));
                    } else {
                        window.AndroidBridge.vibrate(pattern);
                    }
                };
            }
            
            // Injetar função para enviar token FCM no login
            (function() {
                const originalFetch = window.fetch;
                window.fetch = function(...args) {
                    const request = originalFetch.apply(this, args);
                    
                    // Interceptar resposta de login
                    if (args[0] && args[0].includes('/api/app/auth/login')) {
                        request.then(response => {
                            const clonedResponse = response.clone();
                            clonedResponse.json().then(data => {
                                console.log('[Android] 📦 Resposta login completa:', JSON.stringify(data));
                                
                                // Verificar sucesso do login
                                if (data.success) {
                                    // Procurar cliente.id em diferentes estruturas
                                    let clienteId = null;
                                    
                                    if (data.cliente && data.cliente.id) {
                                        clienteId = data.cliente.id;
                                    } else if (data.data && data.data.cliente && data.data.cliente.id) {
                                        clienteId = data.data.cliente.id;
                                    }
                                    
                                    if (clienteId) {
                                        console.log('[Android] 🎯 Cliente ID encontrado:', clienteId);
                                        if (window.AndroidBridge && window.AndroidBridge.onLoginSuccess) {
                                            window.AndroidBridge.onLoginSuccess(clienteId.toString());
                                        } else {
                                            console.error('[Android] ❌ AndroidBridge não disponível!');
                                        }
                                    } else {
                                        console.warn('[Android] ⚠️ Cliente ID não encontrado na resposta');
                                    }
                                }
                            }).catch(err => {
                                console.error('[Android] ❌ Erro ao parsear resposta:', err);
                            });
                        });
                    }
                    
                    return request;
                };
            })();
        """.trimIndent(), null)
    }

    override fun onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack()
        } else {
            super.onBackPressed()
        }
    }

    /**
     * Registrar token FCM no servidor Laravel
     */
    /**
     * FUNÇÃO REMOVIDA - Token agora é registrado apenas via onLoginSuccess()
     * Isso garante que sempre tenha user_id associado ao token
     */
    @Deprecated("Usar apenas onLoginSuccess() para registrar token com user_id")
    private fun registerFCMToken() {
        // Código removido - token agora só é enviado após login
        Log.d("FCM_TOKEN", "⚠️ registerFCMToken() está deprecated - usar onLoginSuccess()")
    }

    /**
     * Enviar token FCM para o servidor Laravel vinculado ao cliente
     */
    private fun sendTokenToServer(token: String, clienteId: Int? = null) {
        Log.d("FCM_TOKEN", "📤 Enviando token para servidor... Cliente ID: $clienteId")
        CoroutineScope(Dispatchers.Default).launch {
            try {
                val httpConnection = URL("http://192.168.15.9/api/notificacao/salvar-token")
                    .openConnection() as java.net.HttpURLConnection
                
                httpConnection.requestMethod = "POST"
                httpConnection.setRequestProperty("Content-Type", "application/json")
                httpConnection.doOutput = true

                val jsonPayload = if (clienteId != null) {
                    """{
                        "token": "$token",
                        "user_id": $clienteId,
                        "device_type": "android",
                        "device_name": "${Build.MODEL}"
                    }"""
                } else {
                    """{
                        "token": "$token",
                        "device_type": "android",
                        "device_name": "${Build.MODEL}"
                    }"""
                }
                
                Log.d("FCM_TOKEN", "📦 Payload: $jsonPayload")

                httpConnection.outputStream.use { os ->
                    os.write(jsonPayload.toByteArray(Charsets.UTF_8))
                }

                val responseCode = httpConnection.responseCode
                Log.d("FCM_TOKEN", "📡 Response Code: $responseCode")

                if (responseCode == 200) {
                    val response = httpConnection.inputStream.bufferedReader().use { it.readText() }
                    Log.d("FCM_TOKEN", "✅ Response: $response")
                    Log.d("FCM_TOKEN", "✅ Token registrado com sucesso!")
                    // Snackbar removido - usuário não precisa ver isso toda vez
                } else {
                    val errorBody = httpConnection.errorStream?.bufferedReader()?.use { it.readText() }
                    Log.e("FCM_TOKEN", "❌ Error: $responseCode - $errorBody")
                }

                httpConnection.disconnect()

            } catch (e: Exception) {
                Log.e("FCM_TOKEN", "❌ Exception: ${e.message}", e)
                e.printStackTrace()
            }
        }
    }

    // Interface JavaScript para Android
    private inner class JavaScriptInterface {
        @android.webkit.JavascriptInterface
        fun vibrate(pattern: String) {
            try {
                val patterns = pattern.split(",").map { it.toLong() }.toLongArray()

                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                    val amplitudes = IntArray(patterns.size) { 255 }
                    val effect = VibrationEffect.createWaveform(patterns, amplitudes, -1)
                    vibrator.vibrate(effect)
                } else {
                    @Suppress("DEPRECATION")
                    vibrator.vibrate(patterns, -1)
                }
            } catch (e: Exception) {
                e.printStackTrace()
            }
        }

        @android.webkit.JavascriptInterface
        fun showMessage(message: String) {
            runOnUiThread {
                Snackbar.make(webView, message, Snackbar.LENGTH_SHORT).show()
            }
        }
        
        @android.webkit.JavascriptInterface
        fun onLoginSuccess(clienteId: String) {
            Log.d("FCM_TOKEN", "🔔 Login detectado! Cliente ID: $clienteId")
            
            // Buscar token FCM e enviar para servidor com cliente_id
            FirebaseMessaging.getInstance().token.addOnCompleteListener { task ->
                if (task.isSuccessful) {
                    val token = task.result
                    Log.d("FCM_TOKEN", "📤 Enviando token com cliente_id: $clienteId")
                    sendTokenToServer(token, clienteId.toIntOrNull())
                }
            }
        }
        
        @android.webkit.JavascriptInterface
        fun forceReload() {
            Log.d("WEBVIEW", "🔄 Forçando reload da página")
            runOnUiThread {
                webView.reload()
            }
        }
        
        @android.webkit.JavascriptInterface
        fun clearCache() {
            Log.d("WEBVIEW", "🗑️ Limpando cache do WebView")
            runOnUiThread {
                webView.clearCache(true)
                webView.clearHistory()
            }
        }
    }
}

