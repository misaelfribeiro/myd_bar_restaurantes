<!DOCTYPE html>
<html lang="pt-BR">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Abertura de Caixa - MyD Bar & Restaurantes</title>
 <!-- Bootstrap CSS -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <!-- Bootstrap Icons -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
 <style>
 .abertura-container {
 min-height: 100vh;
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 display: flex;
 align-items: center;
 justify-content: center;
 }
 .abertura-card {
 background: white;
 border-radius: 20px;
 box-shadow: 0 20px 60px rgba(0,0,0,0.1);
 padding: 3rem;
 max-width: 500px;
 width: 100%;
 }
 .abertura-icon {
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 color: white;
 width: 80px;
 height: 80px;
 border-radius: 50%;
 display: flex;
 align-items: center;
 justify-content: center;
 font-size: 2rem;
 margin: 0 auto 2rem;
 }
 .form-control:focus {
 border-color: #667eea;
 box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
 }
 .btn-primary {
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 border: none;
 padding: 12px 30px;
 border-radius: 10px;
 font-weight: 600;
 }
 .btn-primary:hover {
 background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
 transform: translateY(-2px);
 }
 </style>
</head>
<body>
 <div class="abertura-container">
 <div class="abertura-card">
 <div class="abertura-icon">
 <i class="bi bi-cash-coin"></i>
 </div>
 <h1 class="text-center mb-4">Abertura de Caixa</h1>
 <p class="text-center text-muted mb-4">Para iniciar as operações, é necessário abrir o caixa com o saldo inicial.</p>
 @if(session('error'))
 <div class="alert alert-danger">
 <i class="bi bi-exclamation-triangle me-2"></i>
 {{ session('error') }}
 </div>
 @endif
 <form action="{{ route('caixa.abrir') }}" method="POST">
 @csrf
 <div class="mb-4">                    <label for="saldo_inicial" class="form-label">
 <i class="bi bi-cash me-2"></i>
 Saldo Inicial em Dinheiro
 </label>
 <div class="input-group">
 <span class="input-group-text">R$</span>
 <input type="number" 
 class="form-control @error('saldo_inicial') is-invalid @enderror" 
 id="saldo_inicial" 
 name="saldo_inicial" 
 value="{{ old('saldo_inicial', '0.00') }}"
 step="0.01" 
 min="0" 
 required>
 </div>
 @error('saldo_inicial')
 <div class="invalid-feedback">{{ $message }}</div>
 @enderror
 <small class="form-text text-muted">Valor em caixa para troco no início do dia</small>
 </div>
 <div class="mb-4">
 <label for="observacoes" class="form-label">
 <i class="bi bi-chat-text me-2"></i>
 Observações
 </label>
 <textarea class="form-control" 
 id="observacoes" 
 name="observacoes" 
 rows="3" 
 placeholder="Observações sobre a abertura do caixa (opcional)">{{ old('observacoes') }}</textarea>
 </div>
 <div class="d-grid gap-2">
 <button type="submit" class="btn btn-primary">
 <i class="bi bi-unlock me-2"></i>
 Abrir Caixa
 </button>
 <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
 <i class="bi bi-arrow-left me-2"></i>
 Voltar ao Dashboard
 </a>
 </div>
 </form>
 <div class="mt-4 text-center">
 <small class="text-muted">
 <i class="bi bi-info-circle me-1"></i>
 Data/Hora: {{ now()->format('d/m/Y H:i:s') }}
 </small>
 </div>
 </div>
 </div>
 <!-- Bootstrap JS -->
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 <script>
 document.getElementById('saldo_inicial').focus();
 document.getElementById('saldo_inicial').addEventListener('input', function(e) {
 let value = e.target.value;
 if (value && !value.includes('.') && value.length > 2) {
 value = value.slice(0, -2) + '.' + value.slice(-2);
 e.target.value = value;
 }
 });
 </script>
</body>
</html>