
namespace InterClass.App
{
    public partial class Login : ContentPage
    {

        public Login()
        {
            InitializeComponent();   
        }
        private void Campos_TextChanged(
        object sender,
        TextChangedEventArgs e)
        {
        }
        private async void Button_Clicked(object sender, EventArgs e)
        {
            

        }
        private async void IrParaCadastro(object sender, TappedEventArgs e)
        {
            //await Navigation.PushAsync(new Cadastro());
        }
    }
}