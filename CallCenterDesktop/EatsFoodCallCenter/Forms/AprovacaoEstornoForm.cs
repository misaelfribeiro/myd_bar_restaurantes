using System;
using System.Drawing;
using System.Windows.Forms;
using System.Linq;
using EatsFoodCallCenter.Services;
using EatsFoodCallCenter.Models;

namespace EatsFoodCallCenter.Forms
{
    public class AprovacaoEstornoForm : UserControl
    {
        private Label lblTitulo;
        private DataGridView dgvEstornos;
        private Button btnAprovar;
        private Button btnRejeitar;
        private Button btnAtualizar;
        private Label lblTotal;
        private System.Windows.Forms.Timer timerAuto;

        public AprovacaoEstornoForm()
        {
            InitializeComponents();
            CarregarEstornos();
            
            // Auto-refresh a cada 30 segundos
            timerAuto = new System.Windows.Forms.Timer { Interval = 30000 };
            timerAuto.Tick += (s, e) => CarregarEstornos();
            timerAuto.Start();
        }

        private void InitializeComponents()
        {
            this.BackColor = Color.White;
            this.Dock = DockStyle.Fill;

            // Título
            lblTitulo = new Label
            {
                Text = "✅ Aprovação de Estornos",
                Font = new Font("Segoe UI", 18, FontStyle.Bold),
                Location = new Point(20, 20),
                Size = new Size(600, 35),
                ForeColor = Color.FromArgb(52, 58, 64)
            };
            this.Controls.Add(lblTitulo);

            // Total
            lblTotal = new Label
            {
                Text = "Carregando...",
                Font = new Font("Segoe UI", 11),
                Location = new Point(20, 60),
                Size = new Size(800, 25),
                ForeColor = Color.FromArgb(220, 53, 69)
            };
            this.Controls.Add(lblTotal);

            // DataGridView
            dgvEstornos = new DataGridView
            {
                Location = new Point(20, 100),
                Size = new Size(1100, 420),
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

            dgvEstornos.Columns.Add(new DataGridViewTextBoxColumn { Name = "Id", HeaderText = "ID", Width = 60, Visible = false });
            dgvEstornos.Columns.Add(new DataGridViewTextBoxColumn { Name = "PedidoId", HeaderText = "Pedido #", Width = 90 });
            dgvEstornos.Columns.Add(new DataGridViewTextBoxColumn { Name = "Tipo", HeaderText = "Tipo", Width = 90 });
            dgvEstornos.Columns.Add(new DataGridViewTextBoxColumn { Name = "Valor", HeaderText = "Valor", Width = 100 });
            dgvEstornos.Columns.Add(new DataGridViewTextBoxColumn { Name = "Motivo", HeaderText = "Motivo", Width = 300 });
            dgvEstornos.Columns.Add(new DataGridViewTextBoxColumn { Name = "Solicitante", HeaderText = "Solicitante", Width = 150 });
            dgvEstornos.Columns.Add(new DataGridViewTextBoxColumn { Name = "Data", HeaderText = "Data", Width = 130 });

            this.Controls.Add(dgvEstornos);

            // Botões
            btnAprovar = new Button
            {
                Text = "✅ APROVAR",
                Location = new Point(20, 540),
                Size = new Size(200, 45),
                Font = new Font("Segoe UI", 12, FontStyle.Bold),
                BackColor = Color.FromArgb(40, 167, 69),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Cursor = Cursors.Hand,
                Enabled = false
            };
            btnAprovar.FlatAppearance.BorderSize = 0;
            btnAprovar.Click += BtnAprovar_Click;
            this.Controls.Add(btnAprovar);

            btnRejeitar = new Button
            {
                Text = "❌ REJEITAR",
                Location = new Point(240, 540),
                Size = new Size(200, 45),
                Font = new Font("Segoe UI", 12, FontStyle.Bold),
                BackColor = Color.FromArgb(220, 53, 69),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Cursor = Cursors.Hand,
                Enabled = false
            };
            btnRejeitar.FlatAppearance.BorderSize = 0;
            btnRejeitar.Click += BtnRejeitar_Click;
            this.Controls.Add(btnRejeitar);

            btnAtualizar = new Button
            {
                Text = "🔄 ATUALIZAR",
                Location = new Point(460, 540),
                Size = new Size(200, 45),
                Font = new Font("Segoe UI", 12, FontStyle.Bold),
                BackColor = Color.FromArgb(0, 123, 255),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Cursor = Cursors.Hand
            };
            btnAtualizar.FlatAppearance.BorderSize = 0;
            btnAtualizar.Click += (s, e) => CarregarEstornos();
            this.Controls.Add(btnAtualizar);

            // Seleção
            dgvEstornos.SelectionChanged += (s, e) =>
            {
                bool hasSelection = dgvEstornos.SelectedRows.Count > 0;
                btnAprovar.Enabled = hasSelection;
                btnRejeitar.Enabled = hasSelection;
            };

            // Ajustar tamanho ao resize
            this.Resize += (s, e) =>
            {
                if (this.Width > 0 && this.Height > 0)
                {
                    dgvEstornos.Width = this.Width - 40;
                    dgvEstornos.Height = this.Height - 220;
                    btnAprovar.Top = dgvEstornos.Bottom + 20;
                    btnRejeitar.Top = dgvEstornos.Bottom + 20;
                    btnAtualizar.Top = dgvEstornos.Bottom + 20;
                }
            };
        }

        private void CarregarEstornos()
        {
            try
            {
                var estornos = EstornoService.Instance.ListarPendentes();
                
                dgvEstornos.Rows.Clear();
                decimal totalValor = 0;

                foreach (var estorno in estornos)
                {
                    dgvEstornos.Rows.Add(
                        estorno.Id,
                        estorno.PedidoId,
                        estorno.IsParcial ? "PARCIAL" : "TOTAL",
                        $"R$ {estorno.Valor:N2}",
                        estorno.Motivo.Length > 50 ? estorno.Motivo.Substring(0, 47) + "..." : estorno.Motivo,
                        estorno.SolicitadoPorNome,
                        estorno.SolicitadoEm.ToString("dd/MM/yyyy HH:mm")
                    );
                    totalValor += estorno.Valor;
                }

                lblTotal.Text = $"{estornos.Count} estorno(s) pendente(s) | Valor total: R$ {totalValor:N2}";
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Erro ao carregar estornos:\n{ex.Message}", "Erro",
                    MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }

        private void BtnAprovar_Click(object sender, EventArgs e)
        {
            if (dgvEstornos.SelectedRows.Count == 0) return;

            long estornoId = Convert.ToInt64(dgvEstornos.SelectedRows[0].Cells["Id"].Value);
            string tipo = dgvEstornos.SelectedRows[0].Cells["Tipo"].Value.ToString();
            string valor = dgvEstornos.SelectedRows[0].Cells["Valor"].Value.ToString();
            string motivo = dgvEstornos.SelectedRows[0].Cells["Motivo"].Value.ToString();

            var confirmResult = MessageBox.Show(
                $"Confirma a APROVAÇÃO deste estorno?\n\nTipo: {tipo}\nValor: {valor}\nMotivo: {motivo}",
                "Confirmar Aprovação",
                MessageBoxButtons.YesNo,
                MessageBoxIcon.Question
            );

            if (confirmResult != DialogResult.Yes) return;

            // Pedir observações opcionais
            string observacoes = Microsoft.VisualBasic.Interaction.InputBox(
                "Observações da aprovação (opcional):",
                "Observações",
                "",
                -1, -1
            );

            try
            {
                var supervisorId = AuthService.Instance.CurrentUser.Id;
                var (success, message) = EstornoService.Instance.AprovarEstorno(
                    estornoId,
                    supervisorId,
                    string.IsNullOrWhiteSpace(observacoes) ? "Aprovado" : observacoes
                );

                if (success)
                {
                    MessageBox.Show("Estorno aprovado com sucesso!", "Sucesso",
                        MessageBoxButtons.OK, MessageBoxIcon.Information);
                    CarregarEstornos();
                }
                else
                {
                    MessageBox.Show(message, "Erro", MessageBoxButtons.OK, MessageBoxIcon.Error);
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Erro ao aprovar estorno:\n{ex.Message}", "Erro",
                    MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }

        private void BtnRejeitar_Click(object sender, EventArgs e)
        {
            if (dgvEstornos.SelectedRows.Count == 0) return;

            long estornoId = Convert.ToInt64(dgvEstornos.SelectedRows[0].Cells["Id"].Value);
            string tipo = dgvEstornos.SelectedRows[0].Cells["Tipo"].Value.ToString();
            string valor = dgvEstornos.SelectedRows[0].Cells["Valor"].Value.ToString();

            // Pedir motivo da rejeição (obrigatório)
            string motivo = Microsoft.VisualBasic.Interaction.InputBox(
                "Motivo da REJEIÇÃO (obrigatório):",
                "Rejeitar Estorno",
                "",
                -1, -1
            );

            if (string.IsNullOrWhiteSpace(motivo))
            {
                MessageBox.Show("É necessário informar o motivo da rejeição", "Atenção",
                    MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            var confirmResult = MessageBox.Show(
                $"Confirma a REJEIÇÃO deste estorno?\n\nTipo: {tipo}\nValor: {valor}\n\nMotivo da rejeição:\n{motivo}",
                "Confirmar Rejeição",
                MessageBoxButtons.YesNo,
                MessageBoxIcon.Warning
            );

            if (confirmResult != DialogResult.Yes) return;

            try
            {
                var supervisorId = AuthService.Instance.CurrentUser.Id;
                var (success, message) = EstornoService.Instance.RejeitarEstorno(
                    estornoId,
                    supervisorId,
                    motivo
                );

                if (success)
                {
                    MessageBox.Show("Estorno rejeitado", "Sucesso",
                        MessageBoxButtons.OK, MessageBoxIcon.Information);
                    CarregarEstornos();
                }
                else
                {
                    MessageBox.Show(message, "Erro", MessageBoxButtons.OK, MessageBoxIcon.Error);
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Erro ao rejeitar estorno:\n{ex.Message}", "Erro",
                    MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }

        protected override void Dispose(bool disposing)
        {
            if (disposing)
            {
                timerAuto?.Stop();
                timerAuto?.Dispose();
            }
            base.Dispose(disposing);
        }
    }
}
