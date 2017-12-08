ALTER TABLE `afup_inscription_forum`
ADD `id_member` int unsigned NULL AFTER `id_forum`,
ADD `member_type` int unsigned NULL AFTER `id_member`;

ALTER TABLE `afup_personnes_physiques`
ADD INDEX `email` (`email`);
