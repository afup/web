<?php

namespace AppBundle\Event\Ticket;

class Purchase
{
    /**
     * @var bool
     */
    private $companyCitation = true;

    /**
     * @var bool
     */
    private $newsletterAfup = false;



    /**
     * @return bool
     */
    public function getCompanyCitation()
    {
        return $this->companyCitation;
    }

    /**
     * @param bool $companyCitation
     * @return Purchase
     */
    public function setCompanyCitation($companyCitation)
    {
        $this->companyCitation = $companyCitation;
        return $this;
    }

    /**
     * @return bool
     */
    public function getNewsletterAfup()
    {
        return $this->newsletterAfup;
    }

    /**
     * @param bool $newsletterAfup
     * @return Purchase
     */
    public function setNewsletterAfup($newsletterAfup)
    {
        $this->newsletterAfup = $newsletterAfup;
        return $this;
    }
}
