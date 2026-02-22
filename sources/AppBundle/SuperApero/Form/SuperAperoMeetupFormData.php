<?php

declare(strict_types=1);

namespace AppBundle\SuperApero\Form;

use AppBundle\SuperApero\Entity\SuperAperoMeetup;

final class SuperAperoMeetupFormData
{
    public ?int $meetupId = null;
    public ?string $description = null;

    public static function fromEntity(SuperAperoMeetup $entity): self
    {
        $data = new self();
        $data->meetupId = $entity->meetupId;
        $data->description = $entity->description;

        return $data;
    }

    public function hasValues(): bool
    {
        return $this->meetupId !== null
            || trim((string) $this->description) !== '';
    }
}
