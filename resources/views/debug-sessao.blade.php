<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug de Sessão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-warning">
                <h4 class="mb-0">🔍 DEBUG DE SESSÃO</h4>
            </div>
            <div class="card-body">
                <h5>Guard WEB (usuarios table):</h5>
                <div class="alert alert-info">
                    <strong>Autenticado:</strong> {{ auth()->guard('web')->check() ? 'SIM ✓' : 'NÃO ✗' }}<br>
                    @if(auth()->guard('web')->check())
                        <strong>ID:</strong> {{ auth()->guard('web')->user()->id }}<br>
                        <strong>Nome:</strong> {{ auth()->guard('web')->user()->nome }}<br>
                        <strong>Email:</strong> {{ auth()->guard('web')->user()->email }}<br>
                        <strong>Role:</strong> {{ auth()->guard('web')->user()->role }}<br>
                        <strong>Tenant Code:</strong> {{ auth()->guard('web')->user()->tenant_code ?? 'NULL' }}<br>
                    @endif
                </div>

                <h5>Guard ADMIN (users table):</h5>
                <div class="alert alert-danger">
                    <strong>Autenticado:</strong> {{ auth()->guard('admin')->check() ? 'SIM ✓' : 'NÃO ✗' }}<br>
                    @if(auth()->guard('admin')->check())
                        <strong>ID:</strong> {{ auth()->guard('admin')->user()->id }}<br>
                        <strong>Nome:</strong> {{ auth()->guard('admin')->user()->name }}<br>
                        <strong>Email:</strong> {{ auth()->guard('admin')->user()->email }}<br>
                        <strong>Tenant Code:</strong> {{ auth()->guard('admin')->user()->tenant_code ?? 'NULL' }}<br>
                    @endif
                </div>

                <h5>Sessão PHP:</h5>
                <div class="alert alert-secondary">
                    <strong>Session ID:</strong> {{ session()->getId() }}<br>
                    <strong>CSRF Token:</strong> {{ csrf_token() }}<br>
                </div>

                <h5>URL Atual:</h5>
                <div class="alert alert-primary">
                    <strong>URL:</strong> {{ url()->current() }}<br>
                    <strong>Route Name:</strong> {{ Route::currentRouteName() ?? 'Sem nome' }}<br>
                </div>

                <hr>

                <a href="/logout-forcado" class="btn btn-danger">Fazer Logout Forçado</a>
                <a href="/" class="btn btn-primary">Ir para Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
