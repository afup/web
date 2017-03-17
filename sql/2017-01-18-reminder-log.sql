CREATE TABLE `afup_subscription_reminder_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int unsigned NOT NULL,
  `user_type` tinyint unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `reminder_key` varchar(30) NOT NULL,
  `reminder_date` datetime NOT NULL,
  `mail_sent` tinyint unsigned NOT NULL
) COMMENT='' ENGINE='InnoDB';
