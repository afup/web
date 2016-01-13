-- STORY
--  As the account manager
--  I want to tell that a line in `compta` doesn't require any attachment
--  So that I can have a quick look onto which lines require one.

-- Add `attachment_required` column in compta
ALTER TABLE `compta`
  ADD COLUMN `attachment_required` TINYINT(1) DEFAULT 0 AFTER `comment`;

-- STORY
--  As the account manager
--  I want to know what attachment filename is linked to a line in `compta`
--  Because I need to export a complete and structured summary sheet.

-- Add `attachment_filename` column in compta
ALTER TABLE `compta`
  ADD COLUMN `attachment_filename` VARCHAR(255) NULL DEFAULT NULL AFTER `attachment_required`;
