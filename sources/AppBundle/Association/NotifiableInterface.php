<?php

declare(strict_types=1);


namespace AppBundle\Association;

interface NotifiableInterface
{
    public function getId();
    public function getEmail();
}
