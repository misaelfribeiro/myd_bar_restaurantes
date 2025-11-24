# Script de Configuração Automática do Apache para MyD Bar & Restaurantes
# Execute como ADMINISTRADOR

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Configuração do Apache - MyD System  " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verificar se está rodando como administrador
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "❌ ERRO: Execute este script como ADMINISTRADOR!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Clique com botão direito no PowerShell e selecione 'Executar como Administrador'" -ForegroundColor Yellow
    pause
    exit
}

Write-Host "✅ Script rodando como Administrador" -ForegroundColor Green
Write-Host ""

# Passo 1: Adicionar Include no httpd.conf
Write-Host "📝 Passo 1: Configurando httpd.conf..." -ForegroundColor Yellow
$httpdConf = "C:\xampp\apache\conf\httpd.conf"

if (Test-Path $httpdConf) {
    $content = Get-Content $httpdConf -Raw
    $includeString = "Include conf/extra/httpd-vhosts-myd.conf"
    
    if ($content -notmatch [regex]::Escape($includeString)) {
        Add-Content $httpdConf "`n# MyD Bar & Restaurantes Virtual Host"
        Add-Content $httpdConf $includeString
        Write-Host "   ✅ Include adicionado ao httpd.conf" -ForegroundColor Green
    } else {
        Write-Host "   ℹ️  Include já existe no httpd.conf" -ForegroundColor Cyan
    }
} else {
    Write-Host "   ❌ httpd.conf não encontrado!" -ForegroundColor Red
}

Write-Host ""

# Passo 2: Adicionar entradas no arquivo hosts
Write-Host "📝 Passo 2: Configurando arquivo hosts..." -ForegroundColor Yellow
$hostsFile = "C:\Windows\System32\drivers\etc\hosts"

if (Test-Path $hostsFile) {
    $hostsContent = Get-Content $hostsFile -Raw
    $mydLocal = "127.0.0.1    myd.local"
    $wwwMydLocal = "127.0.0.1    www.myd.local"
    
    $modified = $false
    
    if ($hostsContent -notmatch "myd\.local") {
        Add-Content $hostsFile "`n# MyD Bar & Restaurantes"
        Add-Content $hostsFile $mydLocal
        Add-Content $hostsFile $wwwMydLocal
        $modified = $true
        Write-Host "   ✅ Entradas adicionadas ao arquivo hosts" -ForegroundColor Green
    } else {
        Write-Host "   ℹ️  Entradas já existem no arquivo hosts" -ForegroundColor Cyan
    }
} else {
    Write-Host "   ❌ Arquivo hosts não encontrado!" -ForegroundColor Red
}

Write-Host ""

# Passo 3: Verificar módulos do Apache
Write-Host "📝 Passo 3: Verificando módulos do Apache..." -ForegroundColor Yellow
$requiredModules = @(
    "LoadModule rewrite_module modules/mod_rewrite.so",
    "LoadModule ssl_module modules/mod_ssl.so",
    "LoadModule socache_shmcb_module modules/mod_socache_shmcb.so"
)

$httpdContent = Get-Content $httpdConf

foreach ($module in $requiredModules) {
    $moduleName = ($module -split ' ')[1]
    
    if ($httpdContent -match [regex]::Escape($module)) {
        Write-Host "   ✅ $moduleName habilitado" -ForegroundColor Green
    } elseif ($httpdContent -match "#\s*$([regex]::Escape($module))") {
        Write-Host "   ⚠️  $moduleName comentado (precisa descomentar)" -ForegroundColor Yellow
    } else {
        Write-Host "   ❌ $moduleName não encontrado" -ForegroundColor Red
    }
}

Write-Host ""

# Passo 4: Configurar permissões do Laravel
Write-Host "📝 Passo 4: Configurando permissões do Laravel..." -ForegroundColor Yellow
$projectPath = "C:\xampp\htdocs\myd_bar_restaurantes"

