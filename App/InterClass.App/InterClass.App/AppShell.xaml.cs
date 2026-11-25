namespace InterClass.App
{
    public partial class AppShell : Shell
    {
        public AppShell()
        {
            InitializeComponent();
            Routing.RegisterRoute(nameof(Calendario), typeof(Calendario));
            Routing.RegisterRoute(nameof(MinhaSala), typeof(MinhaSala));
            Routing.RegisterRoute(nameof(Classificacao), typeof(Classificacao));
            Routing.RegisterRoute(nameof(EditarAluno), typeof(EditarAluno));
            Routing.RegisterRoute(nameof(Modalidade), typeof(Modalidade));
            Routing.RegisterRoute(nameof(Login), typeof(Login));
            Routing.RegisterRoute(nameof(Calendario), typeof(Calendario));
        }
    }
}
