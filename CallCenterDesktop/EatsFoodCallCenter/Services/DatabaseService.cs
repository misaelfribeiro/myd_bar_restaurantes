using System;
using System.IO;
using MySql.Data.MySqlClient;
using Newtonsoft.Json.Linq;

namespace EatsFoodCallCenter.Services
{
    public class DatabaseService
    {
        private static DatabaseService _instance;
        private string _connectionString;

        private DatabaseService()
        {
            LoadConfiguration();
        }

        public static DatabaseService Instance => _instance ??= new DatabaseService();

        private void LoadConfiguration()
        {
            try
            {
                var jsonPath = Path.Combine(AppDomain.CurrentDomain.BaseDirectory, "appsettings.json");
                var json = File.ReadAllText(jsonPath);
                var config = JObject.Parse(json);
                
                var db = config["Database"];
                _connectionString = $"Server={db["Server"]};Port={db["Port"]};Database={db["Database"]};" +
                                  $"Uid={db["User"]};Pwd={db["Password"]};Connection Timeout={db["ConnectionTimeout"]};";
            }
            catch (Exception ex)
            {
                throw new Exception($"Erro ao carregar configurações: {ex.Message}");
            }
        }

        public MySqlConnection GetConnection()
        {
            try
            {
                var connection = new MySqlConnection(_connectionString);
                connection.Open();
                return connection;
            }
            catch (Exception ex)
            {
                throw new Exception($"Erro ao conectar ao banco de dados: {ex.Message}");
            }
        }

        public bool TestConnection()
        {
            try
            {
                using (var conn = GetConnection())
                {
                    return conn.State == System.Data.ConnectionState.Open;
                }
            }
            catch
            {
                return false;
            }
        }
    }
}
