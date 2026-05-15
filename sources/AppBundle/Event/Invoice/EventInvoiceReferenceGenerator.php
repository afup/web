<?php

declare(strict_types=1);

namespace AppBundle\Event\Invoice;

use Symfony\Component\String\Slugger\AsciiSlugger;

class EventInvoiceReferenceGenerator
{
    public function generate(int $forumId, string $label): string
    {
        $slugger = new AsciiSlugger();
        $label = preg_replace('/[^A-Z0-9_\-\:\.;]/', '', strtoupper($slugger->slug($label)->toString()));

        return 'F' . date('Y') . sprintf('%02d', $forumId) . '-' . date('dm') . '-' . substr((string) $label, 0, 5) . '-' . substr(md5(date('r') . $label), -5);
    }
}
