ALTER TABLE `afup_user_github`
  CHANGE `company` `company` varchar(255) COLLATE 'latin1_swedish_ci' NULL AFTER `name`,
  COMMENT='';

ALTER TABLE `afup_forum`
  ADD `text` text NOT NULL AFTER `annee`,
  COMMENT='';

ALTER TABLE `afup_forum`
  CHANGE `text` `text` text COLLATE 'latin1_swedish_ci' NULL AFTER `annee`;