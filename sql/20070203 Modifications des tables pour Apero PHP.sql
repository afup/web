DROP TABLE IF EXISTS `afup_aperos`, `afup_aperos_messages`, `afup_aperos_phpautes`, 
                     `afup_phpnautes`, `afup_phpnautes_temp`, `afup_phpnautes_ville`;

CREATE TABLE `afup_aperos` (
    `id`             INT         NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `date`           DATETIME    NOT NULL ,
    `lieu`           VARCHAR(70) NOT NULL ,
    `id_ville`       INT         NOT NULL ,
    `id_responsable` INT         NOT NULL ,
    `publier`        TINYINT(1)  NOT NULL DEFAULT '0',
    `annuler`        TINYINT(1)  NOT NULL DEFAULT '0'
) ENGINE = innodb;

CREATE TABLE `afup_aperos_inscrits` (
    `id`            INT          NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `date`          DATE         NOT NULL ,
    `login`         VARCHAR(20)  NOT NULL ,
    `mot_de_passe`  VARCHAR(32)  NOT NULL ,
    `nom`           VARCHAR(70)  NOT NULL ,
    `prenom`        VARCHAR(70)  NOT NULL ,
    `email`         VARCHAR(150) NOT NULL ,
    `site_internet` VARCHAR(150) NOT NULL ,
    `id_ville`      INT          NOT NULL ,
    `valider`       TINYINT(1)   NOT NULL default '0',
    UNIQUE (`login` , `email`)
) ENGINE = innodb;

CREATE TABLE `afup_aperos_participants` (
    `id_apero`    INT(11)  NOT NULL,
    `id_inscript` INT(11)  NOT NULL,
    `date`        DATETIME NOT NULL,
    PRIMARY KEY (`id_apero`, `id_inscript`)
) ENGINE = innodb;
