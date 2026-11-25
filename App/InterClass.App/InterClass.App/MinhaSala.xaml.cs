namespace InterClass.App;

using InterClass.App.Database;
using System.Collections.ObjectModel;

public partial class MinhaSala : ContentPage
{
	public MinhaSala()
	{
		InitializeComponent();
        BindingContext = this;
        var service = new Database.Service();
        
    }
    private async void Botao_Voltar(object sender, EventArgs e)
    {
        await Shell.Current.GoToAsync("..");
    }
    private void Anterior_Clicked(object sender, TappedEventArgs e)
    {
        PaginaAnterior();
    }

    private void Proxima_Clicked(object sender, TappedEventArgs e)
    {
        ProximaPagina();
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
    private string _posicaoGeral;
    public string PosicaoGeral
    {
        get => _posicaoGeral;
        set
        {
            _posicaoGeral = value;
            OnPropertyChanged();
        }
    }
    private string _vinculados;
    public string Vinculados
    {
        get => _vinculados;
        set
        {
            _vinculados = value;
            OnPropertyChanged();
        }
    }
    private List<DadosAluno> _todosAlunos = new();

    private int _paginaAtual = 1;
    private const int AlunosPorPagina = 6;

    public ObservableCollection<DadosAluno> Alunos { get; set; } = new();

    public string TextoPaginacao =>
        $"Mostrando {Alunos.Count} de {_todosAlunos.Count} alunos";

    public string Paginas
    {
        get
        {
            int totalPaginas = (int)Math.Ceiling((double)_todosAlunos.Count / AlunosPorPagina);
            return totalPaginas == 0 ? "0" : $"{_paginaAtual} / {totalPaginas}";
        }
    }
    private void AtualizarPagina()
    {
        Alunos.Clear();

        var alunosPagina = _todosAlunos
            .Skip((_paginaAtual - 1) * AlunosPorPagina)
            .Take(AlunosPorPagina);

        foreach (var aluno in alunosPagina)
        {
            Alunos.Add(aluno);
        }

        OnPropertyChanged(nameof(TextoPaginacao));
        OnPropertyChanged(nameof(Paginas));
    }
    private void ProximaPagina()
    {
        int totalPaginas = (int)Math.Ceiling((double)_todosAlunos.Count / AlunosPorPagina);

        if (_paginaAtual < totalPaginas)
        {
            _paginaAtual++;
            AtualizarPagina();
        }
    }

    private void PaginaAnterior()
    {
        if (_paginaAtual > 1)
        {
            _paginaAtual--;
            AtualizarPagina();
        }
    }
    protected override void OnAppearing()
    {
        base.OnAppearing();

        var service = new Database.Service();

        var sala = service.BuscarDadosSala(Login.idUsuario);

        NomeSala = sala.NomeSala;
        TotalAlunos = sala.QuantidadeAlunos;

        var posicao = service.BuscarClassificacaoSala(sala.IdSala);

        PosicaoGeral = posicao.Posicao + "º";

        Vinculados = sala.QuantidadeAlunos.ToString() + " alunos vinculados à sala";

        _todosAlunos = service.BuscarAlunosSala(Login.idUsuario);

        _paginaAtual = 1;

        string cargo = service.Cargo(Login.idUsuario);

        IsEditarAlunos = cargo?.Trim().Equals(
            "representante",
            StringComparison.OrdinalIgnoreCase
        ) == true;

        AtualizarPagina();
    }
    private bool _isEditarAlunos;
    public bool IsEditarAlunos
    {
        get => _isEditarAlunos;
        set
        {
            _isEditarAlunos = value;
            OnPropertyChanged();
        }
    }
    private async void EditarAlunos(object sender, EventArgs e)
    {
        await Shell.Current.GoToAsync(nameof(EditarAluno));
    }
    private async void IrModalidade(object sender, EventArgs e)
    {
        await Shell.Current.GoToAsync(nameof(Modalidade));
    }
}