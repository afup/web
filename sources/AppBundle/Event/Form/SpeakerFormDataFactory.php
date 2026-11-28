<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Entity\Speaker;

class SpeakerFormDataFactory
{
    public function fromSpeaker(Speaker $speaker): SpeakerFormData
    {
        $data = new SpeakerFormData();
        $data->genre = $speaker->genre;
        $data->firstname = $speaker->firstname;
        $data->lastname = $speaker->lastname;
        $data->email = $speaker->email;
        $data->company = $speaker->company;
        $data->locality = $speaker->locality;
        $data->biography = $speaker->biography;
        $data->twitter = (string) $speaker->twitter;
        $data->mastodon = $speaker->mastodon;
        $data->linkedin = $speaker->linkedin;
        $data->bluesky = $speaker->bluesky;
        $data->githubUser = $speaker->githubUserId;
        $data->phoneNumber = $speaker->phoneNumber;
        $data->referentPerson = $speaker->referentPerson;
        $data->referentPersonEmail = $speaker->referentPersonEmail;

        return $data;
    }
}
