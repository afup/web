CREATE TABLE `afup_techletter_unsubscriptions` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `email` varchar(255) NOT NULL,
  `unsubscription_date` datetime NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `mailchimp_id` varchar(255) DEFAULT NULL
) COMMENT='' ENGINE='InnoDB';
