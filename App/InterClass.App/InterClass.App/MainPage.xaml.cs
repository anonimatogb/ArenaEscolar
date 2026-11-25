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
                {
                    await Shell.Current.GoToAsync(nameof(Login));
                }
                else
                {
                    await Shell.Current.GoToAsync(nameof(Usuario));
                }
            });
            BindingContext = this;
            

        }
        protected override void OnAppearing()
        {
            base.OnAppearing();

            if (Login.idUsuario != 0)
            {
                NomeLogin = Login.nomeUsuario;
                IsLoggedIn = true;
            }
            else
            {
                NomeLogin = "Fazer Login";
                IsLoggedIn = false;
            }
        }

        private string _nomeLogin;

        public string NomeLogin
        {
            get => _nomeLogin;
            set
            {
                if (_nomeLogin != value)
                {
                    _nomeLogin = value; // CORREÇÃO: Atribuindo o valor para a variável privada!
                    OnPropertyChanged();
                }
            }
        }
        private bool _isLoggedIn;

        public bool IsLoggedIn
        {
            get => _isLoggedIn;
            set
            {
                _isLoggedIn = value;
                OnPropertyChanged();

            }
        }

        public string Time1_PJ { get; set; }

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