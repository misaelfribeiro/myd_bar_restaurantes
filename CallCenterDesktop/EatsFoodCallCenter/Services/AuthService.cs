using System;
using System.Data;
using EatsFoodCallCenter.Models;
using MySql.Data.MySqlClient;
using BCrypt.Net;

namespace EatsFoodCallCenter.Services
{
    public class AuthService
    {
        private static AuthService _instance;
        private Usuario _currentUser;

        private AuthService() { }

        public static AuthService Instance => _instance ??= new AuthService();

        public Usuario CurrentUser => _currentUser;
        public bool IsAuthenticated => _currentUser != null;

        public (bool success, string message, Usuario user) Login(string email, string senha)
        {
            try
            {
                using (var conn = DatabaseService.Instance.GetConnection())
                {
                    var query = @"SELECT id, nome, email, password, nivel, ativo, login_attempts, locked_until, last_login, created_at 
                                 FROM usuarios WHERE email = @email";
                    
                    using (var cmd = new MySqlCommand(query, conn))
                    {
                        cmd.Parameters.AddWithValue("@email", email);
                        
                        using (var reader = cmd.ExecuteReader())
                        {
                            if (!reader.Read())
                                return (false, "Usuário não encontrado", null);

                            var user = new Usuario
                            {
                                Id = reader.GetInt32("id"),
                                Nome = reader.GetString("nome"),
                                Email = reader.GetString("email"),
                                Senha = reader.GetString("password"),
                                Nivel = reader.GetString("nivel"),
                                Ativo = reader.GetBoolean("ativo"),
                                LoginAttempts = reader.IsDBNull("login_attempts") ? 0 : reader.GetInt32("login_attempts"),
                                LockedUntil = reader.IsDBNull("locked_until") ? null : reader.GetDateTime("locked_until"),
                                LastLogin = reader.IsDBNull("last_login") ? null : reader.GetDateTime("last_login"),
                                CreatedAt = reader.GetDateTime("created_at")
                            };

                            if (!user.Ativo)
                                return (false, "Usuário inativo. Contate o administrador.", null);

                            if (user.IsLocked)
                                return (false, $"Conta bloqueada até {user.LockedUntil:HH:mm}", null);

                            reader.Close();

                            // Verificar senha
                            if (!BCrypt.Net.BCrypt.Verify(senha, user.Senha))
                            {
                                IncrementLoginAttempts(user.Id);
                                return (false, "Senha incorreta", null);
                            }

                            // Login bem-sucedido
                            ResetLoginAttempts(user.Id);
                            UpdateLastLogin(user.Id);
                            _currentUser = user;
                            
                            // Log de auditoria
                            AuditoriaService.Instance.Log(user.Id, "login", "usuario", user.Id, 
                                $"Login realizado de {GetLocalIP()}");

                            return (true, "Login realizado com sucesso", user);
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                return (false, $"Erro no login: {ex.Message}", null);
            }
        }

        public void Logout()
        {
            if (_currentUser != null)
            {
                AuditoriaService.Instance.Log(_currentUser.Id, "logout", "usuario", _currentUser.Id, "Logout realizado");
                _currentUser = null;
            }
        }

        private void IncrementLoginAttempts(int userId)
        {
            using (var conn = DatabaseService.Instance.GetConnection())
            {
                var query = @"UPDATE usuarios 
                             SET login_attempts = login_attempts + 1,
                                 locked_until = CASE WHEN login_attempts >= 2 THEN DATE_ADD(NOW(), INTERVAL 15 MINUTE) ELSE NULL END
                             WHERE id = @id";
                
                using (var cmd = new MySqlCommand(query, conn))
                {
                    cmd.Parameters.AddWithValue("@id", userId);
                    cmd.ExecuteNonQuery();
                }
            }
        }

        private void ResetLoginAttempts(int userId)
        {
            using (var conn = DatabaseService.Instance.GetConnection())
            {
                var query = "UPDATE usuarios SET login_attempts = 0, locked_until = NULL WHERE id = @id";
                using (var cmd = new MySqlCommand(query, conn))
                {
                    cmd.Parameters.AddWithValue("@id", userId);
                    cmd.ExecuteNonQuery();
                }
            }
        }

        private void UpdateLastLogin(int userId)
        {
            using (var conn = DatabaseService.Instance.GetConnection())
            {
                var query = "UPDATE usuarios SET last_login = NOW() WHERE id = @id";
                using (var cmd = new MySqlCommand(query, conn))
                {
                    cmd.Parameters.AddWithValue("@id", userId);
                    cmd.ExecuteNonQuery();
                }
            }
        }

        private string GetLocalIP()
        {
            try
            {
                var host = System.Net.Dns.GetHostEntry(System.Net.Dns.GetHostName());
                foreach (var ip in host.AddressList)
                {
                    if (ip.AddressFamily == System.Net.Sockets.AddressFamily.InterNetwork)
                        return ip.ToString();
                }
            }
            catch { }
            return "127.0.0.1";
        }
    }
}
