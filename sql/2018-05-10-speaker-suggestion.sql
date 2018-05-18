CREATE TABLE `afup_speaker_suggestion` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `event_id` int(10) unsigned NOT NULL,
  `suggester_email` varchar(255) NOT NULL,
  `suggester_name` varchar(255) NOT NULL,
  `speaker_name` varchar(255) NOT NULL,
  `comment` TEXT DEFAULT NULL,
  `created_at` datetime NOT NULL
) COMMENT='' ENGINE='InnoDB';
