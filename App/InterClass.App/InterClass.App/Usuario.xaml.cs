namespace InterClass.App;

using static InterClass.App.Login;

public partial class Usuario : ContentPage
{
	public Usuario()
	{
		InitializeComponent();
	}

    private void Botao_Alterar(object sender, EventArgs e)
    {
        var iduser = idUsuario;
        var service = new Database.Service();
        if (NovaSenha_Entry.Text == ConfirmarSenha_Entry.Text)
        {
            var novasenha = int.Parse(NovaSenha_Entry.Text);
            service.AtualizarSenha(iduser, novasenha);
        }

    }
}