using System;

namespace EatsFoodCallCenter.Models
{
    public class Estorno
    {
        public int Id { get; set; }
        public int PedidoId { get; set; }
        public int? ItemPedidoId { get; set; } // null = estorno total
        public string Tipo { get; set; } // parcial, total
        public decimal Valor { get; set; }
        public string Motivo { get; set; }
        public int SolicitadoPor { get; set; }
        public string SolicitadoPorNome { get; set; }
        public int? AprovadoPor { get; set; }
        public string AprovadoPorNome { get; set; }
        public string Status { get; set; } // pendente, aprovado, rejeitado
        public DateTime SolicitadoEm { get; set; }
        public DateTime? ProcessadoEm { get; set; }
        public string ObservacoesAprovacao { get; set; }

        public bool IsPendente => Status == "pendente";
        public bool IsAprovado => Status == "aprovado";
        public bool IsRejeitado => Status == "rejeitado";
        public bool IsParcial => Tipo == "parcial";
        public bool IsTotal => Tipo == "total";
    }
}
