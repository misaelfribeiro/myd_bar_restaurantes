using System;
using MySql.Data.MySqlClient;

namespace EatsFoodCallCenter.Services
{
    public class AuditoriaService
    {
        private static AuditoriaService _instance;

        private AuditoriaService() { }

        public static AuditoriaService Instance => _instance ??= new AuditoriaService();

        public void Log(int usuarioId, string acao, string entidade, long? entidadeId, string detalhes)
        {
            try
            {
                using (var conn = DatabaseService.Instance.GetConnection())
                {
                    var query = @"INSERT INTO auditoria_logs (usuario_id, acao, entidade, entidade_id, detalhes, ip_address, created_at)
                                 VALUES (@usuario_id, @acao, @entidade, @entidade_id, @detalhes, @ip, NOW())";
                    
                    using (var cmd = new MySqlCommand(query, conn))
                    {
                        cmd.Parameters.AddWithValue("@usuario_id", usuarioId);
                        cmd.Parameters.AddWithValue("@acao", acao);
                        cmd.Parameters.AddWithValue("@entidade", entidade);
                        cmd.Parameters.AddWithValue("@entidade_id", entidadeId.HasValue ? (object)entidadeId.Value : DBNull.Value);
                        cmd.Parameters.AddWithValue("@detalhes", detalhes ?? "");
                        cmd.Parameters.AddWithValue("@ip", GetLocalIP());
                        
                        cmd.ExecuteNonQuery();
                    }
                }
            }
            catch (Exception ex)
            {
                // Log de auditoria não deve quebrar o sistema
                System.Diagnostics.Debug.WriteLine($"Erro ao registrar auditoria: {ex.Message}");
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
