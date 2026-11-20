
CREATE DATABASE IF NOT EXISTS interclass
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE interclass;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================
-- TABELA SALA
-- =====================================

CREATE TABLE sala (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    pontos INT NOT NULL DEFAULT 0,
    qtdtimes INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO sala (id, nome, pontos, qtdtimes) VALUES
(1, 'Diretoria', 0, 0),
(2, '9° ano', 0, 0),
(3, '1° EM', 0, 0),
(4, '2° EM', 0, 0),
(5, '3° EM', 0, 0),
(7, '8° EF', 0, 2),
(8, '7° EF', 0, 2),
(9, '6° EF', 0, 2);


-- =====================================
-- TABELA MODALIDADE
-- =====================================

CREATE TABLE modalidade (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    quantidade INT NOT NULL,
    genero VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO modalidade (id, nome, quantidade, genero) VALUES
(1, 'Futsal', 10, 'Masc'),
(2, 'Volei', 6, 'Fem'),
(3, 'Queimada', 20, 'Masc');


-- =====================================
-- TABELA USUARIO
-- =====================================

CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    rm INT NOT NULL UNIQUE,
    senha VARCHAR(255) NULL,
    tipo VARCHAR(255) NOT NULL,
    id_sala INT NOT NULL,

    CONSTRAINT fk_usuario_sala
    FOREIGN KEY (id_sala) REFERENCES sala(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO usuario (id, nome, rm, senha, tipo, id_sala) VALUES
(9, 'Gabriel Machado', 0,
'$2y$10$LYpLIp/o9T.EtlCbQv0QXOcXKQMdJ2Wpy0pTf52jHfdcernrF6BQa',
'admin', 1);


-- =====================================
-- TABELA TIME
-- =====================================

CREATE TABLE `time` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_modalidade INT NOT NULL,
    id_sala INT NOT NULL,
    nome_array VARCHAR(255) NOT NULL,

    CONSTRAINT fk_time_modalidade
    FOREIGN KEY (id_modalidade) REFERENCES modalidade(id),

    CONSTRAINT fk_time_sala
    FOREIGN KEY (id_sala) REFERENCES sala(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `time` (id, id_modalidade, id_sala, nome_array) VALUES
(1, 1, 7, '["Time 1° A - Futsal"]'),
(2, 1, 8, '["Time 2° B - Futsal"]'),
(3, 1, 9, '["Time 3° C - Futsal"]'),
(4, 2, 7, '["Time 1° A - Vôlei"]'),
(5, 2, 8, '["Time 2° B - Vôlei"]'),
(6, 2, 9, '["Time 3° C - Vôlei"]');


-- =====================================
-- TABELA JOGO
-- =====================================

CREATE TABLE jogo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_modalidade INT NOT NULL,
    id_salaA INT NOT NULL,
    id_salaB INT NOT NULL,
    dia DATE NOT NULL,
    horario TIME NULL,
    id_timeA INT NULL,
    id_timeB INT NULL,
    ganhador VARCHAR(255) NOT NULL DEFAULT 'A definir',
    quadra VARCHAR(255) NOT NULL,

    INDEX fk_jogo_modalidade (id_modalidade),
    INDEX fk_jogo_salaA (id_salaA),
    INDEX fk_jogo_salaB (id_salaB),
    INDEX fk_jogo_timeA (id_timeA),
    INDEX fk_jogo_timeB (id_timeB),

    CONSTRAINT fk_jogo_modalidade
    FOREIGN KEY (id_modalidade) REFERENCES modalidade(id),

    CONSTRAINT fk_jogo_salaA
    FOREIGN KEY (id_salaA) REFERENCES sala(id),

    CONSTRAINT fk_jogo_salaB
    FOREIGN KEY (id_salaB) REFERENCES sala(id),

    CONSTRAINT fk_jogo_timeA
    FOREIGN KEY (id_timeA) REFERENCES `time`(id),

    CONSTRAINT fk_jogo_timeB
    FOREIGN KEY (id_timeB) REFERENCES `time`(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO jogo
(id, id_modalidade, id_salaA, id_salaB, dia, horario,
 id_timeA, id_timeB, ganhador, quadra)
VALUES
(1, 1, 7, 8, '2026-12-01', NULL, 1, 2, 'A definir', 'Quadra Coberta'),
(2, 2, 8, 9, '2026-12-02', NULL, 5, 6, 'A definir', 'Ginásio'),
(3, 1, 9, 7, '2026-12-03', NULL, 3, 1, 'A definir', 'Quadra Coberta'),
(4, 2, 7, 8, '2026-12-04', NULL, 4, 5, 'A definir', 'Ginásio'),
(5, 1, 8, 9, '2026-12-05', NULL, 2, 3, 'A definir', 'Quadra Coberta');


-- =====================================
-- TABELA PLACAR
-- =====================================

CREATE TABLE placar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_jogo INT NOT NULL UNIQUE,
    pont_a INT NOT NULL DEFAULT 0,
    pont_b INT NOT NULL DEFAULT 0,

    CONSTRAINT fk_placar_jogo
    FOREIGN KEY (id_jogo) REFERENCES jogo(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================
-- FINALIZACAO
-- =====================================

ALTER TABLE sala AUTO_INCREMENT = 10;
ALTER TABLE modalidade AUTO_INCREMENT = 4;
ALTER TABLE usuario AUTO_INCREMENT = 10;
ALTER TABLE `time` AUTO_INCREMENT = 7;
ALTER TABLE jogo AUTO_INCREMENT = 6;

SET FOREIGN_KEY_CHECKS = 1;

COMMIT;
