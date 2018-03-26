CREATE TABLE `afup_techletter_subscriptions` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int unsigned NOT NULL,
  `subscription_date` datetime NOT NULL
) COMMENT='' ENGINE='InnoDB';
