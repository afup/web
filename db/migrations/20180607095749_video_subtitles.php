<?php

use Phinx\Migration\AbstractMigration;

class VideoSubtitles extends AbstractMigration
{
    public function change()
    {
$sql = <<<EOF
ALTER TABLE `afup_sessions`
  ADD `video_has_fr_subtitles` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `youtube_id`,
  ADD `video_has_en_subtitles` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `video_has_fr_subtitles`
;
EOF;
        $this->execute($sql);
    }
}
