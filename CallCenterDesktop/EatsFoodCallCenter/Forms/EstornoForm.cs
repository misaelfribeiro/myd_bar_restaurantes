using System;
using System.Drawing;
using System.Windows.Forms;
using EatsFoodCallCenter.Services;

namespace EatsFoodCallCenter.Forms
{
    public class EstornoForm : Form
    {
        private long _pedidoId;
        private long? _itemPedidoId;
        private string _nomeProduto;
        private decimal _valor;
        private bool _isTotal;

        private Label lblTitulo;
        private Label lblInfo;
        private Label lblValor;
        private Label lblMotivo;
        private TextBox txtMotivo;
        private Button btnConfirmar;
        private Button btnCancelar;

        public EstornoForm(long pedidoId, long? itemPedidoId, string nomeProduto, decimal valor, bool isTotal)
        {
            _pedidoId = pedidoId;
            _itemPedidoId = itemPedidoId;
            _nomeProduto = nomeProduto;
            _valor = valor;
            _isTotal = isTotal;

            InitializeComponents();
        }

        private void InitializeComponents()
        {
            this.Text = "Solicitar Estorno";
            this.Size = new Size(600, 450);
            this.StartPosition = FormStartPosition.CenterParent;
            this.FormBorderStyle = FormBorderStyle.FixedDialog;
            this.MaximizeBox = false;
            this.MinimizeBox = false;
            this.BackColor = Color.FromArgb(240, 240, 240);

            // Título
            lblTitulo = new Label
            {
                Text = _isTotal ? "💰 Estorno Total do Pedido" : "💰 Estorno Parcial (Item)",
                Font = new Font("Segoe UI", 16, FontStyle.Bold),
                Location = new Point(20, 20),
                Size = new Size(540, 30),
                ForeColor = Color.FromArgb(220, 53, 69)
            };
            this.Controls.Add(lblTitulo);

            // Info
            lblInfo = new Label
            {
                Text = _isTotal ? "Estorno de todo o pedido" : $"Item: {_nomeProduto}",
                Font = new Font("Segoe UI", 11),
                Location = new Point(20, 60),
                Size = new Size(540, 25)
            };
            this.Controls.Add(lblInfo);

            // Valor
            lblValor = new Label
            {
                Text = $"Valor a estornar: R$ {_valor:N2}",
                Font = new Font("Segoe UI", 12, FontStyle.Bold),
                Location = new Point(20, 90),
                Size = new Size(540, 25),
                ForeColor = Color.FromArgb(220, 53, 69)
            };
            this.Controls.Add(lblValor);

            // Motivo
            lblMotivo = new Label
            {
                Text = "Motivo do estorno (mínimo 10 caracteres):",
                Font = new Font("Segoe UI", 10, FontStyle.Bold),
                Location = new Point(20, 130),
                Size = new Size(540, 20)
            };
            this.Controls.Add(lblMotivo);

            txtMotivo = new TextBox
            {
                Location = new Point(20, 155),
                Size = new Size(540, 120),
                Font = new Font("Segoe UI", 11),
                Multiline = true,
                ScrollBars = ScrollBars.Vertical
            };
            this.Controls.Add(txtMotivo);

            // Aviso
            var lblAviso = new Label
            {
                Text = "⚠️ Este estorno requer aprovação de um supervisor",
                Font = new Font("Segoe UI", 9),
                Location = new Point(20, 285),
                Size = new Size(540, 20),
                ForeColor = Color.Orange
            };
            this.Controls.Add(lblAviso);

            // Botões
            btnConfirmar = new Button
            {
                Text = "CONFIRMAR SOLICITAÇÃO",
                Location = new Point(20, 320),
                Size = new Size(260, 45),
                Font = new Font("Segoe UI", 11, FontStyle.Bold),
                BackColor = Color.FromArgb(40, 167, 69),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Cursor = Cursors.Hand
            };
            btnConfirmar.FlatAppearance.BorderSize = 0;
            btnConfirmar.Click += BtnConfirmar_Click;
            this.Controls.Add(btnConfirmar);

            btnCancelar = new Button
            {
                Text = "CANCELAR",
                Location = new Point(300, 320),
                Size = new Size(260, 45),
                Font = new Font("Segoe UI", 11),
                BackColor = Color.FromArgb(108, 117, 125),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Cursor = Cursors.Hand
            };
            btnCancelar.FlatAppearance.BorderSize = 0;
            btnCancelar.Click += (s, e) => this.DialogResult = DialogResult.Cancel;
            this.Controls.Add(btnCancelar);

            txtMotivo.Focus();
        }

        private void BtnConfirmar_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrWhiteSpace(txtMotivo.Text))
            {
                MessageBox.Show("Por favor, informe o motivo do estorno", "Atenção", 
                    MessageBoxButtons.OK, MessageBoxIcon.Warning);
                txtMotivo.Focus();
                return;
            }

            if (txtMotivo.Text.Trim().Length < 10)
            {
                MessageBox.Show("O motivo deve ter no mínimo 10 caracteres", "Atenção",
                    MessageBoxButtons.OK, MessageBoxIcon.Warning);
                txtMotivo.Focus();
                return;
            }

            var confirmResult = MessageBox.Show(
                $"Confirma a solicitação de estorno no valor de R$ {_valor:N2}?\n\nMotivo:\n{txtMotivo.Text}",
                "Confirmar Estorno",
                MessageBoxButtons.YesNo,
                MessageBoxIcon.Question
            );

            if (confirmResult != DialogResult.Yes)
                return;

            btnConfirmar.Enabled = false;
            btnConfirmar.Text = "PROCESSANDO...";
            this.Cursor = Cursors.WaitCursor;

            try
            {
                var usuarioId = AuthService.Instance.CurrentUser.Id;
                bool success;
                string message;

                if (_isTotal)
                {
                    (success, message) = EstornoService.Instance.SolicitarEstornoTotal(
                        _pedidoId,
                        txtMotivo.Text.Trim(),
                        usuarioId
                    );
                }
                else
                {
                    (success, message) = EstornoService.Instance.SolicitarEstornoParcial(
                        _pedidoId,
                        _itemPedidoId.Value,
                        txtMotivo.Text.Trim(),
                        usuarioId
                    );
                }

                if (success)
                {
                    this.DialogResult = DialogResult.OK;
                    this.Close();
                }
                else
                {
                    MessageBox.Show(message, "Erro", MessageBoxButtons.OK, MessageBoxIcon.Error);
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Erro ao solicitar estorno:\n{ex.Message}", "Erro",
                    MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
            finally
            {
                btnConfirmar.Enabled = true;
                btnConfirmar.Text = "CONFIRMAR SOLICITAÇÃO";
                this.Cursor = Cursors.Default;
            }
        }
    }
}
