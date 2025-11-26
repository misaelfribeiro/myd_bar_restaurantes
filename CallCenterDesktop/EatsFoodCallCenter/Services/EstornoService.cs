using System;
using System.Collections.Generic;
using EatsFoodCallCenter.Models;
using MySql.Data.MySqlClient;

namespace EatsFoodCallCenter.Services
{
    public class EstornoService
    {
        private static EstornoService _instance;

        private EstornoService() { }

        public static EstornoService Instance => _instance ??= new EstornoService();

        public (bool success, string message) SolicitarEstornoParcial(long pedidoId, long itemPedidoId, string motivo, int usuarioId)
        {
            try
            {
                // Buscar valor do item
                decimal valorItem = 0;
                using (var conn = DatabaseService.Instance.GetConnection())
                {
                    var query = "SELECT quantidade * preco_unitario as valor FROM item_pedidos WHERE id = @id";
                    using (var cmd = new MySqlCommand(query, conn))
                    {
                        cmd.Parameters.AddWithValue("@id", itemPedidoId);
                        var result = cmd.ExecuteScalar();
                        if (result != null)
                            valorItem = Convert.ToDecimal(result);
                    }
                }

                if (valorItem == 0)
                    return (false, "Item não encontrado");

                // Verificar se já existe estorno para este item
                using (var conn = DatabaseService.Instance.GetConnection())
                {
                    var checkQuery = "SELECT COUNT(*) FROM estornos WHERE item_pedido_id = @item_id AND status != 'rejeitado'";
                    using (var cmd = new MySqlCommand(checkQuery, conn))
                    {
                        cmd.Parameters.AddWithValue("@item_id", itemPedidoId);
                        var result = cmd.ExecuteScalar();
                        var count = result != null ? Convert.ToInt32(result) : 0;
                        if (count > 0)
                            return (false, "Já existe uma solicitação de estorno para este item");
                    }
                }

                // Criar solicitação de estorno
                using (var conn = DatabaseService.Instance.GetConnection())
                {
                    var query = @"INSERT INTO estornos (pedido_id, item_pedido_id, tipo, valor, motivo, 
                                                       solicitado_por, status, solicitado_em)
                                 VALUES (@pedido_id, @item_id, 'parcial', @valor, @motivo, @usuario_id, 'pendente', NOW())";
                    
                    using (var cmd = new MySqlCommand(query, conn))
                    {
                        cmd.Parameters.AddWithValue("@pedido_id", pedidoId);
                        cmd.Parameters.AddWithValue("@item_id", itemPedidoId);
                        cmd.Parameters.AddWithValue("@valor", valorItem);
                        cmd.Parameters.AddWithValue("@motivo", motivo);
                        cmd.Parameters.AddWithValue("@usuario_id", usuarioId);
                        
                        cmd.ExecuteNonQuery();
                    }
                }

                AuditoriaService.Instance.Log(usuarioId, "solicitar_estorno_parcial", "pedido", pedidoId,
                    $"Estorno parcial solicitado - Item {itemPedidoId} - Valor: R$ {valorItem:F2} - Motivo: {motivo}");

                return (true, "Solicitação de estorno parcial enviada para aprovação");
            }
            catch (Exception ex)
            {
                return (false, $"Erro ao solicitar estorno: {ex.Message}");
            }
        }

        public (bool success, string message) SolicitarEstornoTotal(long pedidoId, string motivo, int usuarioId)
        {
            try
            {
                // Buscar valor total do pedido
                decimal valorTotal = 0;
                using (var conn = DatabaseService.Instance.GetConnection())
                {
                    var query = "SELECT total FROM pedidos WHERE id = @id";
                    using (var cmd = new MySqlCommand(query, conn))
                    {
                        cmd.Parameters.AddWithValue("@id", pedidoId);
                        var result = cmd.ExecuteScalar();
                        if (result != null)
                            valorTotal = Convert.ToDecimal(result);
                    }
                }

                if (valorTotal == 0)
                    return (false, "Pedido não encontrado");

                // Verificar se já existe estorno total
                using (var conn = DatabaseService.Instance.GetConnection())
                {
                    var checkQuery = "SELECT COUNT(*) FROM estornos WHERE pedido_id = @pedido_id AND tipo = 'total' AND status != 'rejeitado'";
                    using (var cmd = new MySqlCommand(checkQuery, conn))
                    {
                        cmd.Parameters.AddWithValue("@pedido_id", pedidoId);
                        var count = Convert.ToInt32(cmd.ExecuteScalar());
                        if (count > 0)
                            return (false, "Já existe uma solicitação de estorno total para este pedido");
                    }
                }

                // Criar solicitação de estorno
                using (var conn = DatabaseService.Instance.GetConnection())
                {
                    var query = @"INSERT INTO estornos (pedido_id, tipo, valor, motivo, solicitado_por, status, solicitado_em)
                                 VALUES (@pedido_id, 'total', @valor, @motivo, @usuario_id, 'pendente', NOW())";
                    
                    using (var cmd = new MySqlCommand(query, conn))
                    {
                        cmd.Parameters.AddWithValue("@pedido_id", pedidoId);
                        cmd.Parameters.AddWithValue("@valor", valorTotal);
                        cmd.Parameters.AddWithValue("@motivo", motivo);
                        cmd.Parameters.AddWithValue("@usuario_id", usuarioId);
                        
                        cmd.ExecuteNonQuery();
                    }
                }

                AuditoriaService.Instance.Log(usuarioId, "solicitar_estorno_total", "pedido", pedidoId,
                    $"Estorno total solicitado - Valor: R$ {valorTotal:F2} - Motivo: {motivo}");

                return (true, "Solicitação de estorno total enviada para aprovação");
            }
            catch (Exception ex)
            {
                return (false, $"Erro ao solicitar estorno: {ex.Message}");
            }
        }

