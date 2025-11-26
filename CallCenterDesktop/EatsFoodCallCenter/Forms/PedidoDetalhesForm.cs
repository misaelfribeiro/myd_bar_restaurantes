using System;
using System.Drawing;
using System.Windows.Forms;
using System.Linq;
using EatsFoodCallCenter.Services;
using EatsFoodCallCenter.Models;

namespace EatsFoodCallCenter.Forms
{
    public class PedidoDetalhesForm : Form
    {
        private long _pedidoId;
        private Pedido _pedido;
        private Label lblTitulo;
        private Panel panelInfo;
        private DataGridView dgvItens;
        private Button btnEstornoParcial;
        private Button btnEstornoTotal;
        private Button btnFechar;

        public PedidoDetalhesForm(long pedidoId)
        {
            _pedidoId = pedidoId;
            InitializeComponents();
            CarregarDados();
        }

        private void InitializeComponents()
        {
            this.Text = "Detalhes do Pedido";
            this.Size = new Size(900, 700);
            this.StartPosition = FormStartPosition.CenterScreen;
            this.BackColor = Color.FromArgb(240, 240, 240);

            // Título
            lblTitulo = new Label
            {
                Text = "📦 Detalhes do Pedido",
                Font = new Font("Segoe UI", 16, FontStyle.Bold),
                Location = new Point(20, 20),
                Size = new Size(800, 30),
                ForeColor = Color.FromArgb(52, 58, 64)
            };
            this.Controls.Add(lblTitulo);

            // Panel Info
            panelInfo = new Panel
            {
                Location = new Point(20, 60),
                Size = new Size(840, 150),
                BackColor = Color.White,
                BorderStyle = BorderStyle.FixedSingle
            };
            this.Controls.Add(panelInfo);

            // Itens
            var lblItens = new Label
            {
                Text = "Itens do Pedido:",
                Font = new Font("Segoe UI", 12, FontStyle.Bold),
                Location = new Point(20, 225),
                Size = new Size(200, 25)
            };
            this.Controls.Add(lblItens);

            dgvItens = new DataGridView
            {
                Location = new Point(20, 255),
                Size = new Size(840, 300),
                Font = new Font("Segoe UI", 10),
                AllowUserToAddRows = false,
                AllowUserToDeleteRows = false,
                ReadOnly = true,
                SelectionMode = DataGridViewSelectionMode.FullRowSelect,
                MultiSelect = false,
                AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.Fill,
                BackgroundColor = Color.White,
                RowHeadersVisible = false
            };

            dgvItens.Columns.Add(new DataGridViewTextBoxColumn { Name = "Id", HeaderText = "ID", Visible = false });
            dgvItens.Columns.Add(new DataGridViewTextBoxColumn { Name = "Nome", HeaderText = "Produto", Width = 300 });
            dgvItens.Columns.Add(new DataGridViewTextBoxColumn { Name = "Quantidade", HeaderText = "Qtd", Width = 80 });
            dgvItens.Columns.Add(new DataGridViewTextBoxColumn { Name = "PrecoUnitario", HeaderText = "Preço Unit.", Width = 100 });
            dgvItens.Columns.Add(new DataGridViewTextBoxColumn { Name = "Subtotal", HeaderText = "Subtotal", Width = 100 });
            dgvItens.Columns.Add(new DataGridViewTextBoxColumn { Name = "Estornado", HeaderText = "Status Estorno", Width = 150 });

            this.Controls.Add(dgvItens);

            // Botões
            btnEstornoParcial = new Button
            {
                Text = "Solicitar Estorno Parcial",
                Location = new Point(20, 570),
                Size = new Size(250, 40),
                Font = new Font("Segoe UI", 11, FontStyle.Bold),
                BackColor = Color.FromArgb(255, 193, 7),
                ForeColor = Color.Black,
                FlatStyle = FlatStyle.Flat,
                Cursor = Cursors.Hand
            };
            btnEstornoParcial.FlatAppearance.BorderSize = 0;
            btnEstornoParcial.Click += BtnEstornoParcial_Click;
            this.Controls.Add(btnEstornoParcial);

            btnEstornoTotal = new Button
            {
                Text = "Solicitar Estorno Total",
                Location = new Point(280, 570),
                Size = new Size(250, 40),
                Font = new Font("Segoe UI", 11, FontStyle.Bold),
                BackColor = Color.FromArgb(220, 53, 69),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Cursor = Cursors.Hand
            };
            btnEstornoTotal.FlatAppearance.BorderSize = 0;
            btnEstornoTotal.Click += BtnEstornoTotal_Click;
            this.Controls.Add(btnEstornoTotal);

            btnFechar = new Button
            {
                Text = "Fechar",
                Location = new Point(610, 570),
                Size = new Size(250, 40),
                Font = new Font("Segoe UI", 11),
                BackColor = Color.FromArgb(108, 117, 125),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Cursor = Cursors.Hand
            };
            btnFechar.FlatAppearance.BorderSize = 0;
            btnFechar.Click += (s, e) => this.Close();
            this.Controls.Add(btnFechar);
        }

