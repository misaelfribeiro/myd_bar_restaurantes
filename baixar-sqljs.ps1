# Script para baixar SQL.js localmente
# Execute: .\baixar-sqljs.ps1

Write-Host "=================================================" -ForegroundColor Cyan
Write-Host "  Baixando SQL.js Localmente" -ForegroundColor Cyan
Write-Host "=================================================" -ForegroundColor Cyan
Write-Host ""

# Criar diretório
$destDir = "public\libs"
if (-not (Test-Path $destDir)) {
    Write-Host "📁 Criando diretório $destDir..." -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $destDir -Force | Out-Null
    Write-Host "✅ Diretório criado!" -ForegroundColor Green
} else {
    Write-Host "✅ Diretório já existe: $destDir" -ForegroundColor Green
}
Write-Host ""

# URLs dos arquivos
$files = @{
    "sql-wasm.js" = "https://sql.js.org/dist/sql-wasm.js"
    "sql-wasm.wasm" = "https://sql.js.org/dist/sql-wasm.wasm"
}

foreach ($file in $files.GetEnumerator()) {
    $fileName = $file.Key
    $url = $file.Value
    $destPath = Join-Path $destDir $fileName
    
    Write-Host "📥 Baixando $fileName..." -ForegroundColor Yellow
    Write-Host "   URL: $url" -ForegroundColor Gray
    Write-Host "   Destino: $destPath" -ForegroundColor Gray
    
    try {
        Invoke-WebRequest -Uri $url -OutFile $destPath -UseBasicParsing
        
        $fileSize = (Get-Item $destPath).Length
        $fileSizeKB = [math]::Round($fileSize / 1KB, 2)
        
        Write-Host "✅ $fileName baixado com sucesso! ($fileSizeKB KB)" -ForegroundColor Green
    } catch {
        Write-Host "❌ Erro ao baixar $fileName : $_" -ForegroundColor Red
    }
    Write-Host ""
}

Write-Host "=================================================" -ForegroundColor Cyan
Write-Host "  Próximos passos:" -ForegroundColor Cyan
Write-Host "=================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Edite: resources\views\layouts\app.blade.php" -ForegroundColor Yellow
Write-Host "   Trocar:" -ForegroundColor Gray
Write-Host '   <script src="https://sql.js.org/dist/sql-wasm.js"></script>' -ForegroundColor Red
Write-Host "   Por:" -ForegroundColor Gray
Write-Host '   <script src="{{ asset(' + "'libs/sql-wasm.js'" + ') }}"></script>' -ForegroundColor Green
Write-Host ""
Write-Host "2. Edite: public\js\local-database.js" -ForegroundColor Yellow
Write-Host "   No método init(), trocar:" -ForegroundColor Gray
Write-Host '   locateFile: file => `https://sql.js.org/dist/${file}`' -ForegroundColor Red
Write-Host "   Por:" -ForegroundColor Gray
Write-Host '   locateFile: file => `/libs/${file}`' -ForegroundColor Green
Write-Host ""
Write-Host "3. Recarregue a página (F5)" -ForegroundColor Yellow
Write-Host ""
Write-Host "✅ Pronto! Agora o sistema funcionará 100% offline!" -ForegroundColor Green
Write-Host ""
