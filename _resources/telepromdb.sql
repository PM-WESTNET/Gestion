create database telepromdb;

CREATE TABLE campanas
(
    Id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(20) NOT NULL,
    Descripcion VARCHAR(50) NOT NULL,
    PromedioHabla INT(11) NOT NULL,
    TMaxHabla INT(11) NOT NULL,
    TMaxTipeo INT(11) NOT NULL,
    IdFormulario INT(11) NOT NULL,
    RangoHorario BLOB,
    IdArbolE INT(11) NOT NULL,
    IdArbolS INT(11) NOT NULL,
    IdScriptE INT(11) NOT NULL,
    IdScriptS INT(11) NOT NULL,
    Estado CHAR(20) NOT NULL COMMENT 'Activa o no',
    IdHabilidad INT(11) NOT NULL,
    secdiscado INT(11) DEFAULT '0' NOT NULL COMMENT 'Secuencia de seleccion de los registros de las bases "0" secuencial entre bases, "1" un reg de cada base',
    ultimadiscada INT(11) DEFAULT '-1' NOT NULL COMMENT 'Ultima base de la campaña que se estubo marcando',
    reintentos VARCHAR(53) DEFAULT '01100-01100-01100-01100-01100-01100-00000-00000-00000' NOT NULL COMMENT 'ImposibleConectar/Ocupado/SinTono/NoContesta/Fax/Contestador/DiferidoFallido/Desconectadas/Abandonadas
Reintentos(1/99)minutos(1/999) 4 digitos separados por -
3009 => tres reintentos cada 9 minutos',
    fechainicio DATETIME,
    fechafin DATETIME,
    deleted TINYINT(1),
    predictiva TINYINT(1),
    habilitacionentrante TINYINT(1) DEFAULT '0' NOT NULL,
    maxlin INT(11) DEFAULT '-1' NOT NULL,
    colaentrantes VARCHAR(20),
    minlinentrantes INT(11) DEFAULT '1',
    tmaxatender INT(11) NOT NULL,
    criterioacd INT(11) NOT NULL,
    criteriooperdiferido INT(11) NOT NULL,
    mododecontacto VARCHAR(50) DEFAULT '3',
    autoatender TINYINT(1) DEFAULT '0' NOT NULL,
    predicQLlamadas INT(11) DEFAULT '1',
    predicTLlamadas INT(11) DEFAULT '1',
    predicDefLlamadas INT(11) DEFAULT '60',
    predicDefTipeo INT(11) DEFAULT '10',
    predicManual INT(11) DEFAULT '0',
    tfinestimadoexedido INT(11) DEFAULT '-1' NOT NULL,
    discadonumunico TINYINT(1) DEFAULT '0' NOT NULL,
    idsms INT(11) DEFAULT '0',
    idGrupoGSM INT(11) DEFAULT '0' COMMENT 'Grupo de equipos GSM',
    abandonomaximo INT(11) DEFAULT '100' COMMENT 'Sin limite de abadono',
    cantllamadasmanual INT(11) DEFAULT '1' COMMENT '-1 desactivado',
    automcomoatendidas TINYINT(1) DEFAULT '0' COMMENT 'considerar atendidas autom como atendidas',
    NoContPorcentaje INT(11) DEFAULT '0',
    NoContCantAEvaluar INT(11) DEFAULT '0',
    diferiragenteespecifico TINYINT(1) DEFAULT '-1' NOT NULL,
    maxvistaprevia INT(11) DEFAULT '0' NOT NULL
);
CREATE INDEX campana_form ON campanas (IdFormulario);

CREATE TABLE mensajescombinado
(
    id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    mensaje VARCHAR(300) NOT NULL,
    telefono VARCHAR(100) NOT NULL,
    idcontacto INT(11),
    idlogllamado INT(11) NOT NULL,
    rediscado DATETIME,
    estado VARCHAR(20),
    idcampana INT(11) NOT NULL,
    tipomensaje VARCHAR(50) NOT NULL,
    imeiEquipo_gsm VARCHAR(15) DEFAULT '0' NOT NULL
);