
using System.Windows.Input;

namespace InterClass.App
{
    public partial class MainPage : ContentPage
    {
        public MainPage()
        {
            InitializeComponent();

            LoginCommand = new Command(async () =>
            {
                if (Login.idUsuario == 0)
                {
                    await Shell.Current.GoToAsync(nameof(Login));
                }
                else
                {
                    IsSairVisible = !IsSairVisible;
                }
            });

            SairCommand = new Command(() =>
            {
                Login.idUsuario = 0;
                Login.nomeUsuario = "";

                NomeLogin = "Fazer Login";
                IsLoggedIn = false;

                MinhaSala = "Faça o login para ver informações sobre sua sala";
                NumeroAlunos_MS = 0;

                IsSairVisible = false;
                IsConfirmarSaidaVisible = false;

                CarregarProximoJogo();
            });

            ConfirmarSaidaCommand = new Command(() =>
            {
                Login.idUsuario = 0;
                Login.nomeUsuario = "";

                NomeLogin = "Fazer Login";
                IsLoggedIn = false;

                MinhaSala = "Faça o login para ver informações sobre sua sala";
                NumeroAlunos_MS = 0;

                IsSairVisible = false;
                IsConfirmarSaidaVisible = false;

                CarregarProximoJogo();
            });

            BindingContext = this;
        }

        // COMANDOS

        public Command LoginCommand { get; set; }
        public Command SairCommand { get; set; }
        public Command ConfirmarSaidaCommand { get; set; }


        // VISIBILIDADE DO BOTÃO SAIR

        private bool _isSairVisible;

        public bool IsSairVisible
        {
            get => _isSairVisible;
            set
            {
                _isSairVisible = value;
                OnPropertyChanged();
            }
        }

        private bool _isConfirmarSaidaVisible;

        public bool IsConfirmarSaidaVisible
        {
            get => _isConfirmarSaidaVisible;
            set
            {
                _isConfirmarSaidaVisible = value;
                OnPropertyChanged();
            }
        }


        // INFORMAÇÕES DA SALA

        private string _minhaSala = string.Empty;

        public string MinhaSala
        {
            get => _minhaSala;
            set
            {
                _minhaSala = value;
                OnPropertyChanged();
            }
        }

        private int _numeroAlunos_MS;

        public int NumeroAlunos_MS
        {
            get => _numeroAlunos_MS;
            set
            {
                _numeroAlunos_MS = value;
                OnPropertyChanged();
            }
        }


        // INFORMAÇÕES DO PRÓXIMO JOGO

        private string _modalidadePJ = "Carregando...";

        public string Modalidade_PJ
        {
            get => _modalidadePJ;
            set
            {
                _modalidadePJ = value;
                OnPropertyChanged();
            }
        }

        private string _time1PJ = string.Empty;

        public string Time1_PJ
        {
            get => _time1PJ;
            set
            {
                _time1PJ = value;
                OnPropertyChanged();
            }
        }

        private string _time2PJ = string.Empty;

        public string Time2_PJ
        {
            get => _time2PJ;
            set
            {
                _time2PJ = value;
                OnPropertyChanged();
            }
        }

        private string _diaHorarioLocal = string.Empty;

        public string Dia_Horario_Local
        {
            get => _diaHorarioLocal;
            set
            {
                _diaHorarioLocal = value;
                OnPropertyChanged();
            }
        }

        private bool _temProximoJogo;

        public bool TemProximoJogo
        {
            get => _temProximoJogo;
            set
            {
                _temProximoJogo = value;
                OnPropertyChanged();
            }
        }


        // BUSCAR PRÓXIMO JOGO

