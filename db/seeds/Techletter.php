<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class Techletter extends AbstractSeed
{
    public function run(): void
    {
        $table = $this->table('afup_techletter');
        $table->truncate();

        $example1 = <<<EOF
{"firstNews":null,"secondNews":null,"articles":[{"url":"https:\/\/kevinsmith.io\/modern-php-without-a-framework","title":"Modern PHP Without a Framework | Kevin Smith","host":"kevinsmith.io","readingTime":"4","excerpt":"I've got a challenge for you. The next time you start a new project, try *not* using a PHP framework."},{"url":"https:\/\/blog.frankdejonge.nl\/being-in-control-of-time-in-php\/","title":"Being in control of time in PHP","host":"blog.frankdejonge.nl","readingTime":"7","excerpt":"Handling date and time in PHP using clocks for improved control and better testing."},{"url":"https:\/\/medium.com\/ifixit-engineering\/functional-programming-with-php-generators-837a6c91b0e3","title":"Functional Programming with PHP Generators \u2013 iFixit Engineering","host":"medium.com","readingTime":"10","excerpt":"Generators are cool. They make it easy to write iterators by defining a function instead of building an entire class that implements Iterator. They also make it easy to write lazy lists and infinite streams..."},{"url":"https:\/\/dev.to\/brpaz\/lets-all-stop-the-language-wars-and-focus-more-on-building-great-products-2nj6","title":"Lets all stop the language wars and focus more on building great products","host":"dev.to","readingTime":"8","excerpt":"We as developers, have the opportunity to change the world and improve people lives using technology, shouldn't we support everyone that is building something that will be useful for someone, instead of bashing them just because they are using an \u201cinferior\u201d language?"},{"url":"https:\/\/matthiasnoback.nl\/2018\/03\/ormless-a-memento-like-pattern-for-object-persistence\/","title":"ORMless; a Memento-like pattern for object persistence - Matthias Noback","host":"matthiasnoback.nl","readingTime":"12","excerpt":"Follow aggregate design rules. Implement a method for extracting state. Implement a method for restoring the object, based on its state. Implement a repository for dealing with state."}],"projects":[{"url":"https:\/\/github.com\/z7zmey\/php-parser","name":"z7zmey\/php-parser","description":"A Parser for PHP written in Go"},{"url":"https:\/\/github.com\/dunglas\/panthere","name":"dunglas\/panthere","description":"A browser testing and web crawling library for PHP and Symfony"},{"url":"https:\/\/github.com\/san-kumar\/lambdaphp","name":"san-kumar\/lambdaphp","description":"Quick and Dirty PHP website hosting using Aws Lambda"},{"url":"https:\/\/github.com\/NoiseByNorthwest\/php-spx","name":"NoiseByNorthwest\/php-spx","description":"A simple & straight-to-the-point PHP profiling extension with its built-in web UI"}]}
EOF;

        $example2 = <<<EOF
{"firstNews":null,"secondNews":null,"articles":[{"url":"https:\/\/www.tomasvotruba.cz\/blog\/2018\/04\/09\/try-psr-12-on-your-code-today\/","title":"Try PSR-12 on Your Code Today","host":"tomasvotruba.cz","readingTime":"4","excerpt":"The standard is still behind the door, but feedback, before it gets accepted, is very important. After accepting it will be written down and it will be difficult to change anything. ","language":null},{"url":"https:\/\/laravel-news.com\/eloquent-tips-tricks","title":" 20 Laravel Eloquent Tips and Tricks","host":"laravel-news.com","readingTime":"7","excerpt":"Eloquent ORM seems like a simple mechanism, but under the hood, there\u2019s a lot of semi-hidden functions and less-known ways to achieve more with it. In this article, I will show you a few tricks.","language":null},{"url":"https:\/\/www.grafikart.fr\/tutoriels\/php\/php-langage-merde-1001","title":"Tutoriel Vid\u00e9o PHP - PHP \"c'est de la merde\"","host":"grafikart.fr","readingTime":"14","excerpt":"M\u00eame si PHP est un langage qui est extr\u00eamement r\u00e9pandu, il est souvent la source de critiques et de moqueries sur internet. Il suffit de se rendre sur certaines communaut\u00e9s de d\u00e9veloppeurs pour s'en rendre compte. Aussi, je vous propose aujourd'hui de nous attarder sur les critiques faites \u00e0 l'\u00e9gard de PHP afin de mieux les comprendre.","language":"fr"},{"url":"https:\/\/wpmarmite.com\/gutenberg-wordpress\/","title":"Gutenberg arrive dans WordPress 5.0\u2026 et tout va bien se passer","host":"wpmarmite.com","readingTime":"18","excerpt":"Au WordCamp Europe 2017, la communaut\u00e9 WordPress a frissonn\u00e9. Matt Mullenweg, Le co-fondateur du CMS, a pr\u00e9sent\u00e9 en grande pompe ce qu\u2019il adviendra de WordPress tr\u00e8s bient\u00f4t.","language":"fr"}],"projects":[{"url":"https:\/\/github.com\/vimeo\/psalm","name":"vimeo\/psalm","description":"A static analysis tool for finding errors in PHP applications"},{"url":"https:\/\/github.com\/prettier\/plugin-php","name":"prettier\/plugin-php","description":"Prettier PHP Plugin"},{"url":"https:\/\/github.com\/tpunt\/phactor","name":"tpunt\/phactor","description":"An implementation of the Actor model for PHP"}]}
EOF;


        $data = [
            [
                'id' => 1,
                'sending_date' => '2018-04-04 00:00:00',
                'techletter' => $example1,
                'sent_to_mailchimp' => 1,
            ],
            [
                'id' => 2,
                'sending_date' => '2018-04-18 00:00:00',
                'techletter' => $example2,
                'sent_to_mailchimp' => 0,
            ],
        ];

        $table
            ->insert($data)
            ->save()
        ;
    }
}
