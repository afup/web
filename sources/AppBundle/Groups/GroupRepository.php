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
        $email = $this->cleanEmail($email);
        try {
            $this->directory->members->get($mailing, $email);
        } catch (\Google_Service_Exception $exception) {
            return false;
        }
        return true;
    }

    public function addMember($mailing, $email)
    {
        $email = $this->cleanEmail($email);
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
        $email = $this->cleanEmail($email);
        try {
            return $this->directory->members->delete($mailing, $email);
        } catch (\Google_Service_Exception $exception) {
            return false;
        }
    }

    /**
     * @param $mailing
     * @return \Google_Service_Directory_Member[]
     */
    public function getMembers($mailing)
    {
        $members = [];
        $nextPage = [];
        do {
            $list = $this->directory->members->listMembers($mailing, $nextPage);
            if ($list->getNextPageToken() !== null) {
                $nextPage = ['pageToken' => $list->getNextPageToken()];
            } else {
                $nextPage = [];
            }

            $members = array_merge($members, $list->getMembers());
        } while ($nextPage !== []);

        return $members;
    }

    public function cleanEmail($email)
    {
        // Google groups does not supports disposable emails
        return preg_replace('/(.*)\+.*(@.*)/', '$1$2', $email);
    }
}
