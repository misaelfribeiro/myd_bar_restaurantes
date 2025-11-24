package com.myd.restaurante

import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import android.os.Build
import android.util.Log
import androidx.core.app.NotificationCompat
import com.google.firebase.messaging.FirebaseMessagingService
import com.google.firebase.messaging.RemoteMessage

class MyFirebaseMessagingService : FirebaseMessagingService() {

    companion object {
        const val CHANNEL_ID = "myd_notifications"
        const val CHANNEL_NAME = "MyD Notificações"
        const val TAG = "FCM_SERVICE"
    }

    override fun onMessageReceived(remoteMessage: RemoteMessage) {
        super.onMessageReceived(remoteMessage)
        
        Log.d(TAG, "=== NOTIFICAÇÃO RECEBIDA ===")
        Log.d(TAG, "From: ${remoteMessage.from}")
        Log.d(TAG, "Notification: ${remoteMessage.notification}")
        Log.d(TAG, "Data: ${remoteMessage.data}")

        // Extrair dados da mensagem
        val title = remoteMessage.notification?.title ?: "MyD Bar & Restaurantes"
        val body = remoteMessage.notification?.body ?: ""
        val pedidoId = remoteMessage.data["pedido_id"] ?: ""
        val action = remoteMessage.data["action"] ?: ""
        
        Log.d(TAG, "Title: $title")
        Log.d(TAG, "Body: $body")
        Log.d(TAG, "Pedido ID: $pedidoId")
        Log.d(TAG, "Action: $action")

        // Enviar notificação
        sendNotification(title, body, pedidoId, action)
    }

    override fun onNewToken(token: String) {
        super.onNewToken(token)
        Log.d(TAG, "=== NOVO TOKEN FCM ===")
        Log.d(TAG, "Token: $token")
        // Aqui você pode enviar o token para seu servidor
        // para usar em futuras notificações
        saveTokenToServer(token)
    }

    private fun sendNotification(title: String, body: String, pedidoId: String, action: String) {
        Log.d(TAG, "=== ENVIANDO NOTIFICAÇÃO ===")
        Log.d(TAG, "Título: $title")
        Log.d(TAG, "Mensagem: $body")
        
        // Criar canal de notificação (para Android 8.0+)
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                CHANNEL_ID,
                CHANNEL_NAME,
                NotificationManager.IMPORTANCE_HIGH
            ).apply {
                description = "Notificações do MyD Bar & Restaurantes"
                enableVibration(true)
                vibrationPattern = longArrayOf(0, 500, 250, 500, 250, 500)
                enableLights(true)
                setShowBadge(true)
                lockscreenVisibility = android.app.Notification.VISIBILITY_PUBLIC
            }
            val notificationManager: NotificationManager =
                getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
            notificationManager.createNotificationChannel(channel)
            Log.d(TAG, "Canal de notificação criado")
        }

        // Intent para abrir o app quando clicar na notificação
        val intent = Intent(this, MainActivity::class.java).apply {
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
            putExtra("pedido_id", pedidoId)
            putExtra("action", action)
        }
        
        val pendingIntent: PendingIntent = PendingIntent.getActivity(
            this, 0, intent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )

        // Construir notificação com som padrão e vibração
        val notificationBuilder = NotificationCompat.Builder(this, CHANNEL_ID)
            .setContentTitle(title)
            .setContentText(body)
            .setSmallIcon(android.R.drawable.ic_dialog_info)
            .setContentIntent(pendingIntent)
            .setAutoCancel(true)
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .setDefaults(NotificationCompat.DEFAULT_ALL) // Som, vibração e luz
            .setVibrate(longArrayOf(0, 500, 250, 500, 250, 500))
            .setVisibility(NotificationCompat.VISIBILITY_PUBLIC)
            .setCategory(NotificationCompat.CATEGORY_MESSAGE)

        // Mostrar notificação
        val notificationManager: NotificationManager =
            getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        val notificationId = pedidoId.hashCode()
        notificationManager.notify(notificationId, notificationBuilder.build())
        
        Log.d(TAG, "Notificação enviada com ID: $notificationId")
        Log.d(TAG, "========================")
    }

    private fun saveTokenToServer(token: String) {
        // TODO: Enviar token para seu servidor Laravel
        // Fazer uma chamada HTTP para salvar o token do FCM
    }
}