        public List<Estorno> ListarPendentes()
        {
            var estornos = new List<Estorno>();
            
            try
            {
                using (var conn = DatabaseService.Instance.GetConnection())
                {
                    var query = @"SELECT e.*, u.nome as solicitado_por_nome
                                 FROM estornos e
                                 INNER JOIN usuarios u ON e.solicitado_por = u.id
                                 WHERE e.status = 'pendente'
                                 ORDER BY e.solicitado_em DESC";
                    
                    using (var cmd = new MySqlCommand(query, conn))
                    {
                        using (var reader = cmd.ExecuteReader())
                        {
                            while (reader.Read())
                            {
                                estornos.Add(MapEstorno(reader));
                            }
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                throw new Exception($"Erro ao listar estornos pendentes: {ex.Message}");
            }
            
            return estornos;
        }

        public (bool success, string message) AprovarEstorno(long estornoId, int supervisorId, string observacoes = null)
        {
            try
            {
                using (var conn = DatabaseService.Instance.GetConnection())
                {
                    var query = @"UPDATE estornos 
                                 SET status = 'aprovado', aprovado_por = @supervisor_id, 
                                     processado_em = NOW(), observacoes_aprovacao = @obs
                                 WHERE id = @id AND status = 'pendente'";
                    
                    using (var cmd = new MySqlCommand(query, conn))
                    {
                        cmd.Parameters.AddWithValue("@id", estornoId);
                        cmd.Parameters.AddWithValue("@supervisor_id", supervisorId);
                        cmd.Parameters.AddWithValue("@obs", observacoes ?? "");
                        
                        var affected = cmd.ExecuteNonQuery();
                        
                        if (affected > 0)
                        {
                            AuditoriaService.Instance.Log(supervisorId, "aprovar_estorno", "estorno", estornoId,
                                $"Estorno aprovado - Obs: {observacoes}");
                            return (true, "Estorno aprovado com sucesso");
                        }
                        else
                        {
                            return (false, "Estorno não encontrado ou já processado");
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                return (false, $"Erro ao aprovar estorno: {ex.Message}");
            }
        }

        public (bool success, string message) RejeitarEstorno(long estornoId, int supervisorId, string observacoes)
        {
            try
            {
                if (string.IsNullOrWhiteSpace(observacoes))
                    return (false, "Informe o motivo da rejeição");

                using (var conn = DatabaseService.Instance.GetConnection())
                {
                    var query = @"UPDATE estornos 
                                 SET status = 'rejeitado', aprovado_por = @supervisor_id, 
                                     processado_em = NOW(), observacoes_aprovacao = @obs
                                 WHERE id = @id AND status = 'pendente'";
                    
                    using (var cmd = new MySqlCommand(query, conn))
                    {
                        cmd.Parameters.AddWithValue("@id", estornoId);
                        cmd.Parameters.AddWithValue("@supervisor_id", supervisorId);
                        cmd.Parameters.AddWithValue("@obs", observacoes);
                        
                        var affected = cmd.ExecuteNonQuery();
                        
                        if (affected > 0)
                        {
                            AuditoriaService.Instance.Log(supervisorId, "rejeitar_estorno", "estorno", estornoId,
                                $"Estorno rejeitado - Motivo: {observacoes}");
                            return (true, "Estorno rejeitado");
                        }
                        else
                        {
                            return (false, "Estorno não encontrado ou já processado");
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                return (false, $"Erro ao rejeitar estorno: {ex.Message}");
            }
        }

        private Estorno MapEstorno(MySqlDataReader reader)
        {
            return new Estorno
            {
                Id = reader.GetInt32("id"),
                PedidoId = reader.GetInt32("pedido_id"),
                ItemPedidoId = reader.IsDBNull(reader.GetOrdinal("item_pedido_id")) ? null : reader.GetInt32("item_pedido_id"),
                Tipo = reader.GetString("tipo"),
                Valor = reader.GetDecimal("valor"),
                Motivo = reader.GetString("motivo"),
                SolicitadoPor = reader.GetInt32("solicitado_por"),
                SolicitadoPorNome = reader.GetString("solicitado_por_nome"),
                AprovadoPor = reader.IsDBNull(reader.GetOrdinal("aprovado_por")) ? null : reader.GetInt32("aprovado_por"),
                Status = reader.GetString("status"),
                SolicitadoEm = reader.GetDateTime("solicitado_em"),
                ProcessadoEm = reader.IsDBNull(reader.GetOrdinal("processado_em")) ? null : reader.GetDateTime("processado_em"),
                ObservacoesAprovacao = reader.IsDBNull(reader.GetOrdinal("observacoes_aprovacao")) ? "" : reader.GetString("observacoes_aprovacao")
            };
        }
    }
}
