<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPhotoUrlToMeetupTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('afup_meetup')
            ->addColumn('photo_url', 'string', [
                'null' => true,
            ])
            ->update();
    }
}
