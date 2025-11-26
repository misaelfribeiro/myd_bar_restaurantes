using System;

namespace EatsFoodCallCenter.Models
{
    public class Usuario
    {
        public int Id { get; set; }
        public string Nome { get; set; }
        public string Email { get; set; }
        public string Senha { get; set; }
        public string Nivel { get; set; } // admin, supervisor, atendente
        public bool Ativo { get; set; }
        public DateTime CreatedAt { get; set; }
        public DateTime? LastLogin { get; set; }
        public int LoginAttempts { get; set; }
        public DateTime? LockedUntil { get; set; }

        public bool IsAdmin => Nivel?.ToLower() == "admin";
        public bool IsSupervisor => Nivel?.ToLower() == "supervisor";
        public bool IsAtendente => Nivel?.ToLower() == "atendente";
        public bool IsLocked => LockedUntil.HasValue && LockedUntil.Value > DateTime.Now;
    }
}
