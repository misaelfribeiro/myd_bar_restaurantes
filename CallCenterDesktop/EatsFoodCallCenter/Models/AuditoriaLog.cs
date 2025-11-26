using System;

namespace EatsFoodCallCenter.Models
{
    public class AuditoriaLog
    {
        public int Id { get; set; }
        public int UsuarioId { get; set; }
        public string UsuarioNome { get; set; }
        public string Acao { get; set; }
        public string Entidade { get; set; } // pedido, estorno, cliente
        public int? EntidadeId { get; set; }
        public string Detalhes { get; set; }
        public string IpAddress { get; set; }
        public DateTime CreatedAt { get; set; }
    }
}
