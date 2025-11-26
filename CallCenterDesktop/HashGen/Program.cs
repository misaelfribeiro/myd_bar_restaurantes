using System;

var admin = BCrypt.Net.BCrypt.HashPassword("admin123");
var super = BCrypt.Net.BCrypt.HashPassword("super123");
var atendente = BCrypt.Net.BCrypt.HashPassword("atendente123");

Console.WriteLine("Admin: " + admin);
Console.WriteLine("Super: " + super);
Console.WriteLine("Atendente: " + atendente);
