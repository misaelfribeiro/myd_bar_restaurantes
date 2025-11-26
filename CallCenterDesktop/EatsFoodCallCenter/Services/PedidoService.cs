using System;
using System.Collections.Generic;
using EatsFoodCallCenter.Models;
using MySql.Data.MySqlClient;

namespace EatsFoodCallCenter.Services
{
    public class PedidoService
    {
        private static PedidoService _instance;

        private PedidoService() { }

        public static PedidoService Instance => _instance ??= new PedidoService();

        public List<Pedido> BuscarPorCliente(string busca)
        {
            var pedidos = new List<Pedido>();
            
            try
            {
                using (var conn = DatabaseService.Instance.GetConnection())
                {
                    var query = @"SELECT p.*, c.nome as cliente_nome, c.telefone as cliente_telefone,
                                        e.nome as entregador_nome
                                 FROM pedidos p
                                 INNER JOIN clientes c ON p.cliente_id = c.id
                                 LEFT JOIN usuarios e ON p.entregador_id = e.id
                                 WHERE c.telefone LIKE @busca OR c.nome LIKE @busca OR c.cpf LIKE @busca
                                       OR p.numero_pedido LIKE @busca
                                 ORDER BY p.created_at DESC
                                 LIMIT 50";
                    
                    using (var cmd = new MySqlCommand(query, conn))
                    {
                        cmd.Parameters.AddWithValue("@busca", $"%{busca}%");
                        
                        using (var reader = cmd.ExecuteReader())
                        {
                            while (reader.Read())
                            {
                                pedidos.Add(MapPedido(reader));
                            }
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                throw new Exception($"Erro ao buscar pedidos: {ex.Message}");
            }
            
            return pedidos;
        }

        public Pedido BuscarPorId(long pedidoId)
        {
            try
            {
                using (var conn = DatabaseService.Instance.GetConnection())
                {
                    var query = @"SELECT p.*, c.nome as cliente_nome, c.telefone as cliente_telefone,
                                        e.nome as entregador_nome
                                 FROM pedidos p
                                 INNER JOIN clientes c ON p.cliente_id = c.id
                                 LEFT JOIN usuarios e ON p.entregador_id = e.id
                                 WHERE p.id = @id";
                    
                    using (var cmd = new MySqlCommand(query, conn))
                    {
                        cmd.Parameters.AddWithValue("@id", pedidoId);
                        
                        using (var reader = cmd.ExecuteReader())
                        {
                            if (reader.Read())
                            {
                                return MapPedido(reader);
                            }
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                throw new Exception($"Erro ao buscar pedido: {ex.Message}");
            }
            
            return null;
        }

        public List<ItemPedido> BuscarItensPedido(long pedidoId)
        {
            var itens = new List<ItemPedido>();
            
            try
            {
                using (var conn = DatabaseService.Instance.GetConnection())
                {
                    var query = @"SELECT ip.*, p.nome as produto_nome,
                                        CASE WHEN e.id IS NOT NULL THEN 1 ELSE 0 END as estornado,
                                        COALESCE(e.status, 'nenhum') as status_estorno
                                 FROM item_pedidos ip
                                 INNER JOIN produtos p ON ip.produto_id = p.id
                                 LEFT JOIN estornos e ON e.item_pedido_id = ip.id
                                 WHERE ip.pedido_id = @pedido_id";
                    
                    using (var cmd = new MySqlCommand(query, conn))
                    {
                        cmd.Parameters.AddWithValue("@pedido_id", pedidoId);
                        
                        using (var reader = cmd.ExecuteReader())
                        {
                            while (reader.Read())
                            {
                                itens.Add(new ItemPedido
                                {
                                    Id = reader.GetInt32("id"),
                                    PedidoId = reader.GetInt32("pedido_id"),
                                    ProdutoId = reader.GetInt32("produto_id"),
                                    Nome = reader.GetString("produto_nome"),
                                    Quantidade = reader.GetInt32("quantidade"),
                                    PrecoUnitario = reader.GetDecimal("preco_unitario"),
                                    Estornado = reader.GetBoolean("estornado"),
                                    StatusEstorno = reader.GetString("status_estorno")
                                });
                            }
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                throw new Exception($"Erro ao buscar itens do pedido: {ex.Message}");
            }
            
            return itens;
        }

        public bool AtualizarStatus(int pedidoId, string novoStatus, int usuarioId)
        {
            try
            {
                using (var conn = DatabaseService.Instance.GetConnection())
                {
                    var query = "UPDATE pedidos SET status = @status WHERE id = @id";
                    
                    using (var cmd = new MySqlCommand(query, conn))
                    {
                        cmd.Parameters.AddWithValue("@status", novoStatus);
                        cmd.Parameters.AddWithValue("@id", pedidoId);
                        
                        var affected = cmd.ExecuteNonQuery();
                        
                        if (affected > 0)
                        {
                            AuditoriaService.Instance.Log(usuarioId, "atualizar_status", "pedido", pedidoId,
                                $"Status alterado para: {novoStatus}");
                            return true;
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                throw new Exception($"Erro ao atualizar status: {ex.Message}");
            }
            
            return false;
        }

        private Pedido MapPedido(MySqlDataReader reader)
        {
            return new Pedido
            {
                Id = reader.GetInt32("id"),
                NumeroPedido = reader.GetInt32("numero_pedido"),
                TenantCode = reader.IsDBNull(reader.GetOrdinal("tenant_code")) ? "" : reader.GetString("tenant_code"),
                ClienteId = reader.GetInt32("cliente_id"),
                ClienteNome = reader.GetString("cliente_nome"),
                ClienteTelefone = reader.GetString("cliente_telefone"),
                Total = reader.GetDecimal("total"),
                TaxaEntrega = reader.GetDecimal("taxa_entrega"),
                Status = reader.GetString("status"),
                FormaPagamento = reader.IsDBNull(reader.GetOrdinal("forma_pagamento")) ? "" : reader.GetString("forma_pagamento"),
                TrocoPara = reader.IsDBNull(reader.GetOrdinal("troco_para")) ? null : reader.GetDecimal("troco_para"),
                Observacoes = reader.IsDBNull(reader.GetOrdinal("observacoes")) ? "" : reader.GetString("observacoes"),
                ObservacoesEntrega = reader.IsDBNull(reader.GetOrdinal("observacoes_entrega")) ? "" : reader.GetString("observacoes_entrega"),
                EntregadorId = reader.IsDBNull(reader.GetOrdinal("entregador_id")) ? null : reader.GetInt32("entregador_id"),
                EntregadorNome = reader.IsDBNull(reader.GetOrdinal("entregador_nome")) ? "" : reader.GetString("entregador_nome"),
                CreatedAt = reader.GetDateTime("created_at"),
                SaiuEntrega = reader.IsDBNull(reader.GetOrdinal("saiu_entrega")) ? null : reader.GetDateTime("saiu_entrega"),
                EntregueEm = reader.IsDBNull(reader.GetOrdinal("entregue_em")) ? null : reader.GetDateTime("entregue_em")
            };
        }
    }
}
