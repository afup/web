-- STORY
--  As the account manager
--  I want to add a comment on a line in journal
--  So that I can have more details about the line.

-- Add `comment` column in compta to keep comments on items :')
ALTER TABLE `compta`
  ADD COLUMN `comment` VARCHAR(255) DEFAULT NULL AFTER `description`;

-- STORY
--  As the account manager
--  I want to know if a line in compta requires a proof document
--  Because I want to be sure to give the correct export for the final accounting report.

-- Add `require_proof` column in compta to know which line requires a document as proof
ALTER TABLE `compta`
  ADD COLUMN `require_proof` TINYINT(1) DEFAULT 0 AFTER `idcompte`;
