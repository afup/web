ALTER TABLE `afup_sessions`
  ADD `markdown` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `language_code`,
  COMMENT='';
