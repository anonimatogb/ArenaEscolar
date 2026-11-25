using MySqlConnector;
using System.Collections.Generic;
using System.Globalization;
using static InterClass.App.MainPage;
using BCrypt.Net;

namespace InterClass.App.Database
{
    public class DadosSala
    {
        public int Posicao { get; set; }
        public int IdSala { get; set; }
        public string NomeSala { get; set; }
        public int QuantidadeAlunos { get; set; }
    }

    public class DadosClassificacao
    {
        public int Posicao { get; set; }
        public int IdSala { get; set; }
        public string NomeSala { get; set; }
        public int Pontos { get; set; }
    }
    public class DadosAluno
    {
        public int Id { get; set; }
        public string Nome { get; set; }
        public string Modalidades { get; set; }
    }
    public class DadosJogo
    {
        public int Id { get; set; }
        public string Modalidade { get; set; }
        public string TimeA { get; set; }
        public string TimeB { get; set; }
        public DateTime Dia { get; set; }
        public TimeSpan Horario { get; set; }
        public string Quadra { get; set; }
        public string Status { get; set; }
        public bool IsMinhaSala { get; set; }

        public DateTime DataHora => Dia.Date.Add(Horario);

        public string DiaFormatado =>
            Dia.ToString("dddd, dd/MM/yyyy",
                new System.Globalization.CultureInfo("pt-BR"));

        public string HorarioFormatado =>
            Horario.ToString(@"hh\:mm");

        public string CorFundo =>
            IsMinhaSala ? "#FFF1E6" :
            DataHora < DateTime.Now ? "#F1F5F9" : "#FFFFFF";

        public string CorBorda =>
            IsMinhaSala ? "#F97316" : "#E2E8F0";

        public string Identificacao =>
            IsMinhaSala ? "JOGO DA SUA SALA" : "INTERCLASSE";

        public string StatusExibicao
        {
            get
            {
                if (Status == "finalizado")
                    return "Finalizado";

                if (DataHora < DateTime.Now)
                    return "Data passada";

                return "Agendado";
            }
        }

        public string DescricaoProximoJogo =>
            $"{TimeA} × {TimeB} — {Modalidade}";

        }
        
        
    public class Service
    {
        private string _connectionString;

        public Service()
        {
            _connectionString =
                "Server=localhost;Database=interclass;User=root;Password=;SslMode=None;";
        }

        public void InserirUsuario(string nome, int idade)
        {
            using var connection =
                new MySqlConnection(_connectionString);

            connection.Open();

            using var cmd = new MySqlCommand(
                "INSERT INTO usuario (nome, idade) VALUES (@nome, @idade)",
                connection);

            cmd.Parameters.AddWithValue("@nome", nome);
            cmd.Parameters.AddWithValue("@idade", idade);

            cmd.ExecuteNonQuery();
        }

        public bool VerificarUsuario(int rm)
        {
            using var connection =
                new MySqlConnection(_connectionString);

            connection.Open();

            using var cmd = new MySqlCommand(
                "SELECT COUNT(*) FROM usuario WHERE rm = @rm",
                connection);

            cmd.Parameters.AddWithValue("@rm", rm);

            long count = (long)cmd.ExecuteScalar();

            return count > 0;
        }
        public bool VerificarUsuarioeSenha(int rm, string senha)
        {
            using var connection = new MySqlConnection(_connectionString);

            connection.Open();

            using var cmd = new MySqlCommand(
                "SELECT senha FROM usuario WHERE rm = @rm",
                connection);

            cmd.Parameters.AddWithValue("@rm", rm);

            var resultado = cmd.ExecuteScalar();

            if (resultado == null || resultado == DBNull.Value)
                return false;

            string senhaHash = resultado.ToString();

            return BCrypt.Net.BCrypt.Verify(senha, senhaHash);
        }


        public void InserirUsuario(string nome, string cargo)
        {
            using var connection =
                new MySqlConnection(_connectionString);
            connection.Open();
            using var cmd = new MySqlCommand(
                "INSERT INTO usuario (nome, rm, senha, cargo) VALUES (@nome, @rm, @senha, @cargo)",
                connection);
            cmd.Parameters.AddWithValue("@nome", nome);
            cmd.Parameters.AddWithValue("@cargo", cargo);
            cmd.ExecuteNonQuery();
        }

