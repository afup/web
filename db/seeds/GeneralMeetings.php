<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class GeneralMeetings extends AbstractSeed
{
    public function run(): void
    {
        $dir = 'htdocs/uploads/general_meetings_reports/';
        if (!is_dir($dir) && (!mkdir($dir) && !is_dir($dir))) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        copy('tests/behat/files/test_file1.pdf', $dir . '2014-02-15_CR AG AFUP 2013-2014.pdf');
        copy('tests/behat/files/test_file2.pdf', $dir . '2013-01-30_CR AG AFUP 2012-2013.pdf');

        $timestamp = strtotime(date("Y-m-d") . "+2 months");

        // Assemblées générales
        $data = [
            [
                'date' => 1635544800,
                'description' => 'Assemblée octobre 2021',
            ],
            [
                'date' => $timestamp,
                'description' => 'Assemblée dans 2 mois',
            ],
        ];

        $table = $this->table('afup_assemblee_generale');
        $table->truncate();
        $table
            ->insert($data)
            ->save()
        ;

        // Présences Assemblées générales
        $data = [
            [
                'id_personne_physique' => '1',
                'date' => 1635544800,
                'presence' => 1,
            ],
            [
                'id_personne_physique' => '1',
                'date' => $timestamp,
                'presence' => 1,
            ],
        ];

        $table = $this->table('afup_presences_assemblee_generale');
        $table->truncate();
        $table
            ->insert($data)
            ->save()
        ;


        // Assemblées générales Questions
        $data = [
            [
                'date' => $timestamp,
                'label' => 'Une 1ère question. Alors d\'accord ?',
                'created_at' => '2021-09-01 10:42:42',
            ],
            [
                'date' => $timestamp,
                'label' => 'Une autre question pertinente. On vote ?',
                'created_at' => '2021-09-12 10:42:42',
            ],
        ];

        $table = $this->table('afup_assemblee_generale_question');
        $table->getAdapter()->execute('SET FOREIGN_KEY_CHECKS=0;');
        $table->truncate();
        $table->getAdapter()->execute('SET FOREIGN_KEY_CHECKS=1;');
        $table
            ->insert($data)
            ->save()
        ;
    }
}
