using static InterClass.App.Database.Service;
namespace InterClass.App
{
    public partial class Login : ContentPage
    {
        public Login()
        {
            InitializeComponent();
            BindingContext = this;
            IsErrorMessageVisible= false;
        }

        public static string nomeUsuario { get; set; } = "";
        public static int idUsuario { get; set; }


        private async void Botao_Voltar(object sender, EventArgs e)
        {
            await Shell.Current.GoToAsync("..");
        }
        private bool _isRepresentante;

        public bool IsRepresentante
        {
            get => _isRepresentante;
            set
            {
                _isRepresentante = value;
                OnPropertyChanged();
            }
        }

        private string _corAluno = "#F97316";
        private string _corRepresentante = "#64748B";

        public string CorAluno
        {
            get => _corAluno;
            set
            {
                _corAluno = value;
                OnPropertyChanged();
            }
        }

        public string CorRepresentante
        {
            get => _corRepresentante;
            set
            {
                _corRepresentante = value;
                OnPropertyChanged();
            }
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
        private void SelecionarAluno(object sender, EventArgs e)
        {
            IsRepresentante = false;

            CorAluno = "#F97316";
            CorRepresentante = "#64748B";

            Senha_Entry.Text = "";
        }

        private void SelecionarRepresentante(object sender, EventArgs e)
        {
            IsRepresentante = true;

            CorAluno = "#64748B";
            CorRepresentante = "#F97316";
        }
        private async void Botao_Login(object sender, EventArgs e)
        {
            if (string.IsNullOrWhiteSpace(RM_Entry.Text) || !int.TryParse(RM_Entry.Text, out int rm))
            {
                ErrorMessage = "RM inválido.";
                IsErrorMessageVisible = true;
                return;
            }

            var senha = Senha_Entry.Text;
            try
            {
                var service = new Database.Service();
                string cargo = service.Cargo(rm);
                if (IsRepresentante && cargo == "representante")
                {
                    bool representanteValido = service.VerificarUsuarioeSenha(rm, senha);
                    if (representanteValido)
                    {
                        var idUser = service.IdUsuario(rm);
                        Login.idUsuario = idUser;
                        string nome = service.NomeUsuario(idUser);
                        Login.nomeUsuario = nome;
                        await Shell.Current.GoToAsync("///MainPage");
                    }
                    else
                    {
                        ErrorMessage = "RM ou senha incorreto.";
                        IsErrorMessageVisible = true;
                    }
                }
                else if(!IsRepresentante && service.Senha(rm) == null && cargo == "aluno")
                {
                    bool usuarioValido = service.VerificarUsuario(rm);
                    if (usuarioValido)
                    {
                        var idUser = service.IdUsuario(rm);
                        Login.idUsuario = idUser;
                        string nome = service.NomeUsuario(idUser);
                        Login.nomeUsuario = nome;
                        await Shell.Current.GoToAsync("///MainPage");
                    }
                    else
                    {
                        ErrorMessage = "RM ou senha incorreto.";
                        IsErrorMessageVisible = true;
                    }

                }
                else
                {
                    ErrorMessage = "RM ou senha incorreto.";
                    IsErrorMessageVisible = true;
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine(ex.ToString());

                ErrorMessage = ex.Message;
                IsErrorMessageVisible = true;
            }
        }
        public static string GetNomeUsuario()
        {
            return nomeUsuario;
        }
        
    }
}