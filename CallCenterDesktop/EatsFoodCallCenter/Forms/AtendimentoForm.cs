using System;
using System.Drawing;
using System.Windows.Forms;
using System.Linq;
using EatsFoodCallCenter.Services;
using EatsFoodCallCenter.Models;

namespace EatsFoodCallCenter.Forms
{
    public class AtendimentoForm : UserControl
    {
        private TextBox txtBusca;
        private Button btnBuscar;
        private DataGridView dgvPedidos;
        private Label lblTitulo;
        private Label lblResultados;

        public AtendimentoForm()
        {
            InitializeComponents();
        }

        private void InitializeComponents()
        {
            this.BackColor = Color.White;
            this.Dock = DockStyle.Fill;

            // Título
            lblTitulo = new Label
            {
                Text = "🔍 Buscar Pedidos de Cliente",
                Font = new Font("Segoe UI", 18, FontStyle.Bold),
                Location = new Point(20, 20),
                Size = new Size(600, 35),
                ForeColor = Color.FromArgb(52, 58, 64)
            };
            this.Controls.Add(lblTitulo);

            // Busca
            var lblBusca = new Label
            {
                Text = "Digite telefone, CPF, nome do cliente ou número do pedido:",
                Location = new Point(20, 70),
                Size = new Size(450, 20),
                Font = new Font("Segoe UI", 10)
            };
            this.Controls.Add(lblBusca);

            txtBusca = new TextBox
            {
                Location = new Point(20, 95),
                Size = new Size(400, 30),
                Font = new Font("Segoe UI", 12)
            };
            txtBusca.KeyPress += (s, e) =>
            {
                if (e.KeyChar == (char)Keys.Enter)
                {
                    e.Handled = true;
                    BtnBuscar_Click(btnBuscar, EventArgs.Empty);
                }
            };
            this.Controls.Add(txtBusca);

            btnBuscar = new Button
            {
                Text = "BUSCAR",
                Location = new Point(430, 95),
                Size = new Size(150, 30),
                Font = new Font("Segoe UI", 11, FontStyle.Bold),
                BackColor = Color.FromArgb(0, 123, 255),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Cursor = Cursors.Hand
            };
            btnBuscar.FlatAppearance.BorderSize = 0;
            btnBuscar.Click += BtnBuscar_Click;
            this.Controls.Add(btnBuscar);

            // Resultados
            lblResultados = new Label
            {
                Text = "Digite algo para buscar pedidos...",
                Location = new Point(20, 140),
                Size = new Size(800, 20),
                Font = new Font("Segoe UI", 10),
                ForeColor = Color.Gray
            };
            this.Controls.Add(lblResultados);

            // DataGridView
            dgvPedidos = new DataGridView
            {
                Location = new Point(20, 170),
                Size = new Size(1100, 450),
                Font = new Font("Segoe UI", 10),
                AllowUserToAddRows = false,
                AllowUserToDeleteRows = false,
                ReadOnly = true,
                SelectionMode = DataGridViewSelectionMode.FullRowSelect,
                MultiSelect = false,
                AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.Fill,
                BackgroundColor = Color.White,
                BorderStyle = BorderStyle.Fixed3D,
                RowHeadersVisible = false
            };

            dgvPedidos.Columns.Add(new DataGridViewTextBoxColumn { Name = "Id", HeaderText = "ID", Width = 60, Visible = false });
            dgvPedidos.Columns.Add(new DataGridViewTextBoxColumn { Name = "NumeroPedido", HeaderText = "Nº Pedido", Width = 100 });
            dgvPedidos.Columns.Add(new DataGridViewTextBoxColumn { Name = "ClienteNome", HeaderText = "Cliente", Width = 200 });
            dgvPedidos.Columns.Add(new DataGridViewTextBoxColumn { Name = "ClienteTelefone", HeaderText = "Telefone", Width = 130 });
            dgvPedidos.Columns.Add(new DataGridViewTextBoxColumn { Name = "Status", HeaderText = "Status", Width = 130 });
            dgvPedidos.Columns.Add(new DataGridViewTextBoxColumn { Name = "Total", HeaderText = "Total", Width = 100 });
            dgvPedidos.Columns.Add(new DataGridViewTextBoxColumn { Name = "CreatedAt", HeaderText = "Data", Width = 150 });

            dgvPedidos.CellDoubleClick += DgvPedidos_CellDoubleClick;
            this.Controls.Add(dgvPedidos);

            // Ajustar tamanho ao resize
            this.Resize += (s, e) =>
            {
                if (this.Width > 0 && this.Height > 0)
                {
                    dgvPedidos.Width = this.Width - 40;
                    dgvPedidos.Height = this.Height - 190;
                }
            };
        }

        private async void BtnBuscar_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrWhiteSpace(txtBusca.Text))
            {
                MessageBox.Show("Digite algo para buscar", "Atenção", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                txtBusca.Focus();
                return;
            }

            btnBuscar.Enabled = false;
            btnBuscar.Text = "BUSCANDO...";
            dgvPedidos.Rows.Clear();
            lblResultados.Text = "Buscando...";
            this.Cursor = Cursors.WaitCursor;

            try
            {
                var pedidos = await System.Threading.Tasks.Task.Run(() => 
                    PedidoService.Instance.BuscarPorCliente(txtBusca.Text.Trim())
                );

                if (pedidos.Count == 0)
                {
                    lblResultados.Text = "Nenhum pedido encontrado";
                    lblResultados.ForeColor = Color.Orange;
                }
                else
                {
                    lblResultados.Text = $"{pedidos.Count} pedido(s) encontrado(s)";
                    lblResultados.ForeColor = Color.Green;

                    foreach (var pedido in pedidos)
                    {
                        dgvPedidos.Rows.Add(
                            pedido.Id,
                            pedido.NumeroPedido,
                            pedido.ClienteNome,
                            pedido.ClienteTelefone,
                            pedido.StatusFormatado,
                            $"R$ {pedido.Total:N2}",
                            pedido.CreatedAt.ToString("dd/MM/yyyy HH:mm")
                        );
                    }
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Erro ao buscar pedidos:\n{ex.Message}", "Erro", MessageBoxButtons.OK, MessageBoxIcon.Error);
                lblResultados.Text = "Erro na busca";
                lblResultados.ForeColor = Color.Red;
            }
            finally
            {
                btnBuscar.Enabled = true;
                btnBuscar.Text = "BUSCAR";
                this.Cursor = Cursors.Default;
            }
        }

        private void DgvPedidos_CellDoubleClick(object sender, DataGridViewCellEventArgs e)
        {
            if (e.RowIndex >= 0)
            {
                long pedidoId = Convert.ToInt64(dgvPedidos.Rows[e.RowIndex].Cells["Id"].Value);
                
                var formDetalhes = new PedidoDetalhesForm(pedidoId);
                formDetalhes.ShowDialog();
                
                // Atualizar busca após fechar detalhes
                if (!string.IsNullOrWhiteSpace(txtBusca.Text))
                {
                    BtnBuscar_Click(btnBuscar, EventArgs.Empty);
                }
            }
        }
    }
}
