ALTER TABLE `afup_sessions`
ADD `youtube_id` varchar(30) DEFAULT NULL AFTER `needs_mentoring`,
ADD `slides_url` varchar(255) DEFAULT NULL AFTER `youtube_id`,
ADD `blog_post_url` varchar(255) DEFAULT NULL AFTER `slides_url`,
COMMENT='';
