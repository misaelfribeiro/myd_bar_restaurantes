# Guia de Configuração do Apache para MyD Bar & Restaurantes

## 🚀 Passo a Passo para Configurar o Apache

### 1️⃣ **Incluir o Virtual Host no httpd.conf**

Abra o arquivo: `C:\xampp\apache\conf\httpd.conf`

No final do arquivo, adicione:
```apache
# MyD Bar & Restaurantes Virtual Host
Include conf/extra/httpd-vhosts-myd.conf
```

### 2️⃣ **Adicionar domínio no arquivo hosts**

Abra o arquivo: `C:\Windows\System32\drivers\etc\hosts`

⚠️ **IMPORTANTE**: Abra o Notepad como Administrador!

Adicione as seguintes linhas:
```
127.0.0.1    myd.local
127.0.0.1    www.myd.local
```

### 3️⃣ **Verificar módulos do Apache**

Abra: `C:\xampp\apache\conf\httpd.conf`

Certifique-se que os seguintes módulos estão descomentados (sem #):

```apache
LoadModule rewrite_module modules/mod_rewrite.so
LoadModule ssl_module modules/mod_ssl.so
LoadModule socache_shmcb_module modules/mod_socache_shmcb.so
```

### 4️⃣ **Configurar permissões do Laravel**

Execute no PowerShell (como Administrador):

```powershell
# Ir para o diretório do projeto
cd C:\xampp\htdocs\myd_bar_restaurantes

# Dar permissões para storage e bootstrap/cache
icacls storage /grant Everyone:(OI)(CI)F /T
icacls bootstrap\cache /grant Everyone:(OI)(CI)F /T

# Limpar cache do Laravel
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 5️⃣ **Reiniciar o Apache**

No XAMPP Control Panel:
1. Clique em **Stop** no Apache
2. Aguarde parar completamente
3. Clique em **Start** no Apache

Ou via PowerShell:
```powershell
# Parar Apache
net stop Apache2.4

# Iniciar Apache
net start Apache2.4
```

### 6️⃣ **Testar a Configuração**

Acesse no navegador:
- **HTTP**: `http://myd.local`
- **HTTPS**: `https://myd.local` (se configurou SSL)
- **Login**: `http://myd.local/login-niveis`
- **Dashboard**: `http://myd.local/dashboard-niveis`

---

## 📋 Checklist de Verificação

- [ ] Arquivo `httpd-vhosts-myd.conf` criado
- [ ] Include adicionado no `httpd.conf`
- [ ] Domínio `myd.local` adicionado no arquivo hosts
- [ ] Módulo `mod_rewrite` habilitado
- [ ] Permissões do storage configuradas
- [ ] Apache reiniciado
- [ ] Site acessível em `http://myd.local`

---

## 🔧 Troubleshooting

### Erro "Forbidden"
```powershell
# Verifique as permissões:
icacls "C:\xampp\htdocs\myd_bar_restaurantes" /grant Everyone:(OI)(CI)F /T
```

### Erro "Internal Server Error"
```powershell
# Limpe o cache do Laravel:
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Apache não inicia
1. Verifique se há erros de sintaxe:
```powershell
C:\xampp\apache\bin\httpd.exe -t
```

2. Verifique se a porta 80 está livre:
```powershell
netstat -ano | findstr :80
```

### Página em branco
1. Verifique os logs:
   - `C:\xampp\apache\logs\myd-error.log`
   - `C:\xampp\htdocs\myd_bar_restaurantes\storage\logs\laravel.log`

2. Ative debug no `.env`:
```env
APP_DEBUG=true
```

---

## ⚡ Otimizações de Performance

### Cache de Configuração (Produção)
```powershell
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### OPcache (php.ini)
Adicione em `C:\xampp\php\php.ini`:
```ini
[opcache]
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### Apache MPM (httpd.conf)
```apache
<IfModule mpm_winnt_module>
    ThreadsPerChild      250
    MaxRequestsPerChild  0
</IfModule>
```

---

## 🌐 Variáveis de Ambiente (.env)

Atualize o APP_URL:
```env
APP_URL=http://myd.local
```

---

## 📊 Monitoramento

### Logs em tempo real
```powershell
# Apache Access Log
Get-Content C:\xampp\apache\logs\myd-access.log -Wait

# Apache Error Log
Get-Content C:\xampp\apache\logs\myd-error.log -Wait

# Laravel Log
Get-Content C:\xampp\htdocs\myd_bar_restaurantes\storage\logs\laravel.log -Wait
```

---

## 🎯 Próximos Passos

1. ✅ Configurar Apache na porta 80
2. ⚠️ Configurar SSL/HTTPS (opcional)
3. ⚠️ Configurar backup automático
4. ⚠️ Implementar rate limiting
5. ⚠️ Configurar monitoramento de performance

---

## 📝 Notas Importantes

- 🔒 **Nunca** coloque `APP_DEBUG=true` em produção
- 📁 **Sempre** mantenha backups do banco de dados
- 🔐 **Configure** SSL para ambientes de produção
- 📊 **Monitore** os logs regularmente
- 🚀 **Otimize** queries do banco de dados

---

## ✅ Configuração Completa!

Após seguir todos os passos acima, seu sistema estará rodando no Apache com performance otimizada! 🎉
