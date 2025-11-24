#!/usr/bin/env powershell
# Script para testar notificação com token FCM real
# Uso: .\testar-fcm-token.ps1 -Token "seu_token_fcm_aqui"

param(
    [string]$Token = "TOKEN_AQUI",
    [string]$Server = "http://192.168.15.9"
)

Write-Host "🔔 Testador de Notificações Firebase" -ForegroundColor Cyan
Write-Host "====================================" -ForegroundColor Cyan
Write-Host ""

if ($Token -eq "TOKEN_AQUI") {
    Write-Host "❌ Token não fornecido!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Uso: .\testar-fcm-token.ps1 -Token 'seu_token_fcm_aqui'" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Exemplo:" -ForegroundColor Green
    Write-Host '  .\testar-fcm-token.ps1 -Token "fTe5...qW2"' -ForegroundColor Cyan
    exit 1
}

Write-Host "Token: $($Token.Substring(0,20))..." -ForegroundColor Green
Write-Host "Servidor: $Server" -ForegroundColor Green
Write-Host ""

function Teste-Endpoint {
    param(
        [string]$Name,
        [string]$Endpoint,
        [hashtable]$Data
    )
    
    Write-Host "📤 Testando: $Name" -ForegroundColor Yellow
    Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
    
    try {
        $url = "$Server/api/notificacao/$Endpoint"
        $response = Invoke-WebRequest -Uri $url `
            -Method POST `
            -ContentType "application/json" `
            -Body ($Data | ConvertTo-Json) `
            -ErrorAction Stop
        
        Write-Host "✅ Status: $($response.StatusCode)" -ForegroundColor Green
        $json = $response.Content | ConvertFrom-Json
        Write-Host "Resposta:" -ForegroundColor Cyan
        Write-Host ($json | ConvertTo-Json -Depth 2) -ForegroundColor White
    }
    catch {
        Write-Host "❌ Erro: $($_.Exception.Message)" -ForegroundColor Red
        if ($_.Exception.Response) {
            $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
            $errorBody = $reader.ReadToEnd()
            Write-Host "Detalhes: $errorBody" -ForegroundColor Red
        }
    }
    
    Write-Host ""
}

# Testes
$testData = @{
    token = $Token
}

Teste-Endpoint "1. Teste Simples" "testar" $testData

$testData2 = @{
    token = $Token
    titulo = "Teste de Notificação"
    mensagem = "Esta é uma notificação de teste do MyD App!"
    pedido_id = "999"
    action = "teste"
}

Teste-Endpoint "2. Notificação Genérica" "enviar" $testData2

$testData3 = @{
    token = $Token
    pedido_id = "123"
}

Teste-Endpoint "3. Pedido Pronto" "pedido-pronto" $testData3

$testData4 = @{
    token = $Token
    pedido_id = "123"
}

Teste-Endpoint "4. Delivery Aceito" "delivery-aceito" $testData4

$testData5 = @{
    token = $Token
    pedido_id = "123"
}

Teste-Endpoint "5. Delivery Entregue" "delivery-entregue" $testData5

Write-Host "✅ Todos os testes concluídos!" -ForegroundColor Green
Write-Host ""
Write-Host "💡 Dicas:" -ForegroundColor Cyan
Write-Host "  - Verifique o tablet para notificações recebidas" -ForegroundColor White
Write-Host "  - Consulte logs: adb logcat -s 'FCM|MyDApp'" -ForegroundColor White
Write-Host "  - Ver Laravel logs: tail -f storage/logs/laravel.log" -ForegroundColor White
