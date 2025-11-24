<!DOCTYPE html>
<html>
<head>
    <title>Logout Forçado</title>
</head>
<body>
    <h1>Limpando sessão...</h1>
    <script>
        // Limpar tudo do navegador
        localStorage.clear();
        sessionStorage.clear();
        
        // Fazer logout no servidor
        fetch('/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => {
            // Redirecionar para login
            window.location.href = '/login';
        });
    </script>
</body>
</html>