        public bool Senha(int rm)
        {
            using var connection = new MySqlConnection(_connectionString);
            connection.Open();

            using var cmd = new MySqlCommand(
                "SELECT senha FROM usuario WHERE rm = @rm",
                connection);

            cmd.Parameters.AddWithValue("@rm", rm);

            var resultado = cmd.ExecuteScalar();

            return resultado != null &&
                   resultado != DBNull.Value &&
                   !string.IsNullOrWhiteSpace(resultado.ToString());
        }
        public string Cargo(int rm)
        {
            using var connection = new MySqlConnection(_connectionString);
            connection.Open();

            using var cmd = new MySqlCommand(
                "SELECT tipo FROM usuario WHERE rm = @rm",
                connection);

            cmd.Parameters.AddWithValue("@rm", rm);

            var resultado = cmd.ExecuteScalar();

            if (resultado != null)
            {
                return resultado.ToString();
            }

            return "";
        }

        public string NomeUsuario(int id)
        {
            using var connection =
                new MySqlConnection(_connectionString);
            connection.Open();
            using var cmd = new MySqlCommand(
        "SELECT nome FROM usuario WHERE id = @id",
        connection);

            cmd.Parameters.AddWithValue("@id", id);

            var resultado = cmd.ExecuteScalar();

            if (resultado != null)
            {
                return resultado.ToString();
            }

            return "";
        }
        public int IdUsuario(int rm)
        {
            using var connection =
                new MySqlConnection(_connectionString);

            connection.Open();

            using var cmd = new MySqlCommand(
                "SELECT id FROM usuario WHERE rm = @rm",
                connection);

            cmd.Parameters.AddWithValue(
                "@rm",
                rm);

            var resultado =
                cmd.ExecuteScalar();

            if (resultado != null)
            {
                return Convert.ToInt32(
                    resultado);
            }

            return 0;
        }
        public int CargaCurso(int idCurso)
        {
            using var connection =
                new MySqlConnection(_connectionString);
            connection.Open();
            using var cmd = new MySqlCommand(
        "SELECT carga_horaria FROM cursos WHERE id = @id",
        connection);

            cmd.Parameters.AddWithValue("@id", idCurso);

            var resultado = cmd.ExecuteScalar();

            if (resultado != null)
            {
                return Convert.ToInt32(resultado);
            }

            return 0;
        }

        

