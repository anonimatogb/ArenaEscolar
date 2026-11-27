namespace InterClass.App;

using InterClass.App.Database;
using System.Collections.ObjectModel;
using System.Globalization;

public partial class Calendario : ContentPage
{
    private bool _somenteMinhaSala = false;
    private List<DadosJogo> _todosJogos = new();

    private DateTime _mesAtual = new DateTime(
        DateTime.Today.Year,
        DateTime.Today.Month,
        1
    );

    public Calendario()
    {
        InitializeComponent();
        BindingContext = this;
    }

    public ObservableCollection<DadosJogo> Jogos { get; set; } = new();

    private string _tituloMes = "";

    public string TituloMes
    {
        get => _tituloMes;
        set
        {
            _tituloMes = value;
            OnPropertyChanged();
        }
    }
    private bool _isFiltroAberto;

    public bool IsFiltroAberto
    {
        get => _isFiltroAberto;
        set
        {
            _isFiltroAberto = value;
            OnPropertyChanged();
        }
    }
    private void AbrirFiltro_Clicked(object sender, EventArgs e)
    {
        IsFiltroAberto = !IsFiltroAberto;
    }

    private void TodosJogos_Clicked(object sender, EventArgs e)
    {
        _somenteMinhaSala = false;
        FiltroSelecionado = "Todos os jogos";

        IsFiltroAberto = false;

        AtualizarCalendario();
    }

    private void MinhaSala_Clicked(object sender, EventArgs e)
    {
        _somenteMinhaSala = true;
        FiltroSelecionado = "Jogos da minha sala";

        IsFiltroAberto = false;

        AtualizarCalendario();
    }

    private string _filtroSelecionado = "Todos os jogos";

    public string FiltroSelecionado
    {
        get => _filtroSelecionado;
        set
        {
            _filtroSelecionado = value;
            OnPropertyChanged();
        }
    }

    private DadosJogo _proximoJogo;

    public DadosJogo ProximoJogo
    {
        get => _proximoJogo;
        set
        {
            _proximoJogo = value;
            OnPropertyChanged();
            OnPropertyChanged(nameof(ProximoJogoTexto));
            OnPropertyChanged(nameof(TemProximoJogo));
        }
    }

    public string ProximoJogoTexto =>
        ProximoJogo == null
        ? "Nenhum jogo futuro agendado."
        : $"{ProximoJogo.DescricaoProximoJogo}\n" +
          $"{ProximoJogo.DiaFormatado} às {ProximoJogo.HorarioFormatado}";

    public bool TemProximoJogo => ProximoJogo != null;
   

    protected override void OnAppearing()
    {
        base.OnAppearing();
        CarregarJogos();
    }

    private void CarregarJogos()
    {
        var service = new Database.Service();

        _todosJogos = service.BuscarJogos(Login.idUsuario);

        ProximoJogo = _todosJogos
            .Where(j => j.Status == "agendado" && j.DataHora >= DateTime.Now)
            .OrderBy(j => j.DataHora)
            .FirstOrDefault();

        AtualizarCalendario();
    }

    private void AtualizarCalendario()
    {
        TituloMes = _mesAtual.ToString(
            "MMMM yyyy",
            new CultureInfo("pt-BR")
        );

        TituloMes = char.ToUpper(TituloMes[0]) + TituloMes.Substring(1);

        Jogos.Clear();

        var jogosDoMes = _todosJogos
            .Where(j =>
                j.Dia.Month == _mesAtual.Month &&
                j.Dia.Year == _mesAtual.Year
            );

        if (_somenteMinhaSala)
        {
            jogosDoMes = jogosDoMes.Where(j => j.IsMinhaSala);
        }

        foreach (var jogo in jogosDoMes
            .OrderBy(j => j.Dia)
            .ThenBy(j => j.Horario))
        {
            Jogos.Add(jogo);
        }

        OnPropertyChanged(nameof(Jogos));
    }

    private void MesAnterior_Clicked(object sender, EventArgs e)
    {
        _mesAtual = _mesAtual.AddMonths(-1);
        AtualizarCalendario();
    }

    private void ProximoMes_Clicked(object sender, EventArgs e)
    {
        _mesAtual = _mesAtual.AddMonths(1);
        AtualizarCalendario();
    }

    private async void Botao_Voltar(object sender, EventArgs e)
    {
        await Shell.Current.GoToAsync("..");
    }
}