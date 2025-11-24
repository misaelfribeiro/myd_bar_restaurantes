# Script de teste da API de Produtos
Write-Host "=== TESTE DA API DE PRODUTOS ===" -ForegroundColor Cyan
Write-Host ""

$baseUrl = "http://localhost:8000/api"

# Teste 1: GET /api/produtos (listar todos)
Write-Host "1. GET /api/produtos (Listar todos os produtos)" -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "$baseUrl/produtos" -Method GET -Headers @{"Accept"="application/json"}
    Write-Host "   ✓ Status: 200 OK" -ForegroundColor Green
    Write-Host "   ✓ Total de produtos: $($response.produtos.Count)" -ForegroundColor Green
    if ($response.produtos.Count -gt 0) {
        Write-Host "   ✓ Primeiro produto: $($response.produtos[0].nome) - R$ $($response.produtos[0].preco)" -ForegroundColor Green
    }
} catch {
    Write-Host "   ✗ Erro: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Teste 2: GET /api/produtos/{id} (buscar específico)
Write-Host "2. GET /api/produtos/1 (Buscar produto específico)" -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "$baseUrl/produtos/1" -Method GET -Headers @{"Accept"="application/json"}
    Write-Host "   ✓ Status: 200 OK" -ForegroundColor Green
    Write-Host "   ✓ Produto: $($response.produto.nome)" -ForegroundColor Green
    Write-Host "   ✓ Preço: R$ $($response.produto.preco)" -ForegroundColor Green
    Write-Host "   ✓ Categoria: $($response.produto.categoria.nome)" -ForegroundColor Green
} catch {
    Write-Host "   ✗ Erro: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Teste 3: GET /api/produtos/cache (dados para cache offline)
Write-Host "3. GET /api/produtos/cache (Dados para cache offline)" -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "$baseUrl/produtos/cache" -Method GET -Headers @{"Accept"="application/json"}
    Write-Host "   ✓ Status: 200 OK" -ForegroundColor Green
    Write-Host "   ✓ Produtos disponíveis para cache: $($response.produtos.Count)" -ForegroundColor Green
} catch {
    Write-Host "   ✗ Erro: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== RESUMO DA API ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "Endpoints disponíveis:" -ForegroundColor White
Write-Host "  GET    /api/produtos           - Listar todos os produtos" -ForegroundColor Gray
Write-Host "  GET    /api/produtos/{id}      - Buscar produto específico" -ForegroundColor Gray
Write-Host "  GET    /api/produtos/cache     - Dados para cache offline" -ForegroundColor Gray
Write-Host "  POST   /api/produtos           - Criar novo produto (auth)" -ForegroundColor Gray
Write-Host "  PUT    /api/produtos/{id}      - Atualizar produto (auth)" -ForegroundColor Gray
Write-Host "  DELETE /api/produtos/{id}      - Excluir produto (auth)" -ForegroundColor Gray
Write-Host "  PATCH  /api/produtos/{id}/toggle-status - Toggle status (auth)" -ForegroundColor Gray
Write-Host ""
Write-Host "Autenticacao: Rotas POST/PUT/DELETE requerem token Sanctum" -ForegroundColor Yellow
Write-Host "Permissoes: Admin e Gerente podem criar/editar/excluir produtos" -ForegroundColor Yellow
