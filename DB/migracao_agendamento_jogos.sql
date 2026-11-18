-- Permite agendar confrontos por sala, modalidade, dia e horário.
-- As equipes podem ser vinculadas depois, quando forem cadastradas.
ALTER TABLE jogo
    ADD COLUMN horario TIME NULL AFTER dia,
    MODIFY COLUMN id_timeA INT(11) NULL,
    MODIFY COLUMN id_timeB INT(11) NULL;
