using System;
using BCrypt.Net;

Console.WriteLine("=== Gerador de Senhas Hash BCrypt ===\n");

var usuarios = new[]
{
    new { Nome = "Administrador", Email = "admin@eatsfood.com", Senha = "admin123", Nivel = "admin" },
    new { Nome = "Supervisor", Email = "supervisor@eatsfood.com", Senha = "super123", Nivel = "supervisor" },
    new { Nome = "Atendente Teste", Email = "atendente@eatsfood.com", Senha = "atendente123", Nivel = "atendente" }
};

Console.WriteLine("-- Script SQL para inserir usuários do Call Center\n");

foreach (var user in usuarios)
{
    string hash = BCrypt.Net.BCrypt.HashPassword(user.Senha);
    
    Console.WriteLine($"-- {user.Nome} | Email: {user.Email} | Senha: {user.Senha}");
    Console.WriteLine($"INSERT INTO `usuarios` (`tenant_code`, `ativo`, `nome`, `email`, `role`, `password`, `nivel`, `created_at`)");
    Console.WriteLine($"VALUES (NULL, 1, '{user.Nome}', '{user.Email}', 'admin', '{hash}', '{user.Nivel}', NOW())");
    Console.WriteLine("ON DUPLICATE KEY UPDATE password = VALUES(password), nivel = VALUES(nivel), ativo = 1;\n");
}

Console.WriteLine("\n=== Copie e execute o SQL acima no MySQL ===");
