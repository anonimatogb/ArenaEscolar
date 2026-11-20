-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 20/11/2026 às 21:09
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

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
  `id_salaA` int(11) NOT NULL,
  `id_salaB` int(11) NOT NULL,
  `dia` datetime NOT NULL,
  `ganhador` int(11) DEFAULT NULL,
  `quadra` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `jogo`
--

INSERT INTO `jogo` (`id`, `id_modalidade`, `id_salaA`, `id_salaB`, `dia`, `ganhador`, `quadra`) VALUES
(6, 1, 3, 9, '2026-11-11 15:44:00', NULL, '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `modalidade`
--

CREATE TABLE `modalidade` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `genero` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `modalidade`
--

INSERT INTO `modalidade` (`id`, `nome`, `quantidade`, `genero`) VALUES
(1, 'Futsal', 10, 'masculino'),
(2, 'Volei', 6, 'feminino'),
(3, 'Queimada', 20, 'masculino'),
(4, 'Judô', 2, 'masculino');

-- --------------------------------------------------------

--
-- Estrutura para tabela `placar`
--

CREATE TABLE `placar` (
  `id` int(11) NOT NULL,
  `id_jogo` int(11) NOT NULL,
  `pont_a` int(11) NOT NULL DEFAULT 0,
  `pont_b` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sala`
--

CREATE TABLE `sala` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `pontos` int(11) NOT NULL DEFAULT 0,
  `qtdtimes` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `sala`
--

INSERT INTO `sala` (`id`, `nome`, `pontos`, `qtdtimes`) VALUES
(1, 'Diretoria', 0, 0),
(2, '9° EF', 0, 0),
(3, '1° EM', 0, 0),
(4, '2° EM', 0, 0),
(5, '3° EM', 0, 0),
(7, '8° EF', 0, 2),
(8, '7° EF', 0, 2),
(9, '6° EF', 0, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `time`
--

CREATE TABLE `time` (
  `id` int(11) NOT NULL,
  `id_modalidade` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `time`
--

INSERT INTO `time` (`id`, `id_modalidade`, `id_sala`) VALUES
(10, 1, 5),
(9, 4, 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `time_aluno`
--

CREATE TABLE `time_aluno` (
  `id` int(11) NOT NULL,
  `id_time` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `time_aluno`
--

INSERT INTO `time_aluno` (`id`, `id_time`, `id_usuario`) VALUES
(14, 9, 18),
(13, 9, 22),
(23, 10, 14),
(21, 10, 15),
(20, 10, 16),
(16, 10, 18),
(18, 10, 19),
(22, 10, 20),
(17, 10, 21),
(15, 10, 22),
(24, 10, 23),
(19, 10, 24);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `rm` int(11) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `tipo` varchar(255) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `genero` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `rm`, `senha`, `tipo`, `id_sala`, `genero`) VALUES
(9, 'Gabriel Machado', NULL, '$2y$10$LYpLIp/o9T.EtlCbQv0QXOcXKQMdJ2Wpy0pTf52jHfdcernrF6BQa', 'admin', 1, NULL),
(10, 'Gabriel', 2147483647, '$2y$10$4tT1ssjBSmssoIIvyjr2gOhSc5uEc.GGDLdcFWanDd38XCLrLgWiK', 'representante', 5, NULL),
(11, 'Miguel', 123415, NULL, 'aluno', 5, 'masculino'),
(14, 'Luis', 563264, NULL, 'aluno', 5, 'masculino'),
(15, 'João', 63463467, NULL, 'aluno', 5, 'masculino'),
(16, 'Jhow', 7346346, NULL, 'aluno', 5, 'masculino'),
(17, 'Vitor', 12314, NULL, 'aluno', 5, 'masculino'),
(18, 'Dhiogo', 15512421, NULL, 'aluno', 5, 'masculino'),
(19, 'Fernando', 152421, NULL, 'aluno', 5, 'masculino'),
(20, 'Kayky', 51214124, NULL, 'aluno', 5, 'masculino'),
(21, 'Esdra', 253523, NULL, 'aluno', 5, 'masculino'),
(22, 'Arthur', 51315, NULL, 'aluno', 5, 'masculino'),
(23, 'Marcelo', 51351, NULL, 'aluno', 5, 'masculino'),
(24, 'Guilherme', 25325, NULL, 'aluno', 5, 'masculino'),
(26, 'Ana', 421421, NULL, 'aluno', 5, 'feminino'),
(27, 'Laura', 13123, NULL, 'aluno', 5, 'feminino'),
(28, 'Sarah', 321422, NULL, 'aluno', 5, 'feminino'),
(30, 'Samanta', 421232, NULL, 'aluno', 5, 'feminino');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `jogo`
--
ALTER TABLE `jogo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_jogo_modalidade` (`id_modalidade`),
  ADD KEY `fk_jogo_salaA` (`id_salaA`),
  ADD KEY `fk_jogo_salaB` (`id_salaB`),
  ADD KEY `fk_jogo_ganhado` (`ganhador`);

--
-- Índices de tabela `modalidade`
--
ALTER TABLE `modalidade`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `placar`
--
ALTER TABLE `placar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_jogo` (`id_jogo`);

--
-- Índices de tabela `sala`
--
ALTER TABLE `sala`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `time`
--
ALTER TABLE `time`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_modalidade_sala` (`id_modalidade`,`id_sala`),
  ADD KEY `fk_time_sala` (`id_sala`);

--
-- Índices de tabela `time_aluno`
--
ALTER TABLE `time_aluno`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `aluno_time` (`id_time`,`id_usuario`),
  ADD KEY `time_aluno_ibfk_2` (`id_usuario`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rm` (`rm`),
  ADD KEY `fk_usuario_sala` (`id_sala`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `jogo`
--
ALTER TABLE `jogo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `modalidade`
--
ALTER TABLE `modalidade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `time_aluno`
--
ALTER TABLE `time_aluno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `jogo`
--
ALTER TABLE `jogo`
  ADD CONSTRAINT `fk_jogo_ganhado` FOREIGN KEY (`ganhador`) REFERENCES `sala` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jogo_modalidade` FOREIGN KEY (`id_modalidade`) REFERENCES `modalidade` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jogo_salaA` FOREIGN KEY (`id_salaA`) REFERENCES `sala` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jogo_salaB` FOREIGN KEY (`id_salaB`) REFERENCES `sala` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Restrições para tabelas `time_aluno`
--
ALTER TABLE `time_aluno`
  ADD CONSTRAINT `time_aluno_ibfk_1` FOREIGN KEY (`id_time`) REFERENCES `time` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `time_aluno_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_sala` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
