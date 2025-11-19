<?php

declare(strict_types=1);

namespace AppBundle\Site;

enum ArticleContentType: string
{
    case Markdown = 'markdown';
    case Html = 'html';
}
