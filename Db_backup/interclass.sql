-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25/11/2026 às 02:23
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `interclass`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `jogo`
--

CREATE TABLE `jogo` (
  `id` int(11) NOT NULL,
  `id_modalidade` int(11) NOT NULL,
  `id_timeA` int(11) NOT NULL,
  `id_timeB` int(11) NOT NULL,
  `dia` date NOT NULL,
  `horario` time DEFAULT NULL,
  `quadra` varchar(100) NOT NULL,
  `status` enum('agendado','finalizado') NOT NULL DEFAULT 'agendado',
  `id_time_vencedor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `jogo`
--

INSERT INTO `jogo` (`id`, `id_modalidade`, `id_timeA`, `id_timeB`, `dia`, `horario`, `quadra`, `status`, `id_time_vencedor`) VALUES
(1, 1, 3, 4, '2026-11-25', '11:11:00', 'Campo', 'agendado', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `modalidade`
--

CREATE TABLE `modalidade` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `genero` varchar(30) NOT NULL,
  `max_jogadores` int(11) NOT NULL DEFAULT 10,
  `pts_vitoria` int(11) NOT NULL DEFAULT 3,
  `pts_empate` int(11) NOT NULL DEFAULT 1,
  `pts_derrota` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `modalidade`
--

INSERT INTO `modalidade` (`id`, `nome`, `genero`, `max_jogadores`, `pts_vitoria`, `pts_empate`, `pts_derrota`) VALUES
(1, 'Futsal', 'Masculino', 10, 3, 1, 0),
(2, 'Volei', 'Feminino', 6, 3, 1, 0),
(3, 'Queimada', 'Misto', 10, 3, 1, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `placar`
--

CREATE TABLE `placar` (
  `id` int(11) NOT NULL,
  `id_jogo` int(11) NOT NULL,
  `gols_pts_a` int(11) NOT NULL DEFAULT 0,
  `gols_pts_b` int(11) NOT NULL DEFAULT 0,
  `pontos_sala_a` int(11) NOT NULL DEFAULT 0,
  `pontos_sala_b` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sala`
--

CREATE TABLE `sala` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `pontos` int(11) NOT NULL DEFAULT 0,
  `qtdtimes` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `sala`
--

INSERT INTO `sala` (`id`, `nome`, `pontos`, `qtdtimes`) VALUES
(1, 'Diretoria', 0, 0),
(2, '9° EF', 0, 0),
(3, '1° EM', 0, 0),
(4, '2° EM', 0, 0),
(5, '3° EM', 0, 0),
(7, '8° EF', 0, 0),
(8, '7° EF', 0, 0),
(9, '6° EF', 0, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `time`
--

CREATE TABLE `time` (
  `id` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `id_modalidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `time`
--

INSERT INTO `time` (`id`, `id_sala`, `id_modalidade`) VALUES
(4, 4, 1),
(2, 4, 2),
(3, 5, 1),
(6, 5, 2),
(5, 5, 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `time_jogador`
--

CREATE TABLE `time_jogador` (
  `id` int(11) NOT NULL,
  `id_time` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `rm` varchar(30) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `tipo` enum('admin','representante','aluno') NOT NULL DEFAULT 'aluno',
  `id_sala` int(11) NOT NULL,
  `genero` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `rm`, `senha`, `tipo`, `id_sala`, `genero`) VALUES
(6, 'Vitor Cardoso', NULL, '$2y$10$iKYvwr5yBlyO0guR70eUoOU/5pw5WQsmvWkwEJfNK0FZcwcmtLPWK', 'admin', 1, NULL),
(7, 'Gabriel Machado', NULL, '$2y$10$LYpLIp/o9T.EtlCbQv0QXOcXKQMdJ2Wpy0pTf52jHfdcernrF6BQa', 'admin', 1, NULL),
(11, 'Miguel', '123415', NULL, 'aluno', 5, 'masculino'),
(14, 'Luis', '563264', NULL, 'aluno', 5, 'masculino'),
(15, 'João', '63463467', NULL, 'aluno', 5, 'masculino'),
(16, 'Jhow', '7346346', NULL, 'aluno', 5, 'masculino'),
(17, 'Vitor', '12314', NULL, 'aluno', 5, 'masculino'),
(18, 'Dhiogo', '15512421', NULL, 'aluno', 5, 'masculino'),
(19, 'Fernando', '152421', NULL, 'aluno', 5, 'masculino'),
(20, 'Kayky', '51214124', NULL, 'aluno', 5, 'masculino'),
(21, 'Esdra', '253523', NULL, 'aluno', 5, 'masculino'),
(22, 'Arthur', '51315', NULL, 'aluno', 5, 'masculino'),
(23, 'Marcela', '51351', NULL, 'aluno', 5, 'feminino'),
(24, 'Guilherma', '25325', NULL, 'aluno', 5, 'feminino'),
(26, 'Ana', '421421', NULL, 'aluno', 5, 'feminino'),
(27, 'Laura', '13123', NULL, 'aluno', 5, 'feminino'),
(28, 'Amanda', '616', '$2y$10$nwMcJVHjkdDP4helGVZesOoB3752AzkaZalsLc408fPgkPO/Gu5wa', 'representante', 5, 'feminino'),
(30, 'Samanta', '421232', NULL, 'aluno', 5, 'feminino'),
(34, 'Lucas', '781234', NULL, 'aluno', 4, 'masculino'),
(35, 'Matheus', '781235', NULL, 'aluno', 4, 'masculino'),
(36, 'Rafael', '781236', NULL, 'aluno', 4, 'masculino'),
(37, 'Pedro', '781237', NULL, 'aluno', 4, 'masculino'),
(38, 'Gustavo', '781238', NULL, 'aluno', 4, 'masculino'),
(39, 'Enzo', '781239', NULL, 'aluno', 4, 'masculino'),
(40, 'Bruno', '781240', NULL, 'aluno', 4, 'masculino'),
(41, 'Thiago', '781241', NULL, 'aluno', 4, 'masculino'),
(42, 'Felipe', '781242', NULL, 'aluno', 4, 'masculino'),
(43, 'Cauã', '781243', NULL, 'aluno', 4, 'masculino'),
(44, 'Mariana', '781244', NULL, 'aluno', 4, 'feminino'),
(45, 'Beatriz', '781245', NULL, 'aluno', 4, 'feminino'),
(46, 'Camila', '999', '$2y$10$bKVr1R8QNGg6wMHlSIdL1OgI/qcYRwOW.Wxydb7VwB5icqch1NJKe', 'representante', 4, 'feminino'),
(47, 'Júlia', '781247', NULL, 'aluno', 4, 'feminino'),
(48, 'Larissa', '781248', NULL, 'aluno', 4, 'feminino'),
(49, 'Lorena', '3553', NULL, 'aluno', 4, 'feminino'),
(51, 'banana', '334', NULL, 'aluno', 4, 'feminino');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `jogo`
--
ALTER TABLE `jogo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_jogo_modalidade` (`id_modalidade`),
  ADD KEY `fk_jogo_timeA` (`id_timeA`),
  ADD KEY `fk_jogo_timeB` (`id_timeB`),
  ADD KEY `fk_jogo_vencedor` (`id_time_vencedor`);

--
-- Índices de tabela `modalidade`
--
ALTER TABLE `modalidade`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_modalidade_genero` (`nome`,`genero`);

--
-- Índices de tabela `placar`
--
ALTER TABLE `placar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_placar_jogo` (`id_jogo`);

--
-- Índices de tabela `sala`
--
ALTER TABLE `sala`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_sala_nome` (`nome`);

--
-- Índices de tabela `time`
--
ALTER TABLE `time`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_time_sala_modalidade` (`id_sala`,`id_modalidade`),
  ADD KEY `fk_time_modalidade` (`id_modalidade`);

--
-- Índices de tabela `time_jogador`
--
ALTER TABLE `time_jogador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_time_usuario` (`id_time`,`id_usuario`),
  ADD KEY `fk_tj_usuario` (`id_usuario`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_usuario_rm` (`rm`),
  ADD KEY `fk_usuario_sala` (`id_sala`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `jogo`
--
ALTER TABLE `jogo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `modalidade`
--
ALTER TABLE `modalidade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `placar`
--
ALTER TABLE `placar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `sala`
--
ALTER TABLE `sala`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `time`
--
ALTER TABLE `time`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `time_jogador`
--
ALTER TABLE `time_jogador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `jogo`
--
ALTER TABLE `jogo`
  ADD CONSTRAINT `fk_jogo_modalidade` FOREIGN KEY (`id_modalidade`) REFERENCES `modalidade` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jogo_timeA` FOREIGN KEY (`id_timeA`) REFERENCES `time` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jogo_timeB` FOREIGN KEY (`id_timeB`) REFERENCES `time` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jogo_vencedor` FOREIGN KEY (`id_time_vencedor`) REFERENCES `time` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `placar`
--
ALTER TABLE `placar`
  ADD CONSTRAINT `fk_placar_jogo` FOREIGN KEY (`id_jogo`) REFERENCES `jogo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `time`
--
ALTER TABLE `time`
  ADD CONSTRAINT `fk_time_modalidade` FOREIGN KEY (`id_modalidade`) REFERENCES `modalidade` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_time_sala` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `time_jogador`
--
ALTER TABLE `time_jogador`
  ADD CONSTRAINT `fk_tj_time` FOREIGN KEY (`id_time`) REFERENCES `time` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tj_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_sala` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
