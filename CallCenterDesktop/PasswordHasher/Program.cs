using System;
using BCrypt.Net;

namespace PasswordHasher
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Gerador de Hashes BCrypt ===\n");
            
            var senhas = new[]
            {
                new { Nome = "Admin", Email = "admin@eatsfood.com", Senha = "admin123", Nivel = "admin" },
                new { Nome = "Supervisor", Email = "supervisor@eatsfood.com", Senha = "super123", Nivel = "supervisor" },
                new { Nome = "Atendente Teste", Email = "atendente@eatsfood.com", Senha = "atendente123", Nivel = "atendente" }
            };
            
            Console.WriteLine("-- Script SQL para inserir usuários\n");
            
            foreach (var user in senhas)
            {
                string hash = BCrypt.Net.BCrypt.HashPassword(user.Senha);
                
                Console.WriteLine($"-- {user.Nome} ({user.Email}) | Senha: {user.Senha}");
                Console.WriteLine($"INSERT INTO `usuarios` (`tenant_code`, `ativo`, `nome`, `email`, `role`, `password`, `nivel`, `created_at`)");
                Console.WriteLine($"VALUES (NULL, 1, '{user.Nome}', '{user.Email}', 'admin', '{hash}', '{user.Nivel}', NOW())");
                Console.WriteLine("ON DUPLICATE KEY UPDATE password = VALUES(password), nivel = VALUES(nivel);\n");
            }
            
            Console.WriteLine("\nPressione qualquer tecla para sair...");
            Console.ReadKey();
        }
    }
}
