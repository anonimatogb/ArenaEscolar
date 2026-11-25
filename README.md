# Arena Escolar / InterClass

Arena Escolar (também apresentado como InterClass) é um sistema para organizar e acompanhar um interclasse escolar. O projeto combina um site em PHP para administradores, representantes e alunos com um aplicativo mobile/desktop desenvolvido em .NET MAUI.

O sistema permite cadastrar usuários, salas, modalidades e jogos; organizar atletas em times; acompanhar partidas e placares; consultar a classificação das salas; e visualizar a agenda do evento.

## Funcionalidades

- Cadastro e autenticação de professores, representantes e alunos.
- Gestão de salas de aula e participantes.
- Cadastro de modalidades esportivas e valores de pontuação.
- Criação de times por sala e modalidade.
- Agendamento de confrontos por data, horário e quadra.
- Registro de placar ao vivo.
- Ranking geral das salas.
- Consulta de jogos por data e escalações dos times.
- Aplicativo .NET MAUI para consulta e contas de usuários.

## Tecnologias

- **Site:** PHP 8+, PDO e MySQL/MariaDB.
- **Banco de dados:** MySQL ou MariaDB.
- **Aplicativo:** .NET 10, .NET MAUI e XAML.
- **Conector MAUI:** MySqlConnector.
- **Ambiente local recomendado:** XAMPP no Windows.
- **Front-end:** HTML, CSS e JavaScript.

## Estrutura do projeto

```text
ArenaEscolar/
├── index.php                 # Página inicial pública e agenda
├── README.md
├── DB/
│   └── database.php          # Conexão PDO usada pelo site
├── Db_backup/
│   └── interclass.sql        # Estrutura e dados de exemplo do banco
├── Site/
│   ├── Controller/           # Regras de entrada e coordenação
│   ├── Model/                # Acesso aos dados
│   ├── Style/                # Estilos das páginas PHP
│   └── View/                 # Páginas e formulários PHP
└── App/
    └── InterClass.App/
        └── InterClass.App/   # Aplicação .NET MAUI
```

## Como executar o site

1. Instale o XAMPP ou outro servidor com PHP e MySQL/MariaDB.
2. Coloque a pasta do projeto em `C:\xampp\htdocs\ArenaEscolar`.
3. Inicie **Apache** e **MySQL** no painel do XAMPP.
4. Abra o phpMyAdmin em `http://localhost/phpmyadmin`.
5. Crie/importe o banco `interclass` usando `Db_backup/interclass.sql`.
6. Confirme as configurações em `DB/database.php`:

```php
$host = '127.0.0.1';
$dbname = 'interclass';
$username = 'root';
$password = '';
```

7. Acesse `http://localhost/ArenaEscolar/`.

O backup já contém modalidades, salas e usuários de exemplo. Consulte os dados de login no próprio banco antes de utilizar registros com senha.

## Como executar o aplicativo MAUI

O projeto está em `App/InterClass.App/InterClass.App.csproj`.

Pré-requisitos:

- .NET 10 SDK.
- Visual Studio com carga de trabalho **.NET Multi-platform App UI development**.
- Android, Windows, iOS ou Mac Catalyst configurado conforme a plataforma desejada.
- MariaDB/MySQL acessível a partir da plataforma escolhida.

Na raiz do projeto do aplicativo, execute:

```powershell
dotnet restore
dotnet build
dotnet run
```

Também é possível abrir `App/InterClass.App/InterClass.App.slnx` no Visual Studio e escolher o dispositivo na barra de execução.

A conexão do aplicativo usa `Server=localhost;Database=interclass;User=root;Password=;SslMode=None;` em `App/InterClass.App/InterClass.App/Database/Service.cs`. Em celulares ou dispositivos físicos, `localhost` representa o próprio dispositivo; nesse caso é necessário informar o IP da máquina que hospeda o banco e garantir que o MySQL aceite conexões remotas.

## Banco de dados

As principais entidades são:

| Tabela | Finalidade |
|---|---|
| `usuario` | Professores, representantes e alunos |
| `sala` | Turmas/salas participantes |
| `modalidade` | Esportes e regras de pontuação |
| `time` | Time de uma sala em uma modalidade |
| `time_jogador` | Escalação dos jogadores em cada time |
| `jogo` | Confrontos agendados |
| `placar` | Pontuação registrada para cada jogo |

Relacionamentos principais:

