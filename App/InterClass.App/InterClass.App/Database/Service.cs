using MySqlConnector;
using System.Globalization;
using static InterClass.App.MainPage;

namespace InterClass.App.Database
{
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

        public bool VerificarUsuario(int rm, string senha)
        {
            using var connection =
                new MySqlConnection(_connectionString);

            connection.Open();

            using var cmd = new MySqlCommand(
                "SELECT COUNT(*) FROM usuario WHERE rm = @rm AND senha = @senha",
                connection);

            cmd.Parameters.AddWithValue("@rm", rm);
            cmd.Parameters.AddWithValue("@senha", senha);

            long count = (long)cmd.ExecuteScalar();

            return count > 0;
        }
        public bool VerificarRM(int rm)
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
        public void AtualizarSenha(int id, int novasenha)
        {
            using var connection =
                new MySqlConnection(_connectionString);
            connection.Open();
            using var cmd = new MySqlCommand(
                "UPDATE usuario SET senha = @senha WHERE id = @id",
                connection);
            cmd.Parameters.AddWithValue("@id", id);
            cmd.Parameters.AddWithValue("@senha", novasenha);
            cmd.ExecuteNonQuery();
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
        public void Cadastro(string senha, int rm)
        {
            using var connection =
                new MySqlConnection(_connectionString);
            connection.Open();
            using var cmd = new MySqlCommand(
                "UPDATE usuario SET senha = @senha WHERE rm = @rm",
                connection);
            cmd.Parameters.AddWithValue("@rm", rm);
            cmd.Parameters.AddWithValue("@senha", senha);
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

        public class DadosSala
        {
            public int IdSala { get; set; }
            public string NomeSala { get; set; }
            public int QuantidadeAlunos { get; set; }
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
    }
}