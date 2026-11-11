
using System.Windows.Input;

namespace InterClass.App
{
    public partial class MainPage : ContentPage
    {
        public MainPage()
        {
            InitializeComponent();
            BindingContext = new
            {
                Time1_PJ = "Corinthians"
            };
            LoginCommand = new Command(FazerLogin);
            BindingContext = this;

        }
        public ICommand LoginCommand { get;  }
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
    }
}