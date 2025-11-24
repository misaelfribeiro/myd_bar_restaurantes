#!/bin/bash
# Script para instalar a APK no Android via ADB

echo "🤖 Instruções para instalar no Android:"
echo ""
echo "Opção 1: Usar Chrome para instalar como PWA"
echo "1. Abra http://localhost/app-cliente/ no Chrome do Android"
echo "2. Toque no menu (⋮) → 'Instalar aplicativo'"
echo "3. Confirme"
echo ""
echo "Opção 2: Instalar via ADB (Android Debug Bridge)"
echo "1. Conecte o dispositivo Android via USB"
echo "2. Ative 'Depuração USB' nas Configurações"
echo "3. Execute: adb install app-debug.apk"
echo ""
echo "Opção 3: Enviar arquivo APK por email/WhatsApp"
echo "Localização: C:\\xampp\\htdocs\\myd_bar_restaurantes\\cordova-app\\MyDApp\\platforms\\android\\app\\build\\outputs\\apk\\debug\\app-debug.apk"
echo ""
echo "✅ Aguarde a compilação concluir..."
