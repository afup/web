<?php

namespace AppBundle\Groups;

class GroupRepository
{
    private $directory;

    public function __construct(\Google_Service_Directory $directory)
    {
        $this->directory = $directory;
    }

    public function hasMember($mailing, $email)
    {
        try {
            $this->directory->members->get($mailing, $email);
        } catch (\Google_Service_Exception $exception) {
            return false;
        }
        return true;
    }

    public function addMember($mailing, $email)
    {
        $member = new \Google_Service_Directory_Member();
        $member->setEmail($email);
        $member->setKind('admin#directory#member');
        $member->setRole('MEMBER');
        try {
            return $this->directory->members->insert($mailing, $member);
        } catch (\Google_Service_Exception $exception) {
            throw $exception;
            return false;
        }
    }

    public function removeMember($mailing, $email)
    {
        try {
            return $this->directory->members->delete($mailing, $email);
        } catch (\Google_Service_Exception $exception) {
            return false;
        }
    }

    private function cleanEmail($email)
    {
        // Google groups does not supports disposable emails
        return preg_replace('/(.*)\+.*(@.*)/', '$1$2', $email);
    }
}
