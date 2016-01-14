-- STORY
--  As the account manager
--  I want to know who came at the events
--  So that I can send "presence confirmation" emails when asked.

-- Add `presence` columns in the registrations table
ALTER TABLE `afup_inscription_forum`
  ADD `presence_day1` TINYINT(1) NULL DEFAULT NULL;
ALTER TABLE `afup_inscription_forum`
  ADD `presence_day2` TINYINT(1) NULL DEFAULT NULL;
