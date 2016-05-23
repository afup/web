-- STORY
--  As the account manager
--  I want to add a comment on a line in journal
--  So that I can have more details about the line.

-- Add `comment` column in compta to keep comments on items :')
ALTER TABLE `compta`
  ADD COLUMN `comment` VARCHAR(255) DEFAULT NULL AFTER `description`;