- Um usuário pertence a uma sala.
- Uma sala pode ter times em várias modalidades.
- Um time pertence a uma sala e a uma modalidade.
- Jogadores são vinculados aos times pela tabela `time_jogador`.
- Um jogo possui uma modalidade e duas salas/times.
- O placar pertence ao jogo correspondente.

## Páginas do site

### Página inicial pública

**Arquivo:** `index.php`

- Exibe jogos do dia ou da data selecionada.
- Permite filtrar confrontos por data.
- Mostra placar apenas para partidas finalizadas.
- Exibe escalações quando o usuário está conectado.
- Lista os próximos jogos nos quais o usuário está escalado.
- Apresenta o ranking das salas por pontuação.
- Direciona o visitante para o login de professor ou usuário.

O link exibido muda conforme a sessão: painel do professor, painel do representante, página do aluno ou opções de login.

### Login do professor

**Arquivo:** `Site/View/Administrador/Login.php`

- Recebe nome de usuário e senha.
- Valida o usuário do tipo `admin` por hash de senha.
- Cria a sessão `ADM`.
- Redireciona para o painel do professor.

### Painel do professor

**Arquivo:** `Site/View/Administrador/inicio.php`

- Cadastra representantes informando nome, RM, senha, sala e gênero.
- Cadastra novos professores.
- Cadastra modalidades e define participantes máximos e gênero.
- Lista modalidades e salas.
- Agenda confrontos escolhendo modalidade, salas, data, horário e quadra.
- Lista os jogos cadastrados e oferece acesso ao placar ao vivo.
- Permite registrar o placar final de uma partida.
- Contém links para visualizar o site e encerrar a sessão.

### Gestão antiga de jogos

**Arquivo:** `Site/View/Administrador/jogos.php`

- Tela alternativa de agendamento de confrontos.
- Permite selecionar modalidade, salas, data, horário e quadra.
- Lista jogos cadastrados.
- Exibe um formulário de placar para partidas finalizadas.

Essa página é uma implementação paralela à gestão de jogos do painel principal.

### Placar ao vivo

**Arquivo:** `Site/View/Administrador/placar.php`

- Controla os pontos das duas salas em tempo real.
- Permite aumentar ou diminuir o placar.
- Usa requisições AJAX e token CSRF.
- Atualiza a pontuação sem recarregar a página.
- Disponível somente para usuários autenticados como professores.

### Login de representante ou aluno

**Arquivo:** `Site/View/Usuario/Login.php`

- O usuário informa o RM.
- Alunos com RM cadastrado entram diretamente.
- Representantes são encaminhados para uma segunda etapa que solicita a senha.
- No primeiro acesso de um representante, uma senha válida pode ser criada.
- Após autenticação, cria a sessão correspondente e redireciona à página inicial.

### Página inicial do aluno

**Arquivo:** `Site/View/Usuario/inicio.php`

- Página simples de boas-vindas para alunos autenticados.
- Exibe o nome do usuário.
- Contém links de início, perfil, página pública e saída.

### Painel do representante

**Arquivo:** `Site/View/Representante/inicio.php`

- Cadastra alunos da sala e vincula cada aluno às modalidades selecionadas.
- Lista todos os alunos da sala.
- Permite editar nome, RM, gênero e modalidades de um aluno.
- Permite atualizar nome e senha do representante.
- Lista os times da sala e suas modalidades.
- Exibe mensagens de sucesso ou erro por meio de feedback na própria página.

### Cadastro e edição de representantes

**Arquivos:**

- `Site/View/Usuario/CadastroRepresentante.php`
- `Site/View/Representante/editarrepresentante.php`

- Cadastram ou atualizam representantes e suas credenciais.
- Utilizam o modelo de usuário e exigem informações da sala.

### Cadastro e edição de alunos

**Arquivos:**

- `Site/View/Usuario/CadastroAluno.php`
- `Site/View/Representante/aluno.php`

- Cadastram alunos em uma sala.
- Permitem selecionar gênero e modalidades.
- Associam o RM do aluno aos times correspondentes.

### Consulta de salas

**Arquivo:** `Site/View/Sala/CadastroSala.php`

- Lista as salas existentes no banco.
- Funciona como consulta das turmas cadastradas no evento.
- Disponível para usuários autenticados.

### Cadastro de times

**Arquivo:** `Site/View/Time/CadastroTime.php`

- Disponível para representantes autenticados.
- Seleciona uma modalidade ainda não usada pela sala.
- Lista alunos disponíveis para aquela modalidade.
- Permite selecionar os participantes e criar o time.
- Respeita o limite máximo de jogadores configurado na modalidade.