if (Test-Path $projectPath) {
    try {
        icacls "$projectPath\storage" /grant Everyone:(OI)(CI)F /T /Q | Out-Null
        Write-Host "   ✅ Permissões do storage configuradas" -ForegroundColor Green
        
        icacls "$projectPath\bootstrap\cache" /grant Everyone:(OI)(CI)F /T /Q | Out-Null
        Write-Host "   ✅ Permissões do bootstrap/cache configuradas" -ForegroundColor Green
    } catch {
        Write-Host "   ⚠️  Erro ao configurar permissões: $_" -ForegroundColor Yellow
    }
} else {
    Write-Host "   ❌ Projeto não encontrado em $projectPath" -ForegroundColor Red
}

Write-Host ""

# Passo 5: Limpar cache do Laravel
Write-Host "📝 Passo 5: Limpando cache do Laravel..." -ForegroundColor Yellow
Set-Location $projectPath

try {
    php artisan cache:clear | Out-Null
    Write-Host "   ✅ Cache limpo" -ForegroundColor Green
    
    php artisan config:clear | Out-Null
    Write-Host "   ✅ Config limpo" -ForegroundColor Green
    
    php artisan route:clear | Out-Null
    Write-Host "   ✅ Rotas limpas" -ForegroundColor Green
    
    php artisan view:clear | Out-Null
    Write-Host "   ✅ Views limpas" -ForegroundColor Green
} catch {
    Write-Host "   ⚠️  Erro ao limpar cache: $_" -ForegroundColor Yellow
}

Write-Host ""

# Passo 6: Atualizar .env
Write-Host "📝 Passo 6: Atualizando .env..." -ForegroundColor Yellow
$envFile = "$projectPath\.env"

if (Test-Path $envFile) {
    $envContent = Get-Content $envFile
    $newContent = $envContent -replace 'APP_URL=.*', 'APP_URL=http://myd.local'
    $newContent | Set-Content $envFile
    Write-Host "   ✅ APP_URL atualizado para http://myd.local" -ForegroundColor Green
} else {
    Write-Host "   ⚠️  Arquivo .env não encontrado" -ForegroundColor Yellow
}

Write-Host ""

# Passo 7: Reiniciar Apache
Write-Host "📝 Passo 7: Reiniciando Apache..." -ForegroundColor Yellow
Write-Host "   ⏳ Parando Apache..." -ForegroundColor Cyan

try {
    $apacheService = Get-Service -Name "Apache2.4" -ErrorAction SilentlyContinue
    
    if ($apacheService) {
        Stop-Service -Name "Apache2.4" -Force
        Start-Sleep -Seconds 2
        Write-Host "   ✅ Apache parado" -ForegroundColor Green
        
        Write-Host "   ⏳ Iniciando Apache..." -ForegroundColor Cyan
        Start-Service -Name "Apache2.4"
        Start-Sleep -Seconds 2
        Write-Host "   ✅ Apache iniciado" -ForegroundColor Green
    } else {
        Write-Host "   ⚠️  Serviço Apache2.4 não encontrado" -ForegroundColor Yellow
        Write-Host "   ℹ️  Use o XAMPP Control Panel para reiniciar manualmente" -ForegroundColor Cyan
    }
} catch {
    Write-Host "   ⚠️  Erro ao reiniciar Apache: $_" -ForegroundColor Yellow
    Write-Host "   ℹ️  Use o XAMPP Control Panel para reiniciar manualmente" -ForegroundColor Cyan
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Configuração Concluída!               " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "🌐 Acesse o sistema:" -ForegroundColor Yellow
Write-Host "   • http://myd.local" -ForegroundColor White
Write-Host "   • http://myd.local/login-niveis" -ForegroundColor White
Write-Host "   • http://myd.local/dashboard-niveis" -ForegroundColor White
Write-Host ""
Write-Host "👥 Usuários demo:" -ForegroundColor Yellow
Write-Host "   • admin@exemplo.com / 123456" -ForegroundColor White
Write-Host "   • gerente@exemplo.com / 123456" -ForegroundColor White
Write-Host "   • garcom@exemplo.com / 123456" -ForegroundColor White
Write-Host "   • caixa@exemplo.com / 123456" -ForegroundColor White
Write-Host ""
Write-Host "📊 Monitoramento:" -ForegroundColor Yellow
Write-Host "   • http://myd.local/monitor-completo.html" -ForegroundColor White
Write-Host ""
Write-Host "Pressione qualquer tecla para sair..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
