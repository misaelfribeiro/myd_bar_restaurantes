using System;
using System.Drawing;
using System.Windows.Forms;
using EatsFoodCallCenter.Services;

namespace EatsFoodCallCenter.Forms
{
    public class LoginForm : Form
    {
        private TextBox txtEmail;
        private TextBox txtSenha;
        private Button btnLogin;
        private Label lblEmail;
        private Label lblSenha;
        private Label lblTitulo;
        private Label lblRodape;
        private Panel panelHeader;
        private Panel panelFooter;
        private Panel panelLogin;
        private CheckBox chkLembrar;
        private Label lblStatus;

        public LoginForm()
        {
            InitializeComponents();
            ConfigurarEstilos();
        }

        private void InitializeComponents()
        {
            // Configurações da janela
            this.Text = "EATSFOOD - Login Call Center";
            this.Size = new Size(450, 550);
            this.StartPosition = FormStartPosition.CenterScreen;
            this.FormBorderStyle = FormBorderStyle.FixedDialog;
            this.MaximizeBox = false;
            this.MinimizeBox = false;
            this.BackColor = Color.FromArgb(240, 240, 240);

            // Header
            panelHeader = new Panel
            {
                Dock = DockStyle.Top,
                Height = 80,
                BackColor = Color.FromArgb(220, 53, 69)
            };

            lblTitulo = new Label
            {
                Text = "EATSFOOD CALL CENTER",
                Font = new Font("Segoe UI", 18, FontStyle.Bold),
                ForeColor = Color.White,
                TextAlign = ContentAlignment.MiddleCenter,
                Dock = DockStyle.Fill
            };

            panelHeader.Controls.Add(lblTitulo);
            this.Controls.Add(panelHeader);

            // Panel de Login
            panelLogin = new Panel
            {
                Size = new Size(350, 280),
                Location = new Point(50, 110),
                BackColor = Color.White,
                BorderStyle = BorderStyle.FixedSingle
            };

            // Email
            lblEmail = new Label
            {
                Text = "Email:",
                Location = new Point(20, 30),
                Size = new Size(300, 20),
                Font = new Font("Segoe UI", 10, FontStyle.Bold)
            };

            txtEmail = new TextBox
            {
                Location = new Point(20, 55),
                Size = new Size(310, 30),
                Font = new Font("Segoe UI", 11)
            };

            // Senha
            lblSenha = new Label
            {
                Text = "Senha:",
                Location = new Point(20, 100),
                Size = new Size(300, 20),
                Font = new Font("Segoe UI", 10, FontStyle.Bold)
            };

            txtSenha = new TextBox
            {
                Location = new Point(20, 125),
                Size = new Size(310, 30),
                Font = new Font("Segoe UI", 11),
                PasswordChar = '●',
                UseSystemPasswordChar = true
            };

            // Lembrar
            chkLembrar = new CheckBox
            {
                Text = "Lembrar meu email",
                Location = new Point(20, 165),
                Size = new Size(200, 25),
                Font = new Font("Segoe UI", 9)
            };

            // Botão Login
            btnLogin = new Button
            {
                Text = "ENTRAR",
                Location = new Point(20, 205),
                Size = new Size(310, 45),
                Font = new Font("Segoe UI", 12, FontStyle.Bold),
                BackColor = Color.FromArgb(40, 167, 69),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Cursor = Cursors.Hand
            };
            btnLogin.FlatAppearance.BorderSize = 0;
            btnLogin.Click += BtnLogin_Click;

            // Status
            lblStatus = new Label
            {
                Text = "",
                Location = new Point(20, 260),
                Size = new Size(310, 15),
                Font = new Font("Segoe UI", 8),
                ForeColor = Color.Red,
                TextAlign = ContentAlignment.MiddleCenter
            };

            panelLogin.Controls.AddRange(new Control[] {
                lblEmail, txtEmail, lblSenha, txtSenha, chkLembrar, btnLogin, lblStatus
            });
            this.Controls.Add(panelLogin);

            // Footer
            panelFooter = new Panel
            {
                Dock = DockStyle.Bottom,
                Height = 50,
                BackColor = Color.FromArgb(52, 58, 64)
            };

            lblRodape = new Label
            {
                Text = "© 2025 EATSFOOD - Sistema de Atendimento ao Cliente\nTodos os direitos reservados",
                Font = new Font("Segoe UI", 8),
                ForeColor = Color.White,
                TextAlign = ContentAlignment.MiddleCenter,
                Dock = DockStyle.Fill
            };

            panelFooter.Controls.Add(lblRodape);
            this.Controls.Add(panelFooter);

            // Enter key para login
            txtSenha.KeyPress += (s, e) =>
            {
                if (e.KeyChar == (char)Keys.Enter)
                {
                    e.Handled = true;
                    BtnLogin_Click(btnLogin, EventArgs.Empty);
                }
            };

            // Carregar email salvo
            CarregarEmailSalvo();
        }

