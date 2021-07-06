<?php

namespace AppBundle\Association\Form;

use AppBundle\Association\Model\CompanyMember;

class CompanyEditFormDataFactory
{
    /** @return CompanyEditFormData */
    public function fromCompany(CompanyMember $companyMember)
    {
        $data = new CompanyEditFormData();
        $data->firstname = $companyMember->getFirstName();
        $data->lastname = $companyMember->getLastName();
        $data->email = $companyMember->getEmail();
        $data->companyName = $companyMember->getCompanyName();
        $data->siret = $companyMember->getSiret();
        $data->address = $companyMember->getAddress();
        $data->zipcode = $companyMember->getZipCode();
        $data->city = $companyMember->getCity();
        $data->countryId = $companyMember->getCountry();
        $data->phone = $companyMember->getPhone();
        $data->cellphone = $companyMember->getCellphone();
        $data->status = $companyMember->getStatus();
        $data->maxMembers = $companyMember->getMaxMembers();

        return $data;
    }

    public function toCompany(CompanyEditFormData $data, CompanyMember $companyMember)
    {
        $companyMember->setFirstName($data->firstname);
        $companyMember->setLastName($data->lastname);
        $companyMember->setEmail($data->email);
        $companyMember->setCompanyName($data->companyName);
        $companyMember->setSiret($data->siret);
        $companyMember->setAddress($data->address);
        $companyMember->setZipCode($data->zipcode);
        $companyMember->setCity($data->city);
        $companyMember->setCountry($data->countryId);
        $companyMember->setPhone($data->phone);
        $companyMember->setCellphone($data->cellphone);
        $companyMember->setStatus($data->status);
        $companyMember->setMaxMembers($data->maxMembers);
    }
}
