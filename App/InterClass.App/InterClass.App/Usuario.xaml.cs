namespace InterClass.App;

using static InterClass.App.Login;

public partial class Usuario : ContentPage
{
	public Usuario()
	{
		InitializeComponent();
        BindingContext = this;
        ErrorMessageVisible = false;
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
    private string trueMessage = "";
    public string TrueMessage
    {
        get => trueMessage;
        set
        {
            trueMessage = value;
            OnPropertyChanged();
        }
    }

    private bool TrueMessageVisible;
    public bool IsTrueMessageVisible
    {
        get => TrueMessageVisible;
        set
        {
            TrueMessageVisible = value;
            OnPropertyChanged();
        }
    }
    private void Senha_TextChanged(object sender, TextChangedEventArgs e)
    {
        if (string.IsNullOrWhiteSpace(e.NewTextValue))
        {
            ErrorMessage = "";
            IsErrorMessageVisible = false;
            return;
        }

        else
        {
            ErrorMessage = "";
            IsErrorMessageVisible = false;
        }
    }

    private async void Botao_Alterar(object sender, EventArgs e)
    {
        ErrorMessage = "";
        IsErrorMessageVisible = false;
        TrueMessage = "";
        IsTrueMessageVisible = false;
        var iduser = idUsuario;
        var service = new Database.Service();
        if (NovaSenha_Entry.Text == ConfirmarSenha_Entry.Text)
        {
            var novasenha = int.Parse(NovaSenha_Entry.Text);
            service.AtualizarSenha(iduser, novasenha);
            TrueMessage = "Senha alterada.";
            IsTrueMessageVisible = true;
        }
        else
        {
            ErrorMessage = "Senhas incoerentes.";
            IsErrorMessageVisible = true;
        }
        
    }
    private async void IrMainPage(object sender, EventArgs e)
    {
        await Shell.Current.GoToAsync("//MainPage");
    }
    private async void Botao_Sair(object sender, EventArgs e)
    {
        idUsuario = 0;
        Login.nomeUsuario = "";
        await Shell.Current.GoToAsync("//MainPage");
    }

}