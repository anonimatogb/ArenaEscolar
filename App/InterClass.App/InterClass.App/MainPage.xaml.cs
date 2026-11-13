using System.Globalization;
using System.Windows.Input;
using static InterClass.App.Login;

namespace InterClass.App
{
    public partial class MainPage : ContentPage
    {
        public MainPage()
        {
            InitializeComponent();
            LoginCommand = new Command(async () =>
            {
                if (idUsuario == 0)
                    await Shell.Current.GoToAsync(nameof(Login));
                else
                    await Shell.Current.GoToAsync(nameof(Usuario));
            });
            BindingContext = this;
        }

        protected override void OnAppearing()
        {
            base.OnAppearing();
            AtualizarEstadoLogin();
        }

        private void AtualizarEstadoLogin()
        {
            IsLoggedIn = idUsuario != 0;
            NomeLogin = IsLoggedIn ? nomeUsuario : "Fazer Login";
            UserName = nomeUsuario;
        }

        private bool _isLoggedIn;
        public bool IsLoggedIn
        {
            get => _isLoggedIn;
            set
            {
                _isLoggedIn = value;
                OnPropertyChanged();
                OnPropertyChanged(nameof(IsNotLoggedIn));
            }
        }

        public bool IsNotLoggedIn => !IsLoggedIn;

        private string _nomeLogin = "Fazer Login";
        public string NomeLogin
        {
            get => _nomeLogin;
            set
            {
                _nomeLogin = value;
                OnPropertyChanged();
            }
        }

        private string? _userName;
        public string? UserName
        {
            get => _userName;
            set
            {
                _userName = value;
                OnPropertyChanged();
            }
        }

        public string Time1_PJ { get; set; } = "Corinthians";

        public ICommand LoginCommand { get; }

        private async void IrCalendario(object sender, EventArgs e)
        {
            await Shell.Current.GoToAsync(nameof(Calendario));
        }
        private async void IrMinhaSala(object sender, EventArgs e)
        {
            await Shell.Current.GoToAsync(nameof(MinhaSala));
        }
        private async void IrClassificacao(object sender, EventArgs e)
        {
            await Shell.Current.GoToAsync(nameof(Classificacao));
        }
        private async void FazerLogin()
        {
            await Shell.Current.GoToAsync(nameof(Login));
        }
        private async void IrUsuario()
        {
            await Shell.Current.GoToAsync(nameof(Usuario));
        }
    }
}