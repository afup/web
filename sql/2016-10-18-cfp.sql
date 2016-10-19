CREATE TABLE `afup_user_github` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `github_id` int(10) unsigned NOT NULL,
  `login` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `profile_url` varchar(255) NOT NULL,
  `avatar_url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `afup_sessions_vote_github` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(10) unsigned NOT NULL,
  `user` int(10) unsigned NOT NULL,
  `comment` text NOT NULL,
  `vote` tinyint(3) unsigned NOT NULL,
  `submitted_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `afup_conferenciers` ADD `user_github` int unsigned NOT NULL;
ALTER TABLE `afup_sessions_vote_github`
  CHANGE `comment` `comment` text COLLATE 'latin1_swedish_ci' NULL AFTER `user`;
ALTER TABLE `afup_conferenciers`
  CHANGE `twitter` `twitter` varchar(255) COLLATE 'latin1_swedish_ci' NULL AFTER `biographie`;