        private void CarregarDados()
        {
            try
            {
                _pedido = PedidoService.Instance.BuscarPorId(_pedidoId);
                if (_pedido == null)
                {
                    MessageBox.Show("Pedido não encontrado", "Erro", MessageBoxButtons.OK, MessageBoxIcon.Error);
                    this.Close();
                    return;
                }

                lblTitulo.Text = $"📦 Pedido #{_pedido.NumeroPedido}";

                // Montar info
                var lbls = new[]
                {
                    new { Text = $"Cliente: {_pedido.ClienteNome}", Top = 15 },
                    new { Text = $"Telefone: {_pedido.ClienteTelefone}", Top = 40 },
                    new { Text = $"Status: {_pedido.StatusFormatado}", Top = 65 },
                    new { Text = $"Total: R$ {_pedido.Total:N2}", Top = 90 },
                    new { Text = $"Taxa Entrega: R$ {_pedido.TaxaEntrega:N2}", Top = 90 },
                    new { Text = $"Forma Pagamento: {_pedido.FormaPagamento}", Top = 15 },
                    new { Text = $"Data: {_pedido.CreatedAt:dd/MM/yyyy HH:mm}", Top = 40 },
                    new { Text = $"Entregador: {_pedido.EntregadorNome ?? "Não atribuído"}", Top = 65 }
                };

                int leftPosition = 15;
                for (int i = 0; i < lbls.Length; i++)
                {
                    if (i == 4 || i == 5) leftPosition = 430; // Segunda coluna
                    if (i == 5) { } // Manter leftPosition

                    var lbl = new Label
                    {
                        Text = lbls[i].Text,
                        Font = new Font("Segoe UI", 10),
                        Location = new Point(leftPosition, lbls[i].Top),
                        Size = new Size(400, 20)
                    };
                    panelInfo.Controls.Add(lbl);
                }

                // Carregar itens
                var itens = PedidoService.Instance.BuscarItensPedido(_pedidoId);
                foreach (var item in itens)
                {
                    dgvItens.Rows.Add(
                        item.Id,
                        item.Nome,
                        item.Quantidade,
                        $"R$ {item.PrecoUnitario:N2}",
                        $"R$ {item.Subtotal:N2}",
                        item.StatusEstornoFormatado
                    );
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Erro ao carregar dados:\n{ex.Message}", "Erro", MessageBoxButtons.OK, MessageBoxIcon.Error);
                this.Close();
            }
        }

        private void BtnEstornoParcial_Click(object sender, EventArgs e)
        {
            if (dgvItens.SelectedRows.Count == 0)
            {
                MessageBox.Show("Selecione um item para estornar", "Atenção", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            long itemId = Convert.ToInt64(dgvItens.SelectedRows[0].Cells["Id"].Value);
            var item = PedidoService.Instance.BuscarItensPedido(_pedidoId).FirstOrDefault(i => i.Id == itemId);

            if (item == null) return;

            if (item.StatusEstorno == "aprovado")
            {
                MessageBox.Show("Este item já foi estornado", "Atenção", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            if (item.StatusEstorno == "pendente")
            {
                MessageBox.Show("Já existe uma solicitação de estorno pendente para este item", "Atenção", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            var formEstorno = new EstornoForm(_pedidoId, itemId, item.Nome, item.Subtotal, false);
            if (formEstorno.ShowDialog() == DialogResult.OK)
            {
                MessageBox.Show("Estorno parcial solicitado com sucesso!\nAguardando aprovação do supervisor.",
                    "Sucesso", MessageBoxButtons.OK, MessageBoxIcon.Information);
                CarregarDados();
            }
        }

        private void BtnEstornoTotal_Click(object sender, EventArgs e)
        {
            var formEstorno = new EstornoForm(_pedidoId, null, "", _pedido.Total, true);
            if (formEstorno.ShowDialog() == DialogResult.OK)
            {
                MessageBox.Show("Estorno total solicitado com sucesso!\nAguardando aprovação do supervisor.",
                    "Sucesso", MessageBoxButtons.OK, MessageBoxIcon.Information);
                CarregarDados();
            }
        }
    }
}
