<?php

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Speaker;

class SpeakerFormDataFactory
{
    public function fromSpeaker(Speaker $speaker)
    {
        $data = new SpeakerFormData();
        $data->civility = $speaker->getCivility();
        $data->firstname = $speaker->getFirstname();
        $data->lastname = $speaker->getLastname();
        $data->email = $speaker->getEmail();
        $data->company = $speaker->getCompany();
        $data->locality = $speaker->getLocality();
        $data->biography = $speaker->getBiography();
        $data->twitter = $speaker->getTwitter();
        $data->githubUser = $speaker->getUser();
        $data->phoneNumber = $speaker->getPhoneNumber();
        $data->referentPerson = $speaker->getReferentPerson();
        $data->referentPersonEmail = $speaker->getReferentPersonEmail();


        return $data;
    }
}
