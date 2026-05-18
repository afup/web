<?php

declare(strict_types=1);

namespace AppBundle\Model;

interface HasUniqueId
{
    public function uniqueId(): ?string;
}