        public DadosSala BuscarDadosSala(int idUsuario)
        {
            using var connection = new MySqlConnection(_connectionString);
            connection.Open();

            string sql = @"
        SELECT 
            s.id,
            s.nome,
            COUNT(u.id) AS quantidade_alunos
        FROM usuario u
        INNER JOIN sala s ON u.id_sala = s.id
        WHERE s.id = (
            SELECT id_sala
            FROM usuario
            WHERE id = @idUsuario
        )
        GROUP BY s.id, s.nome";

            using var cmd = new MySqlCommand(sql, connection);
            cmd.Parameters.AddWithValue("@idUsuario", idUsuario);

            using var reader = cmd.ExecuteReader();

            if (reader.Read())
            {
                return new DadosSala
                {
                    IdSala = Convert.ToInt32(reader["id"]),
                    NomeSala = reader["nome"].ToString(),
                    QuantidadeAlunos = Convert.ToInt32(reader["quantidade_alunos"])
                };
            }

            return null;
        }
        public List<DadosClassificacao> BuscarClassificacao()
        {
            var classificacao = new List<DadosClassificacao>();

            using var connection = new MySqlConnection(_connectionString);
            connection.Open();

            string sql = @"
        SELECT 
            id,
            nome,
            pontos
        FROM sala
        ORDER BY pontos DESC";

            using var cmd = new MySqlCommand(sql, connection);
            using var reader = cmd.ExecuteReader();

            int posicao = 1;

            while (reader.Read())
            {
                posicao++;
            }

            return classificacao;
        }
        public DadosClassificacao BuscarClassificacaoSala(int idSala)
        {
            using var connection = new MySqlConnection(_connectionString);
            connection.Open();

            string sql = @"
        SELECT 
            id,
            nome,
            pontos,
            (SELECT COUNT(*) + 1
             FROM sala AS s2
             WHERE s2.pontos > s1.pontos) AS posicao
        FROM sala AS s1
        WHERE id = @idSala";

            using var cmd = new MySqlCommand(sql, connection);
            cmd.Parameters.AddWithValue("@idSala", idSala);

            using var reader = cmd.ExecuteReader();

            if (reader.Read())
            {
                return new DadosClassificacao
                {
                    Posicao = Convert.ToInt32(reader["posicao"]),
                    IdSala = Convert.ToInt32(reader["id"]),
                    NomeSala = reader["nome"].ToString(),
                    Pontos = Convert.ToInt32(reader["pontos"])
                };
            }

            return null;
        }
        public List<DadosAluno> BuscarAlunosSala(int idUsuario)
        {
            var alunos = new List<DadosAluno>();

            using var connection = new MySqlConnection(_connectionString);
            connection.Open();

            string sql = @"
        SELECT
            u.id,
            u.nome,
            '' AS modalidades
        FROM usuario u
        WHERE u.id_sala = (
            SELECT id_sala
            FROM usuario
            WHERE id = @idUsuario
        )
        ORDER BY
            CASE WHEN u.id = @idUsuario THEN 0 ELSE 1 END,
            u.nome ASC";

            using var cmd = new MySqlCommand(sql, connection);
            cmd.Parameters.AddWithValue("@idUsuario", idUsuario);

            using var reader = cmd.ExecuteReader();

            while (reader.Read())
            {
                alunos.Add(new DadosAluno
                {
                    Id = Convert.ToInt32(reader["id"]),
                    Nome = reader["nome"].ToString(),
                    Modalidades = reader["modalidades"].ToString()
                });
            }

            return alunos;
        }
        public List<DadosJogo> BuscarJogos(int idUsuario)
        {
            var jogos = new List<DadosJogo>();

            using var connection = new MySqlConnection(_connectionString);
            connection.Open();

            string sql = @"
        SELECT
            j.id,
            m.nome AS modalidade,
            sA.nome AS timeA,
            sB.nome AS timeB,
            j.dia,
            j.horario,
            j.quadra,
            j.status,

            CASE
                WHEN u.id_sala IS NOT NULL
                AND (tA.id_sala = u.id_sala OR tB.id_sala = u.id_sala)
                THEN 1
                ELSE 0
            END AS minha_sala

        FROM jogo j

        INNER JOIN modalidade m
            ON j.id_modalidade = m.id

        INNER JOIN `time` tA
            ON j.id_timeA = tA.id

        INNER JOIN `time` tB
            ON j.id_timeB = tB.id

        INNER JOIN sala sA
            ON tA.id_sala = sA.id

        INNER JOIN sala sB
            ON tB.id_sala = sB.id

        LEFT JOIN usuario u
            ON u.id = @idUsuario

        ORDER BY j.dia ASC, j.horario ASC";

            using var cmd = new MySqlCommand(sql, connection);

            cmd.Parameters.AddWithValue("@idUsuario", idUsuario);

            using var reader = cmd.ExecuteReader();

            while (reader.Read())
            {
                jogos.Add(new DadosJogo
                {
                    Id = Convert.ToInt32(reader["id"]),
                    Modalidade = reader["modalidade"].ToString(),
                    TimeA = reader["timeA"].ToString(),
                    TimeB = reader["timeB"].ToString(),
                    Dia = Convert.ToDateTime(reader["dia"]),
                    Horario = reader["horario"] == DBNull.Value
                        ? TimeSpan.Zero
                        : TimeSpan.Parse(reader["horario"].ToString()),
                    Quadra = reader["quadra"].ToString(),
                    Status = reader["status"].ToString(),
                    IsMinhaSala = Convert.ToInt32(reader["minha_sala"]) == 1
                });
            }

            return jogos;
        }
    }
}