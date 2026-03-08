<?php

declare(strict_types=1);

namespace AppBundle\Site\Enum;

enum ArticleContentType: string
{
    case Html = 'html';
    case Markdown = 'markdown';
}
