-- Esquema do Arena Escolar alinhado ao modelo relacional do prototipo.
-- O RM e opcional para ADM e unico quando informado.
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "-03:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `interclass`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `interclass`;

CREATE TABLE `sala` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `pontos` int(11) NOT NULL DEFAULT 0,
  `qtdtimes` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sala_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `rm` varchar(30) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `tipo` enum('admin','representante','aluno') NOT NULL DEFAULT 'aluno',
  `id_sala` int(11) NOT NULL,
  `genero` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuario_rm` (`rm`),
  KEY `fk_usuario_sala` (`id_sala`),
  CONSTRAINT `fk_usuario_sala` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `modalidade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `genero` varchar(30) NOT NULL,
  `max_jogadores` int(11) NOT NULL DEFAULT 10,
  `pts_vitoria` int(11) NOT NULL DEFAULT 3,
  `pts_empate` int(11) NOT NULL DEFAULT 1,
  `pts_derrota` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_modalidade_genero` (`nome`, `genero`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_sala` int(11) NOT NULL,
  `id_modalidade` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_time_sala_modalidade` (`id_sala`, `id_modalidade`),
  KEY `fk_time_modalidade` (`id_modalidade`),
  CONSTRAINT `fk_time_sala` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_time_modalidade` FOREIGN KEY (`id_modalidade`) REFERENCES `modalidade` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `time_jogador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_time` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_time_usuario` (`id_time`, `id_usuario`),
  KEY `fk_tj_usuario` (`id_usuario`),
  CONSTRAINT `fk_tj_time` FOREIGN KEY (`id_time`) REFERENCES `time` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tj_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jogo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_modalidade` int(11) NOT NULL,
  `id_timeA` int(11) NOT NULL,
  `id_timeB` int(11) NOT NULL,
  `dia` date NOT NULL,
  `horario` time DEFAULT NULL,
  `quadra` varchar(100) NOT NULL,
  `status` enum('agendado','finalizado') NOT NULL DEFAULT 'agendado',
  `id_time_vencedor` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_jogo_modalidade` (`id_modalidade`),
  KEY `fk_jogo_timeA` (`id_timeA`),
  KEY `fk_jogo_timeB` (`id_timeB`),
  KEY `fk_jogo_vencedor` (`id_time_vencedor`),
  CONSTRAINT `fk_jogo_modalidade` FOREIGN KEY (`id_modalidade`) REFERENCES `modalidade` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_jogo_timeA` FOREIGN KEY (`id_timeA`) REFERENCES `time` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_jogo_timeB` FOREIGN KEY (`id_timeB`) REFERENCES `time` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_jogo_vencedor` FOREIGN KEY (`id_time_vencedor`) REFERENCES `time` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `placar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_jogo` int(11) NOT NULL,
  `gols_pts_a` int(11) NOT NULL DEFAULT 0,
  `gols_pts_b` int(11) NOT NULL DEFAULT 0,
  `pontos_sala_a` int(11) NOT NULL DEFAULT 0,
  `pontos_sala_b` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_placar_jogo` (`id_jogo`),
  CONSTRAINT `fk_placar_jogo` FOREIGN KEY (`id_jogo`) REFERENCES `jogo` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sala` (`id`, `nome`) VALUES
(1, 'Diretoria'),
(2, '9° EF'),
(3, '1° EM'),
(4, '2° EM'),
(5, '3° EM'),
(7, '8° EF'),
(8, '7° EF'),
(9, '6° EF');

INSERT INTO `usuario` (`id`, `nome`, `rm`, `senha`, `tipo`, `id_sala`, `genero`) VALUES
(9, 'Gabriel Machado', NULL, '$2y$10$LYpLIp/o9T.EtlCbQv0QXOcXKQMdJ2Wpy0pTf52jHfdcernrF6BQa', 'admin', 1, NULL),
(10, 'Gabriel', '2147483647', '$2y$10$4tT1ssjBSmssoIIvyjr2gOhSc5uEc.GGDLdcFWanDd38XCLrLgWiK', 'representante', 5, NULL),
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
(28, 'Sarah', '321422', NULL, 'aluno', 5, 'feminino'),
(30, 'Samanta', '421232', NULL, 'aluno', 5, 'feminino');

INSERT INTO `modalidade` (`id`, `nome`, `genero`, `max_jogadores`) VALUES
(1, 'Futsal', 'Masculino', 10),
(2, 'Volei', 'Feminino', 6),
(3, 'Queimada', 'Masculino', 10);

INSERT INTO `time` (`id`, `id_modalidade`, `id_sala`) VALUES
(9, 4, 5),
(10, 1, 5),
(11, 1, 3),
(12, 1, 9);

INSERT INTO `time_jogador` (`id`, `id_time`, `id_usuario`) VALUES
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


ALTER TABLE `sala` AUTO_INCREMENT = 10;
ALTER TABLE `usuario` AUTO_INCREMENT = 31;
ALTER TABLE `modalidade` AUTO_INCREMENT = 5;
ALTER TABLE `time` AUTO_INCREMENT = 13;
ALTER TABLE `time_jogador` AUTO_INCREMENT = 25;
ALTER TABLE `jogo` AUTO_INCREMENT = 7;
