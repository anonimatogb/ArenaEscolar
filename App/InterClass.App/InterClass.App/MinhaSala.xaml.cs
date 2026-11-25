namespace InterClass.App;

public partial class MinhaSala : ContentPage
{
	public MinhaSala()
	{
		InitializeComponent();
        BindingContext = this;
    }
    private async void Botao_Voltar(object sender, EventArgs e)
    {
        await Shell.Current.GoToAsync("..");
    }
    
    private string _nomeSala;

    public string NomeSala
    {
        get => _nomeSala;
        set
        {
            _nomeSala = value;
            OnPropertyChanged();
        }
    }
    private int _totalAlunos;

    public int TotalAlunos
    {
        get => _totalAlunos;
        set
        {
            _totalAlunos = value;
            OnPropertyChanged();
        }
    }
    protected override void OnAppearing()
    {
        base.OnAppearing();
        var service = new Database.Service();
        var sala = service.BuscarDadosSala(Login.idUsuario);
        NomeSala = sala.NomeSala;
        TotalAlunos = sala.QuantidadeAlunos;
    }
}