### Gestão de modalidades

**Arquivos:**

- `Site/View/Modalidade/modalidade.php`
- `Site/View/Modalidade/formulario.php`
- `Site/View/Modalidade/listar.php`

- Cadastram, exibem e listam modalidades.
- Armazenam nome, gênero e quantidade máxima de participantes.
- Funcionalidades relacionadas também são disponibilizadas no painel do professor.

### Encerrar sessão

**Arquivo:** `Site/View/Usuario/sair.php`

- Destrói a sessão PHP.
- Redireciona o usuário para a página inicial.

## Páginas do aplicativo MAUI

### Página inicial

**Arquivo:** `App/InterClass.App/InterClass.App/MainPage.xaml`

- Apresenta informações sobre o interclasse.
- Exibe a próxima partida cadastrada.
- Mostra dados da turma do usuário.
- Possui acesso à agenda, classificação, turma e conta.
- Mostra o nome do usuário quando ele está autenticado.

### Login

**Arquivo:** `App/InterClass.App/InterClass.App/Login.xaml`

- Valida um RM numérico.
- Verifica RM e senha no banco.
- Exibe erros de validação ou falha de conexão.
- Permite ir para o cadastro.
- Contém um acesso visual para administradores.

### Cadastro

**Arquivo:** `App/InterClass.App/InterClass.App/Cadastro.xaml`

- Permite que um usuário registrado crie a própria senha.
- Confirma que o RM existe no banco.
- Impede o cadastro de senha em um RM já ativado.
- Retorna à página inicial após o cadastro.

### Minha turma

**Arquivo:** `App/InterClass.App/InterClass.App/MinhaSala.xaml`

- Busca a sala relacionada ao usuário autenticado.
- Exibe nome da sala e quantidade de alunos.
- Contém área preparada para listar participantes e modalidades.
- Algumas informações exibidas ainda são estáticas ou aguardam dados adicionais do banco.

### Calendário

**Arquivo:** `App/InterClass.App/InterClass.App/Calendario.xaml`

- Tela cadastrada nas rotas do aplicativo.
- Atualmente contém o texto de protótipo do .NET MAUI e ainda não possui integração completa com os jogos.

### Classificação

**Arquivo:** `App/InterClass.App/InterClass.App/Classificacao.xaml`

- Tela cadastrada nas rotas do aplicativo.
- Atualmente contém o texto de protótipo do .NET MAUI e ainda não possui integração completa com o ranking.

### Conta do usuário

**Arquivo:** `App/InterClass.App/InterClass.App/Usuario.xaml`

- Permite alterar a senha do usuário autenticado.
- Valida se os dois campos da nova senha são iguais.
- Permite sair da conta.
- Retorna à página inicial após alteração ou logout.

## Arquitetura PHP

O site utiliza uma divisão simples em **View**, **Controller** e **Model**:

- `View` contém formulários e páginas HTML/PHP.
- `Controller` valida dados recebidos e organiza a resposta.
- `Model` executa consultas SQL por meio de PDO.

Principais controles:

- `UsuarioController` e `UsuarioModel`.
- `SalaController` e `SalaModel`.
- `ModalidadeC` e `ModalidadeM`.
- `TimeController` e `TimeModel`.
- `JogoController` e `JogoModel`.
- `AdministradorModel`.

## Sessões e perfis

O site usa `$_SESSION` para controlar o acesso:

| Perfil | Valor da sessão | Acesso |
|---|---|---|
| Professor | `tipo = ADM` | Painel administrativo e placar |
| Representante | `tipo = representante` | Painel da turma e cadastro de times/alunos |
| Aluno | `tipo = aluno` | Página pública e informações do próprio acesso |

Algumas páginas verificam diretamente a sessão antes de executar consultas ou exibir dados.

## Observações de segurança e manutenção

- Alterne o usuário e a senha do banco em ambientes de produção.
- Não exponha `Database/Service.cs` com credenciais reais em repositórios públicos.
- Senhas administradores e representantes são armazenadas com `password_hash`.
- Para uma versão pública, adicione HTTPS, políticas de sessão seguras e proteção CSRF a todos os formulários POST.
- O aplicativo MAUI usa consulta direta ao banco; em produção, uma API PHP seria uma separação mais segura.
- O aplicativo ainda contém telas em estado de protótipo e métodos legados no serviço de banco.

## Licença

Nenhuma licença foi definida neste repositório. Inclua uma licença antes de distribuir o projeto publicamente.