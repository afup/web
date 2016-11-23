ALTER TABLE `afup_personnes_physiques`
  ADD `roles` varchar(255) COLLATE 'latin1_general_ci' NOT NULL AFTER `niveau_modules`,
  COMMENT='Personnes physiques';

CREATE TABLE `afup_personnes_morales_invitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `manager` tinyint(1) unsigned NOT NULL,
  `submitted_on` datetime NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `afup_cotisations`
  CHANGE `type_reglement` `type_reglement` tinyint(3) unsigned NULL DEFAULT '0' AFTER `montant`,
  COMMENT='Cotisation des personnes physiques et morales';

ALTER TABLE `afup_cotisations`
  ADD `token` varchar(255) COLLATE 'latin1_swedish_ci' NULL AFTER `commentaires`;

ALTER TABLE `afup_personnes_morales`
  ADD `max_members` tinyint(1) unsigned NULL COMMENT 'Nombre maximum de membre autorisé par la cotisation' AFTER `telephone_portable`,
  COMMENT='Personnes morales';

-- Update afup_personnes_morales pour calculer le nombre maximum de membre en fonction des membres actifs
UPDATE afup_personnes_morales apm SET apm.max_members = (SELECT CEIL(COUNT(app.id)/3)*3 FROM afup_personnes_physiques app WHERE app.id_personne_morale = apm.id AND app.etat = 1);

