namespace InterClass.App;

using InterClass.App.Database;
using System.Collections.ObjectModel;

public partial class Classificacao : ContentPage
{
    public ObservableCollection<DadosClassificacao> Salas { get; set; } = new();

    public Classificacao()
    {
        InitializeComponent();
        BindingContext = this;
    }

    protected override void OnAppearing()
    {
        base.OnAppearing();

        CarregarClassificacao();
    }

    private void CarregarClassificacao()
    {
        try
        {
            var service = new Database.Service();

            // Descobrir qual é a sala do usuário conectado
            int idSalaUsuario = 0;

            if (Login.idUsuario != 0)
            {
                var dadosSala = service.BuscarDadosSala(Login.idUsuario);

                if (dadosSala != null)
                {
                    idSalaUsuario = dadosSala.IdSala;
                }
            }

            // Buscar todas as salas classificadas
            var classificacao = service.BuscarClassificacao();

            Salas.Clear();

            foreach (var sala in classificacao)
            {
                sala.IsMinhaSala =
                    idSalaUsuario != 0 &&
                    sala.IdSala == idSalaUsuario;

                Salas.Add(sala);
            }
        }
        catch (Exception ex)
        {
            System.Diagnostics.Debug.WriteLine(
                $"Erro ao carregar classificação: {ex.Message}"
            );
        }
    }

    private async void Botao_Voltar(object sender, EventArgs e)
    {
        await Shell.Current.GoToAsync("..");
    }
}