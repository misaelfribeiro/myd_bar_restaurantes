using System;
using System.Drawing;
using System.Windows.Forms;
using EatsFoodCallCenter.Services;
using EatsFoodCallCenter.Models;

namespace EatsFoodCallCenter.Forms
{
    public class MainForm : Form
    {
        private MenuStrip menuStrip;
        private StatusStrip statusStrip;
        private ToolStripStatusLabel lblUsuario;
        private ToolStripStatusLabel lblHora;
        private Panel panelConteudo;
        private System.Windows.Forms.Timer timerHora;
        private Label lblBemVindo;

        public MainForm()
        {
            InitializeComponents();
            ConfigurarEstilos();
            AtualizarInformacoes();
        }

        private void InitializeComponents()
        {
            // Configurações da janela
            this.Text = "EATSFOOD - Sistema Call Center";
            this.Size = new Size(1200, 700);
            this.StartPosition = FormStartPosition.CenterScreen;
            this.WindowState = FormWindowState.Maximized;
            this.BackColor = Color.FromArgb(240, 240, 240);

            // MenuStrip
            menuStrip = new MenuStrip
            {
                BackColor = Color.FromArgb(220, 53, 69),
                Font = new Font("Segoe UI", 11, FontStyle.Bold),
                Height = 40
            };

            // Menu Atendimento
            var menuAtendimento = new ToolStripMenuItem("  📞 Atendimento  ");
            menuAtendimento.ForeColor = Color.White;
            menuAtendimento.Click += MenuAtendimento_Click;

            // Menu Estornos (apenas supervisor e admin)
            var menuEstornos = new ToolStripMenuItem("  💰 Estornos  ");
            menuEstornos.ForeColor = Color.White;
            menuEstornos.Click += MenuEstornos_Click;

            // Menu Relatórios (apenas admin)
            var menuRelatorios = new ToolStripMenuItem("  📊 Relatórios  ");
            menuRelatorios.ForeColor = Color.White;
            menuRelatorios.Click += MenuRelatorios_Click;

            // Menu Usuários (apenas admin)
            var menuUsuarios = new ToolStripMenuItem("  👥 Usuários  ");
            menuUsuarios.ForeColor = Color.White;
            menuUsuarios.Click += MenuUsuarios_Click;

            // Menu Sair
            var menuSair = new ToolStripMenuItem("  🚪 Sair  ");
            menuSair.ForeColor = Color.White;
            menuSair.Click += MenuSair_Click;

            menuStrip.Items.Add(menuAtendimento);
            
            var user = AuthService.Instance.CurrentUser;
            if (user.IsSupervisor || user.IsAdmin)
            {
                menuStrip.Items.Add(menuEstornos);
            }
            
            if (user.IsAdmin)
            {
                menuStrip.Items.Add(menuRelatorios);
                menuStrip.Items.Add(menuUsuarios);
            }
            
            menuStrip.Items.Add(menuSair);

            this.Controls.Add(menuStrip);
            this.MainMenuStrip = menuStrip;

            // StatusStrip
            statusStrip = new StatusStrip
            {
                BackColor = Color.FromArgb(52, 58, 64),
                Font = new Font("Segoe UI", 9)
            };

            lblUsuario = new ToolStripStatusLabel
            {
                ForeColor = Color.White,
                Spring = true,
                TextAlign = ContentAlignment.MiddleLeft
            };

            lblHora = new ToolStripStatusLabel
            {
                ForeColor = Color.White,
                TextAlign = ContentAlignment.MiddleRight
            };

            statusStrip.Items.Add(lblUsuario);
            statusStrip.Items.Add(lblHora);
            this.Controls.Add(statusStrip);

            // Panel de Conteúdo
            panelConteudo = new Panel
            {
                Dock = DockStyle.Fill,
                BackColor = Color.White,
                Padding = new Padding(20)
            };

            // Label de Boas-vindas
            lblBemVindo = new Label
            {
                Text = $"Bem-vindo(a), {user.Nome}!\n\nSelecione uma opção no menu acima para começar.",
                Font = new Font("Segoe UI", 16),
                ForeColor = Color.FromArgb(52, 58, 64),
                TextAlign = ContentAlignment.MiddleCenter,
                Dock = DockStyle.Fill
            };

            panelConteudo.Controls.Add(lblBemVindo);
            this.Controls.Add(panelConteudo);

            // Timer para atualizar hora
            timerHora = new System.Windows.Forms.Timer { Interval = 1000 };
            timerHora.Tick += (s, e) => lblHora.Text = DateTime.Now.ToString("dd/MM/yyyy HH:mm:ss");
            timerHora.Start();
        }

        private void ConfigurarEstilos()
        {
            // Efeitos hover nos menus
            foreach (ToolStripMenuItem item in menuStrip.Items)
            {
                item.MouseEnter += (s, e) => ((ToolStripMenuItem)s).BackColor = Color.FromArgb(180, 40, 55);
                item.MouseLeave += (s, e) => ((ToolStripMenuItem)s).BackColor = Color.Transparent;
            }
        }

        private void AtualizarInformacoes()
        {
            var user = AuthService.Instance.CurrentUser;
            lblUsuario.Text = $"👤 {user.Nome} ({user.Nivel.ToUpper()}) | 📧 {user.Email}";
            lblHora.Text = DateTime.Now.ToString("dd/MM/yyyy HH:mm:ss");
        }

        private void LimparConteudo()
        {
            panelConteudo.Controls.Clear();
        }

        private void MenuAtendimento_Click(object sender, EventArgs e)
        {
            LimparConteudo();
            var formAtendimento = new AtendimentoForm { Dock = DockStyle.Fill };
            panelConteudo.Controls.Add(formAtendimento);
        }

        private void MenuEstornos_Click(object sender, EventArgs e)
        {
            LimparConteudo();
            var formEstornos = new AprovacaoEstornoForm { Dock = DockStyle.Fill };
            panelConteudo.Controls.Add(formEstornos);
        }

        private void MenuRelatorios_Click(object sender, EventArgs e)
        {
            MessageBox.Show(
                "Módulo de Relatórios em desenvolvimento",
                "Em Breve",
                MessageBoxButtons.OK,
                MessageBoxIcon.Information
            );
        }

        private void MenuUsuarios_Click(object sender, EventArgs e)
        {
            MessageBox.Show(
                "Módulo de Gestão de Usuários em desenvolvimento",
                "Em Breve",
                MessageBoxButtons.OK,
                MessageBoxIcon.Information
            );
        }

        private void MenuSair_Click(object sender, EventArgs e)
        {
            var result = MessageBox.Show(
                "Deseja realmente sair do sistema?",
                "Confirmar Saída",
                MessageBoxButtons.YesNo,
                MessageBoxIcon.Question
            );

            if (result == DialogResult.Yes)
            {
                AuthService.Instance.Logout();
                this.Close();
                Application.Exit();
            }
        }

        protected override void OnFormClosing(FormClosingEventArgs e)
        {
            timerHora?.Stop();
            timerHora?.Dispose();
            base.OnFormClosing(e);
        }
    }
}
