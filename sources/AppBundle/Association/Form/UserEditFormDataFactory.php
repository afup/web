<?php

namespace AppBundle\Association\Form;

use AppBundle\Association\Model\User;

class UserEditFormDataFactory
{
    /**
     * @return UserEditFormData
     */
    public function fromUser(User $user)
    {
        $data = new UserEditFormData();
        $data->civility = $user->getCivility();
        $data->firstname = $user->getFirstName();
        $data->lastname = $user->getLastName();
        $data->companyId = $user->getCompanyId();
        $data->email = $user->getEmail();
        $data->alternateEmail = $user->getAlternateEmail();
        $data->address = $user->getAddress();
        $data->zipcode = $user->getZipCode();
        $data->city = $user->getCity();
        $data->countryId = $user->getCountry();
        $data->phone = $user->getPhone();
        $data->cellphone = $user->getMobilephone();
        $data->level = $user->getLevel();
        $data->directoryLevel = $user->getDirectoryLevel();
        $data->eventLevel = $user->getEventLevel();
        $data->websiteLevel = $user->getWebsiteLevel();
        $data->officeLevel = $user->getOfficeLevel();
        $data->status = $user->getStatus();
        $data->login = $user->getUsername();
        $data->roles = json_encode($user->getRoles());
        $data->needsUpToDateMembership = $user->getNeedsUpToDateMembership();

        return $data;
    }

    public function toUser(UserEditFormData $data, User $user)
    {
        $user->setUsername($data->login);
        $user->setCompanyId((int) $data->companyId);
        $user->setCivility($data->civility);
        $user->setLastName($data->lastname);
        $user->setFirstName($data->firstname);
        $user->setEmail($data->email);
        $user->setAddress($data->address);
        $user->setZipCode($data->zipcode);
        $user->setCity($data->city);
        $user->setCountry($data->countryId);
        $user->setPhone($data->phone);
        $user->setMobilephone($data->cellphone);
        $user->setStatus($data->status);
        $user->setRoles(json_decode($data->roles, true));
        $user->setAlternateEmail($data->alternateEmail);
        $user->setNeedsUpToDateMembership($data->needsUpToDateMembership);
        $user->setLevel($data->level);
        $user->setDirectoryLevel($data->directoryLevel);
        $user->setWebsiteLevel($data->websiteLevel);
        $user->setEventLevel($data->eventLevel);
        $user->setOfficeLevel($data->officeLevel);
        if (null !== $data->password) {
            $user->setPlainPassword($data->password);
        }
    }

    public function fromSession(array $sessionData)
    {
        $data = new UserEditFormData();
        $data->civility = $sessionData['civilite'];
        $data->lastname = $sessionData['nom'];
        $data->firstname = $sessionData['prenom'];
        $data->email = $sessionData['email'];
        $data->address = $sessionData['adresse'];
        $data->zipcode = $sessionData['code_postal'];
        $data->city = $sessionData['ville'];
        $data->countryId = $sessionData['id_pays'];
        $data->phone = $sessionData['telephone_fixe'];
        $data->cellphone = $sessionData['telephone_portable'];
        $data->status = $sessionData['etat'];

        return $data;
    }
}
