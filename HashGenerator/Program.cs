using System;
using BCrypt.Net;

class Program
{
    static void Main()
    {
        string[] senhas = { "admin123", "super123", "atendente123" };
        string[] emails = { "admin@eatsfood.com", "supervisor@eatsfood.com", "atendente@eatsfood.com" };
        string[] niveis = { "admin", "supervisor", "atendente" };
        
        Console.WriteLine("-- Comandos SQL para atualizar senhas no banco");
        Console.WriteLine();
        
        for (int i = 0; i < senhas.Length; i++)
        {
            string hash = BCrypt.Net.BCrypt.HashPassword(senhas[i]);
            Console.WriteLine($"UPDATE usuarios SET password = '{hash}', nivel = '{niveis[i]}' WHERE email = '{emails[i]}';");
        }
        
        Console.WriteLine();
        Console.WriteLine("-- Copie e execute os comandos acima no MySQL");
    }
}
