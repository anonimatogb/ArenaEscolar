namespace InterClass.App;

public partial class Classificacao : ContentPage
{
	public Classificacao()
	{
        InitializeComponent();
	}
    protected override void OnAppearing()
    {
        base.OnAppearing();
        var service = new Database.Service();
    }
}