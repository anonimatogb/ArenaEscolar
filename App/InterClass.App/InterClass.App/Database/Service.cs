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

        public void InserirUsuario(string nome, string email, string senha, string cargo)
        {
            using var connection =
                new MySqlConnection(_connectionString);
            connection.Open();
            using var cmd = new MySqlCommand(
                "INSERT INTO usuario (nome, rm, senha, cargo) VALUES (@nome, @rm, @senha, @cargo)",
                connection);
            cmd.Parameters.AddWithValue("@nome", nome);
            cmd.Parameters.AddWithValue("@rm", email);
            cmd.Parameters.AddWithValue("@senha", senha);
            cmd.Parameters.AddWithValue("@cargo", cargo);
            cmd.ExecuteNonQuery();
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
        public string Professor(int idCurso)
        {
            using var connection =
                new MySqlConnection(_connectionString);

            connection.Open();

            using var cmd = new MySqlCommand(
                "SELECT professor FROM cursos WHERE id = @id",
                connection);

            cmd.Parameters.AddWithValue(
                "@id",
                idCurso);

            var resultado =
                cmd.ExecuteScalar();

            if (resultado != null)
            {
                int idProfessor =
                    Convert.ToInt32(resultado);

                using var cmd2 =
                    new MySqlCommand(
                        "SELECT nome FROM usuarios WHERE id = @idProfessor",
                        connection);

                cmd2.Parameters.AddWithValue(
                    "@idProfessor",
                    idProfessor);

                var nomeProfessor =
                    cmd2.ExecuteScalar();

                if (nomeProfessor != null)
                {
                    return nomeProfessor.ToString();
                }
            }

            return "";
        }
    }
}