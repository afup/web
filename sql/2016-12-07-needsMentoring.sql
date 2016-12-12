ALTER TABLE `afup_sessions`
ADD `needs_mentoring` tinyint(1) NOT NULL DEFAULT 0 AFTER `plannifie`,
COMMENT='';
