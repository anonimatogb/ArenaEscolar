
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
        }
        private async void Calendario(object sender, TappedEventArgs e)
        {
            await Shell.Current.GoToAsync(nameof(Calendario));
        }
        private async void MinhaSala(object sender, TappedEventArgs e)
        {
            await Shell.Current.GoToAsync(nameof(MinhaSala));
        }
        private async void Classificacao(object sender, TappedEventArgs e)
        {
            await Shell.Current.GoToAsync(nameof(Classificacao));
        }
    }
}