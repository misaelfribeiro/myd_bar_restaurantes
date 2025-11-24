# Configuração Apache - Guia Rápido

## Opção 1: Script Automatizado (RECOMENDADO)

1. **Feche o VS Code**

2. **Abra PowerShell como ADMINISTRADOR**
   - Clique com botão direito no menu Iniciar
   - Selecione "Windows PowerShell (Admin)"

3. **Execute o script**
   ```powershell
   cd C:\xampp\htdocs\myd_bar_restaurantes
   .\configurar-apache-simples.ps1
   ```

4. **Aguarde a conclusão**
   - O script vai configurar tudo automaticamente
   - Pressione qualquer tecla no final

5. **Acesse o sistema**
   - Abra o navegador em: `http://myd.local/login-niveis`

---

## Opção 2: Configuração Manual

### Passo 1: Editar httpd.conf

Abra: `C:\xampp\apache\conf\httpd.conf`

Adicione no final:
```apache
# MyD Bar e Restaurantes Virtual Host
Include conf/extra/httpd-vhosts-myd.conf
```

### Passo 2: Editar arquivo hosts

**Como Administrador**, abra: `C:\Windows\System32\drivers\etc\hosts`

Adicione:
```
127.0.0.1    myd.local
127.0.0.1    www.myd.local
```

### Passo 3: Configurar Permissões

Execute no PowerShell (como Admin):
```powershell
cd C:\xampp\htdocs\myd_bar_restaurantes
icacls storage /grant Everyone:(OI)(CI)F /T
icacls bootstrap\cache /grant Everyone:(OI)(CI)F /T
```

### Passo 4: Limpar Cache Laravel

```powershell
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Passo 5: Atualizar .env

Edite `.env` e altere:
```
APP_URL=http://myd.local
```

### Passo 6: Reiniciar Apache

- Abra o XAMPP Control Panel
- Pare o Apache (botão "Stop")
- Inicie novamente (botão "Start")

### Passo 7: Testar

Acesse: `http://myd.local/login-niveis`

---

## Verificação Rápida

Execute no PowerShell para verificar se está configurado:

```powershell
# Verificar httpd.conf
Select-String -Path "C:\xampp\apache\conf\httpd.conf" -Pattern "httpd-vhosts-myd"

# Verificar hosts
Select-String -Path "C:\Windows\System32\drivers\etc\hosts" -Pattern "myd.local"

# Verificar se Apache está rodando
Get-Service -Name "Apache2.4"
```

---

## Usuários Demo

Após configurar, faça login com:

| Email | Senha | Nível |
|-------|-------|-------|
| admin@exemplo.com | 123456 | Administrador |
| gerente@exemplo.com | 123456 | Gerente |
| garcom@exemplo.com | 123456 | Garçom |
| caixa@exemplo.com | 123456 | Caixa |

---

## Problemas Comuns

### "Este site não pode ser acessado"
- Verifique se o Apache está rodando no XAMPP
- Confirme que adicionou myd.local no arquivo hosts

### "403 Forbidden"
- Execute os comandos de permissão (icacls) como Administrador
- Verifique se a pasta public existe

### "500 Internal Server Error"
- Limpe o cache do Laravel: `php artisan cache:clear`
- Verifique se o .env está configurado
- Veja os logs em: `C:\xampp\apache\logs\myd-error.log`

### Ainda usando porta 8000
- Verifique se configurou o Include no httpd.conf
- Reinicie o Apache pelo XAMPP Control Panel

---

## Comandos Úteis

```powershell
# Ver logs de erro do Apache
Get-Content C:\xampp\apache\logs\myd-error.log -Tail 20

# Ver logs do Laravel
Get-Content storage\logs\laravel.log -Tail 20

# Reiniciar Apache via PowerShell (Admin)
Restart-Service -Name "Apache2.4"

# Verificar porta do Apache
netstat -ano | findstr :80
```