        private void ConfigurarEstilos()
        {
            // Efeitos hover no botão
            btnLogin.MouseEnter += (s, e) => btnLogin.BackColor = Color.FromArgb(33, 136, 56);
            btnLogin.MouseLeave += (s, e) => btnLogin.BackColor = Color.FromArgb(40, 167, 69);
        }

        private void BtnLogin_Click(object sender, EventArgs e)
        {
            lblStatus.Text = "";
            lblStatus.ForeColor = Color.Red;

            // Validação básica
            if (string.IsNullOrWhiteSpace(txtEmail.Text))
            {
                lblStatus.Text = "Por favor, digite seu email";
                txtEmail.Focus();
                return;
            }

            if (string.IsNullOrWhiteSpace(txtSenha.Text))
            {
                lblStatus.Text = "Por favor, digite sua senha";
                txtSenha.Focus();
                return;
            }

            // Desabilitar botão durante login
            btnLogin.Enabled = false;
            btnLogin.Text = "ENTRANDO...";
            lblStatus.Text = "Autenticando...";
            lblStatus.ForeColor = Color.Blue;
            this.Cursor = Cursors.WaitCursor;

            try
            {
                var (success, message, user) = AuthService.Instance.Login(txtEmail.Text.Trim(), txtSenha.Text);

                if (success)
                {
                    // Salvar email se marcou lembrar
                    if (chkLembrar.Checked)
                    {
                        Properties.Settings.Default.SavedEmail = txtEmail.Text.Trim();
                        Properties.Settings.Default.Save();
                    }
                    else
                    {
                        Properties.Settings.Default.SavedEmail = "";
                        Properties.Settings.Default.Save();
                    }

                    lblStatus.Text = "Login realizado com sucesso!";
                    lblStatus.ForeColor = Color.Green;

                    // Abrir MainForm
                    this.Hide();
                    var mainForm = new MainForm();
                    mainForm.FormClosed += (s, args) => this.Close();
                    mainForm.Show();
                }
                else
                {
                    lblStatus.Text = message;
                    lblStatus.ForeColor = Color.Red;
                    txtSenha.Clear();
                    txtSenha.Focus();
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show(
                    $"Erro ao conectar ao banco de dados:\n\n{ex.Message}",
                    "Erro de Conexão",
                    MessageBoxButtons.OK,
                    MessageBoxIcon.Error
                );
                lblStatus.Text = "Erro de conexão";
            }
            finally
            {
                btnLogin.Enabled = true;
                btnLogin.Text = "ENTRAR";
                this.Cursor = Cursors.Default;
            }
        }

        private void CarregarEmailSalvo()
        {
            string savedEmail = Properties.Settings.Default.SavedEmail;
            if (!string.IsNullOrEmpty(savedEmail))
            {
                txtEmail.Text = savedEmail;
                chkLembrar.Checked = true;
                txtSenha.Focus();
            }
            else
            {
                txtEmail.Focus();
            }
        }
    }
}
