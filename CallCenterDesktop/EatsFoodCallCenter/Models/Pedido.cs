using System;
using System.Collections.Generic;

namespace EatsFoodCallCenter.Models
{
    public class Pedido
    {
        public int Id { get; set; }
        public int NumeroPedido { get; set; }
        public string TenantCode { get; set; }
        public int ClienteId { get; set; }
        public string ClienteNome { get; set; }
        public string ClienteTelefone { get; set; }
        public decimal Total { get; set; }
        public decimal TaxaEntrega { get; set; }
        public string Status { get; set; }
        public string FormaPagamento { get; set; }
        public decimal? TrocoPara { get; set; }
        public string Observacoes { get; set; }
        public string ObservacoesEntrega { get; set; }
        public int? EntregadorId { get; set; }
        public string EntregadorNome { get; set; }
        public DateTime CreatedAt { get; set; }
        public DateTime? SaiuEntrega { get; set; }
        public DateTime? EntregueEm { get; set; }
        
        public List<ItemPedido> Itens { get; set; } = new List<ItemPedido>();
        
        public string StatusFormatado => Status switch
        {
            "pendente" => "Pendente",
            "confirmado" => "Confirmado",
            "preparando" => "Preparando",
            "pronto" => "Pronto",
            "enviado" => "Em Entrega",
            "entregue" => "Entregue",
            "cancelado" => "Cancelado",
            _ => Status
        };
    }

    public class ItemPedido
    {
        public int Id { get; set; }
        public int PedidoId { get; set; }
        public int ProdutoId { get; set; }
        public string Nome { get; set; }
        public int Quantidade { get; set; }
        public decimal PrecoUnitario { get; set; }
        public decimal Subtotal => Quantidade * PrecoUnitario;
        public bool Estornado { get; set; }
        public string StatusEstorno { get; set; } = "nenhum";
        
        public string StatusEstornoFormatado => StatusEstorno switch
        {
            "pendente" => "⏳ PENDENTE",
            "aprovado" => "✅ APROVADO",
            "rejeitado" => "❌ REJEITADO",
            _ => "-"
        };
    }
}
