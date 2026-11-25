-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25/11/2026 às 02:21
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
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `jogo`
--
ALTER TABLE `jogo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
