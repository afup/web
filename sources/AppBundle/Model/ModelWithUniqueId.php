<?php

declare(strict_types=1);

namespace AppBundle\Model;

interface ModelWithUniqueId
{
    public function getUniqueId(): ?string;
}
