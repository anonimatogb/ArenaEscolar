using InterClass.App;
using InterClass.App.Database;
using System.Collections.ObjectModel;
using InterClass.App.Database;
using System.Collections.ObjectModel;

namespace InterClass.App;

public partial class Modalidades : ContentPage
{
    

    public Modalidades()
    {
        InitializeComponent();
        BindingContext = this;
    }
    public ObservableCollection<DadosModalidade> ModalidadesLista { get; set; } = new();
    protected override void OnAppearing()
    {
        base.OnAppearing();
        CarregarModalidades();
    }

    private void CarregarModalidades()
    {
        var service = new Database.Service();

        var resultado = service.BuscarModalidades();

        ModalidadesLista.Clear();

        foreach (var modalidade in resultado)
        {
            ModalidadesLista.Add(modalidade);
        }
    }

    private async void Botao_Voltar(object sender, EventArgs e)
    {
        await Shell.Current.GoToAsync("..");
    }
}