        private void CarregarProximoJogo()
        {
            try
            {
                var service = new Database.Service();

                var jogos = service.BuscarJogos(Login.idUsuario);

                var proximoJogo = jogos
                    .Where(j =>
                        string.Equals(
                            j.Status,
                            "agendado",
                            StringComparison.OrdinalIgnoreCase
                        )
                        && j.DataHora >= DateTime.Now
                    )
                    .OrderBy(j => j.DataHora)
                    .FirstOrDefault();

                if (proximoJogo == null)
                {
                    Modalidade_PJ = "Nenhum jogo futuro";
                    Time1_PJ = "-";
                    Time2_PJ = "-";
                    Dia_Horario_Local = "Aguardando novos confrontos";

                    TemProximoJogo = false;
                    return;
                }

                Modalidade_PJ = proximoJogo.Modalidade;
                Time1_PJ = proximoJogo.TimeA;
                Time2_PJ = proximoJogo.TimeB;

                Dia_Horario_Local =
                    $"{proximoJogo.Dia:dd/MM/yyyy} às " +
                    $"{proximoJogo.HorarioFormatado} · " +
                    $"{proximoJogo.Quadra}";

                TemProximoJogo = true;
            }
            catch (Exception ex)
            {
                Modalidade_PJ = "Erro ao carregar jogo";
                Time1_PJ = "-";
                Time2_PJ = "-";
                Dia_Horario_Local = "Não foi possível carregar os confrontos";

                TemProximoJogo = false;

                System.Diagnostics.Debug.WriteLine(
                    $"Erro ao carregar próximo jogo: {ex.Message}"
                );
            }
        }


        // CARREGAR INFORMAÇÕES DA SALA

        private void CarregarMinhaSala()
        {
            if (Login.idUsuario == 0)
            {
                MinhaSala = "Faça o login para ver informações sobre sua sala";
                NumeroAlunos_MS = 0;
                return;
            }

            try
            {
                var service = new Database.Service();

                var sala = service.BuscarDadosSala(Login.idUsuario);

                if (sala != null)
                {
                    MinhaSala = sala.NomeSala;
                    NumeroAlunos_MS = sala.QuantidadeAlunos;
                }
                else
                {
                    MinhaSala = "Você ainda não possui uma sala vinculada";
                    NumeroAlunos_MS = 0;
                }
            }
            catch (Exception ex)
            {
                MinhaSala = "Não foi possível carregar sua sala";
                NumeroAlunos_MS = 0;

                System.Diagnostics.Debug.WriteLine(
                    $"Erro ao carregar sala: {ex.Message}"
                );
            }
        }


        // ATUALIZAR PÁGINA

        protected override void OnAppearing()
        {
            base.OnAppearing();

            if (Login.idUsuario != 0)
            {
                NomeLogin = Login.nomeUsuario;
                IsLoggedIn = true;
            }
            else
            {
                NomeLogin = "Fazer Login";
                IsLoggedIn = false;
            }

            CarregarMinhaSala();
            CarregarProximoJogo();
        }


        // NOME DO BOTÃO DE LOGIN

        private string _nomeLogin = "Fazer Login";

        public string NomeLogin
        {
            get => _nomeLogin;
            set
            {
                if (_nomeLogin != value)
                {
                    _nomeLogin = value;
                    OnPropertyChanged();
                }
            }
        }


        // ESTADO DE LOGIN

        private bool _isLoggedIn;

        public bool IsLoggedIn
        {
            get => _isLoggedIn;
            set
            {
                _isLoggedIn = value;
                OnPropertyChanged();
            }
        }


        // NAVEGAÇÃO

        private async void IrCalendario(object sender, EventArgs e)
        {
            await Shell.Current.GoToAsync(nameof(Calendario));
        }

        private async void IrMinhaSala(object sender, EventArgs e)
        {
            await Shell.Current.GoToAsync(nameof(MinhaSala));
        }

        private async void IrClassificacao(object sender, EventArgs e)
        {
            await Shell.Current.GoToAsync(nameof(Classificacao));
        }

        private async void FazerLogin()
        {
            await Shell.Current.GoToAsync(nameof(Login));
        }
    }
}
