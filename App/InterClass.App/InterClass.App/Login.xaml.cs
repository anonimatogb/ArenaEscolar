using static InterClass.App.Database.Service;
namespace InterClass.App
{
    public partial class Login : ContentPage
    {
        public Login()
        {
            InitializeComponent();
            BindingContext = this;
            ErrorMessageVisible= false;
        }

        public static string nomeUsuario { get; set; } = "";
        public static int idUsuario { get; set; }


        private async void Botao_Voltar(object sender, EventArgs e)
        {
            await Shell.Current.GoToAsync("..");
        }
        private string errorMessage = "";
        public string ErrorMessage
        {
            get => errorMessage;
            set
            {
                errorMessage = value;
                OnPropertyChanged();
            }
        }

        private bool ErrorMessageVisible;
        public bool IsErrorMessageVisible
        {
            get => ErrorMessageVisible;
            set
            {
                ErrorMessageVisible = value;
                OnPropertyChanged();
            }
        }
        private void RM_TextChanged(object sender, TextChangedEventArgs e)
        {
            if (string.IsNullOrWhiteSpace(e.NewTextValue))
            {
                ErrorMessage = "";
                IsErrorMessageVisible = false;
                return;
            }

            if (!int.TryParse(e.NewTextValue, out _))
            {
                ErrorMessage = "O RM deve conter apenas números.";
                IsErrorMessageVisible = true;
            }
            else
            {
                ErrorMessage = "";
                IsErrorMessageVisible = false;
            }
        }
        private async void Botao_Login(object sender, EventArgs e)
        {
            if (string.IsNullOrWhiteSpace(RM_Entry.Text) || !int.TryParse(RM_Entry.Text, out int rm))
            {
                errorMessage = "RM inválido.";
                ErrorMessageVisible = true;
                return;
            }

            var senha = Senha_Entry.Text;
            try
            {
                var service = new Database.Service();
                bool usuarioValido = service.VerificarUsuario(rm, senha);
                if (usuarioValido)
                {
                    var idUser = service.IdUsuario(rm);
                    string nome = service.NomeUsuario(idUser);
                    nomeUsuario = nome;
                    idUsuario = idUser;
                    await Shell.Current.GoToAsync("///MainPage");
                }
                else
                {
                    errorMessage = "RM ou senha inválidos.";
                    ErrorMessageVisible = true;
                }
            }
            catch (Exception ex)
            {
                errorMessage = "Falha na conexão";
                ErrorMessageVisible = true;
            }
        }
        private async void IrParaCadastro(object sender, TappedEventArgs e)
        {
            //await Navigation.PushAsync(new Cadastro());
        }
    }
}