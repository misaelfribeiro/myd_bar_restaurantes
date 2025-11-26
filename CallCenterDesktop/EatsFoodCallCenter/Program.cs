using System;
using System.Windows.Forms;
using EatsFoodCallCenter.Forms;

namespace EatsFoodCallCenter
{
    internal static class Program
    {
        [STAThread]
        static void Main()
        {
            ApplicationConfiguration.Initialize();
            
            // Iniciar com LoginForm
            Application.Run(new LoginForm());
        }
    }
}